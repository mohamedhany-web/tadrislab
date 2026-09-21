<?php

namespace App\Services;

use App\Models\Package;
use App\Models\TeacherPathEnrollment;
use App\Models\TeacherTool;
use App\Models\User;
use App\Models\UserPackageEntitlement;
use Illuminate\Support\Facades\Schema;

/**
 * Access rules for Tools & Resources catalogue items.
 */
class TeacherToolAccessService
{
    public static function canAccess(?User $user, TeacherTool $tool): bool
    {
        if (! $tool->is_active || ! $tool->is_published) {
            return false;
        }

        return match ($tool->access_mode) {
            TeacherTool::ACCESS_FREE => true,
            TeacherTool::ACCESS_LOGIN => $user !== null,
            TeacherTool::ACCESS_PACKAGE => $user !== null && self::userHasPackageAccess($user, $tool),
            TeacherTool::ACCESS_PATH => $user !== null && self::userHasPathAccess($user, $tool),
            default => $user !== null,
        };
    }

    public static function userHasPackageAccess(User $user, TeacherTool $tool): bool
    {
        $packageIds = $tool->packages()->pluck('packages.id');

        if (Schema::hasTable('user_package_entitlements')) {
            // Any active entitlement that includes tools unlocks package-gated catalogue items
            // when the tool is linked to that package — or when includes_tools is set broadly.
            $entitlementQuery = UserPackageEntitlement::query()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                });

            if ($packageIds->isNotEmpty()) {
                if ((clone $entitlementQuery)->whereIn('package_id', $packageIds)->exists()) {
                    return true;
                }
            }

            if ((clone $entitlementQuery)->where('includes_tools', true)->exists()) {
                return true;
            }
        }

        if ($packageIds->isEmpty()) {
            return false;
        }

        // Fallback: active package with includes_tools that user somehow holds via path enrollments from that package.
        return Package::query()
            ->whereIn('id', $packageIds)
            ->where('includes_tools', true)
            ->whereHas('learningPaths', function ($q) use ($user) {
                $q->whereHas('enrollments', function ($eq) use ($user) {
                    $eq->where('user_id', $user->id)->whereIn('status', ['active', 'completed']);
                });
            })
            ->exists();
    }

    public static function userHasPathAccess(User $user, TeacherTool $tool): bool
    {
        $pathIds = $tool->learningPaths()->pluck('learning_paths.id');
        if ($pathIds->isEmpty()) {
            return true;
        }

        if (! Schema::hasTable('teacher_path_enrollments')) {
            return false;
        }

        return TeacherPathEnrollment::query()
            ->where('user_id', $user->id)
            ->whereIn('learning_path_id', $pathIds)
            ->where('status', 'active')
            ->exists();
    }
}
