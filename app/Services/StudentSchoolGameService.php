<?php

namespace App\Services;

use App\Models\SchoolMission;
use App\Models\SchoolMissionProgress;
use App\Models\StudentLearningStreak;
use App\Models\StudentXpLedger;
use App\Models\TutoringClassAttendance;
use App\Models\TutoringClassSession;
use App\Models\TutoringCohortEnrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentSchoolGameService
{
    public static function tablesReady(): bool
    {
        return Schema::hasTable('student_xp_ledger')
            && Schema::hasTable('student_learning_streaks')
            && Schema::hasTable('school_missions');
    }

    public static function ensureDefaultMissions(): void
    {
        if (! Schema::hasTable('school_missions')) {
            return;
        }

        $defaults = [
            [
                'code' => 'daily_attend_1',
                'title' => 'جلسة التطوير اليوم',
                'description' => 'ادخل جلسة تطوير واحدة اليوم ضمن مسارك المهني.',
                'cadence' => 'daily',
                'mission_type' => 'attend_session',
                'target_count' => 1,
                'xp_reward' => 100,
                'sort_order' => 1,
            ],
            [
                'code' => 'daily_earn_50',
                'title' => '50 نقطة تطوير اليوم',
                'description' => 'أي إنجاز مهني: حضور جلسة أو إكمال مهمة أو تمرين عملي.',
                'cadence' => 'daily',
                'mission_type' => 'earn_xp',
                'target_count' => 50,
                'xp_reward' => 50,
                'sort_order' => 2,
            ],
            [
                'code' => 'weekly_attend_3',
                'title' => 'هدف الأسبوع: 3 جلسات',
                'description' => 'أكمل 3 جلسات تطوير مهني هذا الأسبوع.',
                'cadence' => 'weekly',
                'mission_type' => 'attend_session',
                'target_count' => 3,
                'xp_reward' => 250,
                'sort_order' => 10,
            ],
            [
                'code' => 'weekly_activity_5',
                'title' => 'هدف الأسبوع: 5 إنجازات',
                'description' => 'أنجز 5 أنشطة مهنية (حضور / مهمة / تقييم ذاتي).',
                'cadence' => 'weekly',
                'mission_type' => 'learning_activity',
                'target_count' => 5,
                'xp_reward' => 300,
                'sort_order' => 11,
            ],
        ];

        foreach ($defaults as $row) {
            SchoolMission::query()->updateOrCreate(
                [
                    'code' => $row['code'],
                    'tutoring_group_cohort_id' => null,
                ],
                array_merge($row, ['is_active' => true])
            );
        }
    }

    public static function totalXp(User $user): int
    {
        if (! Schema::hasTable('student_xp_ledger')) {
            return 0;
        }

        return (int) (StudentXpLedger::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->value('balance_after') ?: 0);
    }

    public static function levelFromXp(int $xp): int
    {
        $per = max(1, (int) config('school_game.xp_per_level', 250));

        return 1 + intdiv(max(0, $xp), $per);
    }

    public static function xpIntoLevel(int $xp): array
    {
        $per = max(1, (int) config('school_game.xp_per_level', 250));
        $level = self::levelFromXp($xp);
        $into = $xp % $per;
        $need = $per - $into;

        return [
            'level' => $level,
            'into' => $into,
            'need' => $need === $per && $into === 0 ? $per : $need,
            'per_level' => $per,
            'percent' => (int) round(($into / $per) * 100),
        ];
    }

    /**
     * @return StudentXpLedger|null null if duplicate / skipped / tables missing
     */
    public static function award(
        User $user,
        int $amount,
        string $reason,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?int $cohortId = null,
        array $metadata = [],
    ): ?StudentXpLedger {
        if ($amount === 0 || ! self::tablesReady()) {
            return null;
        }

        return DB::transaction(function () use ($user, $amount, $reason, $sourceType, $sourceId, $cohortId, $metadata) {
            if ($sourceType && $sourceId) {
                $exists = StudentXpLedger::query()
                    ->where('user_id', $user->id)
                    ->where('reason', $reason)
                    ->where('source_type', $sourceType)
                    ->where('source_id', $sourceId)
                    ->lockForUpdate()
                    ->exists();
                if ($exists) {
                    return null;
                }
            }

            $current = (int) (StudentXpLedger::query()
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->value('balance_after') ?: 0);

            $entry = StudentXpLedger::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'balance_after' => max(0, $current + $amount),
                'reason' => $reason,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'tutoring_group_cohort_id' => $cohortId,
                'metadata' => $metadata ?: null,
            ]);

            if ($amount > 0 && ! in_array($reason, ['mission_reward'], true)) {
                self::bumpStreak($user);
                self::bumpMissions($user, $reason, $amount, $cohortId);
            }

            return $entry;
        });
    }

    public static function awardAttendance(TutoringClassSession $session, User $user, string $status): ?StudentXpLedger
    {
        if (! in_array($status, [
            TutoringClassAttendance::STATUS_PRESENT,
            TutoringClassAttendance::STATUS_LATE,
        ], true)) {
            return null;
        }

        $amount = $status === TutoringClassAttendance::STATUS_LATE
            ? (int) config('school_game.xp.attendance_late', 35)
            : (int) config('school_game.xp.attendance_present', 50);

        return self::award(
            $user,
            $amount,
            'attendance_join',
            TutoringClassAttendance::class,
            // Use session+user composite via source_id = session id (unique with reason+user)
            $session->id,
            (int) $session->tutoring_group_cohort_id,
            ['status' => $status, 'session_id' => $session->id]
        );
    }

    public static function awardAssignmentSubmit(User $user, int $submissionId, ?int $cohortId = null): ?StudentXpLedger
    {
        return self::award(
            $user,
            (int) config('school_game.xp.assignment_submit', 80),
            'assignment_submit',
            'assignment_submission',
            $submissionId,
            $cohortId
        );
    }

    public static function awardExamComplete(User $user, int $attemptId, ?int $cohortId = null): ?StudentXpLedger
    {
        return self::award(
            $user,
            (int) config('school_game.xp.exam_complete', 100),
            'exam_complete',
            'exam_attempt',
            $attemptId,
            $cohortId
        );
    }

    public static function bumpStreak(User $user): StudentLearningStreak
    {
        $today = now(config('app.timezone', 'Africa/Cairo'))->toDateString();

        $streak = StudentLearningStreak::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['current_streak' => 0, 'longest_streak' => 0, 'last_activity_date' => null]
        );

        $last = $streak->last_activity_date?->toDateString();
        if ($last === $today) {
            return $streak;
        }

        $yesterday = Carbon::parse($today)->subDay()->toDateString();
        $current = ($last === $yesterday) ? ((int) $streak->current_streak + 1) : 1;

        $streak->update([
            'current_streak' => $current,
            'longest_streak' => max((int) $streak->longest_streak, $current),
            'last_activity_date' => $today,
        ]);

        return $streak->fresh();
    }

    public static function streakFor(User $user): array
    {
        if (! Schema::hasTable('student_learning_streaks')) {
            return ['current' => 0, 'longest' => 0, 'last_activity_date' => null];
        }

        $row = StudentLearningStreak::query()->where('user_id', $user->id)->first();

        return [
            'current' => (int) ($row?->current_streak ?: 0),
            'longest' => (int) ($row?->longest_streak ?: 0),
            'last_activity_date' => $row?->last_activity_date?->toDateString(),
        ];
    }

    protected static function bumpMissions(User $user, string $reason, int $xpAmount, ?int $cohortId): void
    {
        if (! Schema::hasTable('school_missions') || ! Schema::hasTable('school_mission_progress')) {
            return;
        }

        self::ensureDefaultMissions();

        $missions = SchoolMission::query()
            ->active()
            ->where(function ($q) use ($cohortId) {
                $q->whereNull('tutoring_group_cohort_id');
                if ($cohortId) {
                    $q->orWhere('tutoring_group_cohort_id', $cohortId);
                }
            })
            ->orderBy('sort_order')
            ->get();

        foreach ($missions as $mission) {
            $inc = self::missionIncrement($mission, $reason, $xpAmount);
            if ($inc <= 0) {
                continue;
            }
            self::applyMissionProgress($user, $mission, $inc, $cohortId);
        }
    }

    protected static function missionIncrement(SchoolMission $mission, string $reason, int $xpAmount): int
    {
        return match ($mission->mission_type) {
            'attend_session' => $reason === 'attendance_join' ? 1 : 0,
            'submit_assignment' => $reason === 'assignment_submit' ? 1 : 0,
            'exam_complete' => $reason === 'exam_complete' ? 1 : 0,
            'earn_xp' => $xpAmount > 0 ? $xpAmount : 0,
            'learning_activity' => in_array($reason, [
                'attendance_join', 'assignment_submit', 'exam_complete',
            ], true) ? 1 : 0,
            default => 0,
        };
    }

    protected static function applyMissionProgress(User $user, SchoolMission $mission, int $inc, ?int $cohortId): void
    {
        $periodKey = $mission->cadence === 'weekly'
            ? now()->format('o-\WW')
            : now()->toDateString();

        $row = SchoolMissionProgress::query()->firstOrCreate(
            [
                'school_mission_id' => $mission->id,
                'user_id' => $user->id,
                'period_key' => $periodKey,
            ],
            [
                'progress_count' => 0,
                'status' => 'active',
            ]
        );

        if ($row->status === 'completed') {
            return;
        }

        $newCount = min((int) $mission->target_count, (int) $row->progress_count + $inc);
        $done = $newCount >= (int) $mission->target_count;

        $row->update([
            'progress_count' => $newCount,
            'status' => $done ? 'completed' : 'active',
            'completed_at' => $done ? now() : null,
        ]);

        if ($done && (int) $mission->xp_reward > 0) {
            $periodHash = abs(crc32($mission->id.'|'.$periodKey)) % 2000000000;
            self::award(
                $user,
                (int) $mission->xp_reward,
                'mission_reward',
                'school_mission_period',
                $periodHash,
                $cohortId ?: $mission->tutoring_group_cohort_id,
                ['mission_code' => $mission->code, 'period_key' => $periodKey]
            );
        }
    }

    /**
     * @return Collection<int, object>
     */
    public static function missionsFor(User $user): Collection
    {
        if (! Schema::hasTable('school_missions')) {
            return collect();
        }

        self::ensureDefaultMissions();

        $missions = SchoolMission::query()
            ->active()
            ->whereNull('tutoring_group_cohort_id')
            ->orderBy('sort_order')
            ->get();

        return $missions->map(function (SchoolMission $mission) use ($user) {
            $periodKey = $mission->cadence === 'weekly'
                ? now()->format('o-\WW')
                : now()->toDateString();

            $progress = SchoolMissionProgress::query()
                ->where('school_mission_id', $mission->id)
                ->where('user_id', $user->id)
                ->where('period_key', $periodKey)
                ->first();

            $count = (int) ($progress?->progress_count ?: 0);
            $target = max(1, (int) $mission->target_count);

            return (object) [
                'id' => $mission->id,
                'code' => $mission->code,
                'title' => $mission->title,
                'description' => $mission->description,
                'cadence' => $mission->cadence,
                'target' => $target,
                'progress' => min($target, $count),
                'percent' => (int) round(min(100, ($count / $target) * 100)),
                'status' => $progress?->status ?: 'active',
                'xp_reward' => (int) $mission->xp_reward,
                'completed' => ($progress?->status === 'completed'),
            ];
        });
    }

    /**
     * Class leaderboard by XP earned inside a cohort.
     *
     * @return Collection<int, object>
     */
    public static function cohortLeaderboard(int $cohortId, int $limit = 10): Collection
    {
        if (! Schema::hasTable('student_xp_ledger') || ! Schema::hasTable('tutoring_cohort_enrollments')) {
            return collect();
        }

        $userIds = TutoringCohortEnrollment::query()
            ->where('tutoring_group_cohort_id', $cohortId)
            ->where('status', TutoringCohortEnrollment::STATUS_ACTIVE)
            ->pluck('user_id');

        if ($userIds->isEmpty()) {
            return collect();
        }

        $rows = StudentXpLedger::query()
            ->selectRaw('user_id, SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as xp_total')
            ->where('tutoring_group_cohort_id', $cohortId)
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->orderByDesc('xp_total')
            ->limit($limit)
            ->get();

        $users = User::query()->whereIn('id', $rows->pluck('user_id'))->get(['id', 'name'])->keyBy('id');

        return $rows->values()->map(function ($row, $i) use ($users) {
            return (object) [
                'rank' => $i + 1,
                'user_id' => (int) $row->user_id,
                'name' => $users->get($row->user_id)?->name ?: 'طالب',
                'xp' => (int) $row->xp_total,
            ];
        });
    }

    /**
     * Snapshot for School Home.
     */
    public static function profileSnapshot(User $user): array
    {
        if (! self::tablesReady()) {
            return [
                'xp' => 0,
                'level' => 1,
                'level_meta' => self::xpIntoLevel(0),
                'streak' => ['current' => 0, 'longest' => 0],
                'missions' => collect(),
                'daily_missions' => collect(),
                'weekly_missions' => collect(),
            ];
        }

        $xp = self::totalXp($user);
        $missions = self::missionsFor($user);

        return [
            'xp' => $xp,
            'level' => self::levelFromXp($xp),
            'level_meta' => self::xpIntoLevel($xp),
            'streak' => self::streakFor($user),
            'missions' => $missions,
            'daily_missions' => $missions->where('cadence', 'daily')->values(),
            'weekly_missions' => $missions->where('cadence', 'weekly')->values(),
        ];
    }
}
