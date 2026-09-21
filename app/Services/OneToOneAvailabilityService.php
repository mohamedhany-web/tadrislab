<?php

namespace App\Services;

use App\Models\ConsultationRequest;
use App\Models\OneToOneSession;
use App\Models\OneToOneWeeklyAvailability;
use App\Support\AppTimezone;
use App\Support\WeeklyScheduleTime;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class OneToOneAvailabilityService
{
    public static function dayLabels(): array
    {
        return [
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
            7 => 'الأحد',
        ];
    }

    public static function rulesForInstructor(int $instructorId): Collection
    {
        if (! Schema::hasTable('one_to_one_weekly_availability')) {
            return collect();
        }

        return OneToOneWeeklyAvailability::query()
            ->where('instructor_id', $instructorId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * @param  array<int, array{day_of_week:int,start_time:string,end_time:string,slot_duration_minutes?:int}>  $rows
     */
    public static function syncRules(int $instructorId, array $rows): void
    {
        $default = (int) config('private_lessons.lesson_duration_minutes', 50);
        $normalized = WeeklyScheduleTime::normalizeRows($rows, 180, $default);

        OneToOneWeeklyAvailability::query()->where('instructor_id', $instructorId)->delete();

        foreach ($normalized as $row) {
            OneToOneWeeklyAvailability::create([
                'instructor_id' => $instructorId,
                'day_of_week' => $row['day_of_week'],
                'start_time' => $row['start_time'].':00',
                'end_time' => $row['end_time'].':00',
                'slot_duration_minutes' => $row['slot_duration_minutes'],
                'is_active' => true,
            ]);
        }
    }

    /**
     * أضف نوافذ أسبوعية للمعلم دون مسح الجدول الحالي (تسكين الإدارة).
     *
     * @param  array<int, array{day_of_week?:mixed,time?:mixed,start_time?:mixed}>  $weeklySlots
     */
    public static function ensureWindows(int $instructorId, array $weeklySlots, ?int $durationMinutes = null): int
    {
        $durationMinutes = max(30, min(180, $durationMinutes ?: (int) config('private_lessons.lesson_duration_minutes', 50)));
        $created = 0;

        foreach ($weeklySlots as $slot) {
            $day = (int) ($slot['day_of_week'] ?? 0);
            $time = WeeklyScheduleTime::normalize((string) ($slot['time'] ?? $slot['start_time'] ?? ''));
            if ($day < 1 || $day > 7 || $time === null) {
                continue;
            }

            $already = self::rulesForInstructor($instructorId)->contains(function ($rule) use ($day, $time) {
                if ((int) $rule->day_of_week !== $day) {
                    return false;
                }
                $start = is_string($rule->start_time)
                    ? substr($rule->start_time, 0, 5)
                    : $rule->start_time->format('H:i');

                return WeeklyScheduleTime::normalize($start) === $time;
            });
            if ($already) {
                continue;
            }

            $endMins = WeeklyScheduleTime::toMinutes($time) + $durationMinutes;
            $end = $endMins >= (24 * 60)
                ? '00:00'
                : sprintf('%02d:%02d', intdiv($endMins, 60), $endMins % 60);

            OneToOneWeeklyAvailability::create([
                'instructor_id' => $instructorId,
                'day_of_week' => $day,
                'start_time' => $time.':00',
                'end_time' => $end.':00',
                'slot_duration_minutes' => $durationMinutes,
                'is_active' => true,
            ]);
            $created++;
        }

        return $created;
    }

    /**
     * Weekly H:i rules are interpreted in the instructor's timezone (academy default), returned as UTC instants.
     *
     * @return Collection<int, array{starts_at: Carbon, ends_at: Carbon, label: string}>
     */
    public static function availableSlots(
        int $instructorId,
        Carbon $from,
        Carbon $to,
        ?int $durationMinutes = null,
        ?int $excludeSessionId = null
    ): Collection {
        $durationMinutes = $durationMinutes ?? (int) config('private_lessons.lesson_duration_minutes', 50);
        $rules = self::rulesForInstructor($instructorId);
        if ($rules->isEmpty()) {
            return collect();
        }

        $clockTz = AppTimezone::forInstructorId($instructorId);
        $viewerTz = AppTimezone::forUser(auth()->user());
        $slots = collect();
        $cursor = $from->copy()->timezone($clockTz)->startOfDay();
        $endDay = $to->copy()->timezone($clockTz)->endOfDay();

        while ($cursor->lte($endDay)) {
            $dayRules = $rules->where('day_of_week', $cursor->isoWeekday());
            foreach ($dayRules as $rule) {
                $startStr = is_string($rule->start_time) ? substr($rule->start_time, 0, 5) : $rule->start_time->format('H:i');
                $endStr = is_string($rule->end_time) ? substr($rule->end_time, 0, 5) : $rule->end_time->format('H:i');
                $slotDuration = (int) ($rule->slot_duration_minutes ?: $durationMinutes);

                $windowStart = AppTimezone::wallClockToUtc($cursor->toDateString(), $startStr, $clockTz);
                $windowEnd = WeeklyScheduleTime::windowEndUtc($cursor->toDateString(), $startStr, $endStr, $clockTz);
                $slotStart = $windowStart->copy();

                while ($slotStart->copy()->addMinutes($slotDuration)->lte($windowEnd)) {
                    $slotEnd = $slotStart->copy()->addMinutes($slotDuration);
                    if ($slotStart->gte($from) && $slotStart->gt(now()) && $slotEnd->lte($to)) {
                        if (! self::hasConflict($instructorId, $slotStart, $slotEnd, $excludeSessionId)) {
                            $slots->push([
                                'starts_at' => $slotStart->copy()->utc(),
                                'ends_at' => $slotEnd->copy()->utc(),
                                'label' => $slotStart->copy()->timezone($viewerTz)->format('Y-m-d H:i'),
                            ]);
                        }
                    }
                    $slotStart->addMinutes($slotDuration);
                }
            }
            $cursor->addDay();
        }

        return $slots->sortBy(fn ($s) => $s['starts_at']->timestamp)->values();
    }

    public static function isSlotAvailable(
        int $instructorId,
        Carbon $startsAt,
        int $durationMinutes,
        ?int $excludeSessionId = null,
        ?int $excludeConsultationId = null
    ): bool {
        $endsAt = $startsAt->copy()->addMinutes($durationMinutes);

        if ($startsAt->lte(now())) {
            return false;
        }

        if (self::hasConflict($instructorId, $startsAt, $endsAt, $excludeSessionId, $excludeConsultationId)) {
            return false;
        }

        $rules = self::rulesForInstructor($instructorId);
        if ($rules->isEmpty()) {
            return false;
        }

        $clockTz = AppTimezone::forInstructorId($instructorId);
        $localStart = $startsAt->copy()->timezone($clockTz);
        $localEnd = $endsAt->copy()->timezone($clockTz);

        $dayRules = $rules->where('day_of_week', $localStart->isoWeekday());
        foreach ($dayRules as $rule) {
            $startStr = is_string($rule->start_time) ? substr($rule->start_time, 0, 5) : $rule->start_time->format('H:i');
            $endStr = is_string($rule->end_time) ? substr($rule->end_time, 0, 5) : $rule->end_time->format('H:i');
            $windowStart = AppTimezone::wallClockToUtc($localStart->toDateString(), $startStr, $clockTz);
            $windowEnd = WeeklyScheduleTime::windowEndUtc($localStart->toDateString(), $startStr, $endStr, $clockTz);

            if ($startsAt->copy()->utc()->gte($windowStart) && $endsAt->copy()->utc()->lte($windowEnd)) {
                return true;
            }
        }

        return false;
    }

    public static function hasConflict(
        int $instructorId,
        Carbon $startsAt,
        Carbon $endsAt,
        ?int $excludeSessionId = null,
        ?int $excludeConsultationId = null
    ): bool {
        $sessionQuery = OneToOneSession::query()
            ->where('instructor_id', $instructorId)
            ->where('status', OneToOneSession::STATUS_SCHEDULED)
            ->whereNotNull('scheduled_at');

        if ($excludeSessionId) {
            $sessionQuery->where('id', '!=', $excludeSessionId);
        }

        foreach ($sessionQuery->get() as $session) {
            $existingStart = $session->scheduled_at;
            $existingEnd = $existingStart->copy()->addMinutes((int) ($session->duration_minutes ?? 60));
            if ($startsAt->lt($existingEnd) && $endsAt->gt($existingStart)) {
                return true;
            }
        }

        if (Schema::hasTable('consultation_requests')) {
            $consultationsQuery = ConsultationRequest::query()
                ->where('instructor_id', $instructorId)
                ->whereIn('status', [
                    ConsultationRequest::STATUS_SCHEDULED,
                    ConsultationRequest::STATUS_CONFIRMED,
                    ConsultationRequest::STATUS_RESCHEDULED,
                ])
                ->whereNotNull('scheduled_at');

            if ($excludeConsultationId) {
                $consultationsQuery->where('id', '!=', $excludeConsultationId);
            }

            foreach ($consultationsQuery->get() as $consultation) {
                $existingStart = $consultation->scheduled_at;
                $existingEnd = $existingStart->copy()->addMinutes((int) ($consultation->duration_minutes ?? 60));
                if ($startsAt->lt($existingEnd) && $endsAt->gt($existingStart)) {
                    return true;
                }
            }
        }

        return false;
    }
}
