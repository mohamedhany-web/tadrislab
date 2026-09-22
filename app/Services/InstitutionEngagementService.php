<?php

namespace App\Services;

use App\Models\Institution;
use App\Models\InstitutionMember;
use App\Models\InstitutionProgram;
use App\Models\InstitutionProgramParticipant;

/**
 * قواعد مساري التعاقد: مباشر (مدرب) vs منصة (مقاعد + منسق).
 */
class InstitutionEngagementService
{
    public const MODE_DIRECT = 'direct_delivery';

    public const MODE_PLATFORM = 'platform_access';

    public static function modes(): array
    {
        $cfg = config('platform.schools_institutions.engagement_modes', []);

        return [
            self::MODE_DIRECT => $cfg[self::MODE_DIRECT]['label_ar'] ?? 'تعاقد مباشر (تنفيذ عبر مدرب)',
            self::MODE_PLATFORM => $cfg[self::MODE_PLATFORM]['label_ar'] ?? 'تعاقد منصة (مقاعد + متابعة)',
        ];
    }

    public static function modeLabel(string $mode, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $cfg = config("platform.schools_institutions.engagement_modes.{$mode}", []);
        if ($locale === 'en') {
            return (string) ($cfg['label_en'] ?? $cfg['label_ar'] ?? $mode);
        }

        return (string) ($cfg['label_ar'] ?? $cfg['label_en'] ?? $mode);
    }

    public static function resolveMode(InstitutionProgram $program): string
    {
        if (filled($program->engagement_mode)) {
            return (string) $program->engagement_mode;
        }

        $org = $program->relationLoaded('institution')
            ? $program->institution
            : $program->institution()->first();

        $fallback = $org?->default_engagement_mode;

        return in_array($fallback, [self::MODE_DIRECT, self::MODE_PLATFORM], true)
            ? $fallback
            : self::MODE_PLATFORM;
    }

    public static function isPlatformAccess(InstitutionProgram $program): bool
    {
        return self::resolveMode($program) === self::MODE_PLATFORM;
    }

    public static function isDirectDelivery(InstitutionProgram $program): bool
    {
        return self::resolveMode($program) === self::MODE_DIRECT;
    }

    /**
     * حد المقاعد لبرنامج منصة: seat_limit البرنامج → الجهة → planned_participants.
     */
    public static function seatCap(InstitutionProgram $program): ?int
    {
        if ($program->seat_limit !== null) {
            return max(0, (int) $program->seat_limit);
        }

        $org = $program->relationLoaded('institution')
            ? $program->institution
            : $program->institution()->first();

        if ($org && $org->seat_limit !== null) {
            return max(0, (int) $org->seat_limit);
        }

        if ($program->planned_participants !== null) {
            return max(0, (int) $program->planned_participants);
        }

        return null;
    }

    public static function seatsUsed(InstitutionProgram $program): int
    {
        return (int) $program->participants()
            ->whereNotIn('status', ['withdrawn'])
            ->count();
    }

    public static function seatsRemaining(InstitutionProgram $program): ?int
    {
        $cap = self::seatCap($program);
        if ($cap === null) {
            return null;
        }

        return max(0, $cap - self::seatsUsed($program));
    }

    public static function canEnrollMore(InstitutionProgram $program): bool
    {
        if (! self::isPlatformAccess($program)) {
            return false;
        }

        $remaining = self::seatsRemaining($program);

        return $remaining === null || $remaining > 0;
    }

    /**
     * مقاعد الجهة على مستوى الأعضاء المشاركين (تعاقد منصة).
     */
    public static function orgSeatCap(Institution $institution): ?int
    {
        return $institution->seat_limit !== null ? max(0, (int) $institution->seat_limit) : null;
    }

    public static function orgSeatsUsed(Institution $institution): int
    {
        return (int) $institution->participants()->where('is_active', true)->count();
    }

    public static function canAddOrgParticipant(Institution $institution): bool
    {
        if (($institution->default_engagement_mode ?? self::MODE_PLATFORM) === self::MODE_DIRECT) {
            // الجهة المباشرة قد تضيف منسقين فقط؛ المشاركون اختياريون بلا سقف منصة
            return true;
        }

        $cap = self::orgSeatCap($institution);
        if ($cap === null) {
            return true;
        }

        return self::orgSeatsUsed($institution) < $cap;
    }

    /**
     * ملخص تقرير منسق لتعاقد المنصة.
     *
     * @return array{programs: int, participants: int, completed: int, avg_progress: int, rows: list<array>}
     */
    public static function coordinatorReport(Institution $institution): array
    {
        $programs = $institution->programs()
            ->with(['participants', 'instructor:id,name'])
            ->orderByDesc('id')
            ->get();

        $rows = [];
        $progressSum = 0;
        $progressN = 0;
        $participantTotal = 0;
        $completed = 0;

        foreach ($programs as $program) {
            $mode = self::resolveMode($program);
            foreach ($program->participants as $p) {
                $participantTotal++;
                if ($p->status === 'completed' || (int) $p->progress_percent >= 100) {
                    $completed++;
                }
                $progressSum += (int) $p->progress_percent;
                $progressN++;
                $rows[] = [
                    'program' => $program->title(),
                    'program_id' => $program->id,
                    'mode' => $mode,
                    'mode_label' => self::modeLabel($mode),
                    'name' => $p->name,
                    'email' => $p->email,
                    'status' => $p->status,
                    'progress' => (int) $p->progress_percent,
                    'coach' => $program->instructor?->name,
                ];
            }

            if ($mode === self::MODE_DIRECT && $program->participants->isEmpty()) {
                $rows[] = [
                    'program' => $program->title(),
                    'program_id' => $program->id,
                    'mode' => $mode,
                    'mode_label' => self::modeLabel($mode),
                    'name' => '— تنفيذ عبر المدرب —',
                    'email' => null,
                    'status' => $program->status,
                    'progress' => (int) $program->progress_percent,
                    'coach' => $program->instructor?->name,
                ];
            }
        }

        return [
            'programs' => $programs->count(),
            'participants' => $participantTotal,
            'completed' => $completed,
            'avg_progress' => $progressN > 0 ? (int) round($progressSum / $progressN) : 0,
            'rows' => $rows,
            'seat_cap' => self::orgSeatCap($institution),
            'seats_used' => self::orgSeatsUsed($institution),
        ];
    }
}
