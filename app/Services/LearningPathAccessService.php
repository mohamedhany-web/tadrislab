<?php

namespace App\Services;

use App\Models\LearningPath;
use App\Models\LearningPathLesson;
use App\Models\LearningPathPractice;
use App\Models\Order;
use App\Models\Package;
use App\Models\TeacherPathEnrollment;
use App\Models\TeacherPathProgressItem;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * تفعيل وصول المعلم لمسارات الباقة أو المسار المنفرد + حساب التقدّم.
 */
class LearningPathAccessService
{
    /**
     * @return list<TeacherPathEnrollment>
     */
    public static function activatePackageForUser(
        Package $package,
        User $user,
        ?Order $order = null,
        ?User $activatedBy = null
    ): array {
        if (! Schema::hasTable('teacher_path_enrollments')) {
            return [];
        }

        $package->loadMissing('learningPaths');
        $expiresAt = null;
        if ($package->duration_days) {
            $expiresAt = Carbon::now()->addDays((int) $package->duration_days);
        }

        $created = [];

        DB::transaction(function () use ($package, $user, $order, $activatedBy, $expiresAt, &$created) {
            foreach ($package->learningPaths as $path) {
                $created[] = self::upsertEnrollment(
                    $user,
                    $path,
                    $package->id,
                    $order?->id,
                    $expiresAt,
                    $activatedBy?->id
                );
            }
        });

        return $created;
    }

    public static function activateStandalonePath(
        LearningPath $path,
        User $user,
        ?Order $order = null,
        ?User $activatedBy = null
    ): ?TeacherPathEnrollment {
        if (! Schema::hasTable('teacher_path_enrollments')) {
            return null;
        }

        $expiresAt = null;
        if ($path->access_days) {
            $expiresAt = Carbon::now()->addDays((int) $path->access_days);
        }

        $enrollment = self::upsertEnrollment(
            $user,
            $path,
            null,
            $order?->id,
            $expiresAt,
            $activatedBy?->id
        );

        if ($enrollment) {
            try {
                event(new \App\Events\AccessSubscriptionActivated(
                    $user,
                    'مسار: '.($path->title_ar ?: $path->title_en ?: ('#'.$path->id)),
                    'تم تفعيل الوصول للمسار',
                    $order?->id
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $enrollment;
    }

    public static function userHasAccess(User $user, int $learningPathId): bool
    {
        if (! Schema::hasTable('teacher_path_enrollments')) {
            return false;
        }

        return TeacherPathEnrollment::query()
            ->where('user_id', $user->id)
            ->where('learning_path_id', $learningPathId)
            ->whereIn('status', ['active', 'completed'])
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    public static function markItemComplete(
        User $user,
        LearningPath $path,
        string $itemType,
        int $itemId
    ): TeacherPathProgressItem {
        abort_unless(self::userHasAccess($user, (int) $path->id), 403);
        abort_unless(in_array($itemType, [TeacherPathProgressItem::TYPE_LESSON, TeacherPathProgressItem::TYPE_PRACTICE], true), 422);

        if ($itemType === TeacherPathProgressItem::TYPE_LESSON) {
            $exists = LearningPathLesson::query()
                ->where('id', $itemId)
                ->whereHas('unit', fn ($q) => $q->where('learning_path_id', $path->id))
                ->exists();
        } else {
            $exists = LearningPathPractice::query()
                ->where('id', $itemId)
                ->whereHas('unit', fn ($q) => $q->where('learning_path_id', $path->id))
                ->exists();
        }
        abort_unless($exists, 404);

        $item = TeacherPathProgressItem::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'item_type' => $itemType,
                'item_id' => $itemId,
            ],
            [
                'learning_path_id' => $path->id,
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        self::recalculateEnrollmentProgress($user, $path);

        return $item;
    }

    public static function recalculateEnrollmentProgress(User $user, LearningPath $path): void
    {
        if (! Schema::hasTable('teacher_path_enrollments')) {
            return;
        }

        $path->loadMissing(['units.lessons', 'units.practices']);
        $total = 0;
        $lessonIds = [];
        $practiceIds = [];
        foreach ($path->units as $unit) {
            foreach ($unit->lessons as $lesson) {
                if ($lesson->is_active) {
                    $total++;
                    $lessonIds[] = (int) $lesson->id;
                }
            }
            foreach ($unit->practices as $practice) {
                if ($practice->is_active) {
                    $total++;
                    $practiceIds[] = (int) $practice->id;
                }
            }
        }

        $done = 0;
        if (Schema::hasTable('teacher_path_progress_items') && $total > 0) {
            $doneLessons = TeacherPathProgressItem::query()
                ->where('user_id', $user->id)
                ->where('learning_path_id', $path->id)
                ->where('item_type', TeacherPathProgressItem::TYPE_LESSON)
                ->whereIn('item_id', $lessonIds ?: [0])
                ->where('is_completed', true)
                ->count();
            $donePractices = TeacherPathProgressItem::query()
                ->where('user_id', $user->id)
                ->where('learning_path_id', $path->id)
                ->where('item_type', TeacherPathProgressItem::TYPE_PRACTICE)
                ->whereIn('item_id', $practiceIds ?: [0])
                ->where('is_completed', true)
                ->count();
            $done = $doneLessons + $donePractices;
        }

        $percent = $total > 0 ? round(($done / $total) * 100, 2) : 0.0;
        $completed = $total > 0 && $done >= $total;

        TeacherPathEnrollment::query()
            ->where('user_id', $user->id)
            ->where('learning_path_id', $path->id)
            ->update([
                'progress' => $percent,
                'status' => $completed ? 'completed' : 'active',
                'completed_at' => $completed ? now() : null,
            ]);
    }

    private static function upsertEnrollment(
        User $user,
        LearningPath $path,
        ?int $packageId,
        ?int $orderId,
        ?Carbon $expiresAt,
        ?int $activatedById
    ): TeacherPathEnrollment {
        return TeacherPathEnrollment::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'learning_path_id' => $path->id,
            ],
            [
                'status' => 'active',
                'package_id' => $packageId,
                'order_id' => $orderId,
                'enrolled_at' => now(),
                'expires_at' => $expiresAt,
                'activated_by' => $activatedById,
                'completed_at' => null,
            ]
        );
    }
}
