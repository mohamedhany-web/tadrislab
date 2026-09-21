<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

/**
 * Product-facing role layer over Glottical runtime roles.
 * Maps Brief V3 identity → existing user.role without a full RBAC rewrite.
 */
final class TadrisRoles
{
    public const PLATFORM_ADMIN = 'platform_admin';

    public const INDIVIDUAL_USER = 'individual_user';

    public const COACH = 'coach'; // مدرب — runtime instructor|teacher

    public const INSTITUTION_ADMIN = 'institution_admin';

    public const TEACHER_PARTICIPANT = 'teacher_participant';

    /**
     * @return self::PLATFORM_ADMIN|self::INDIVIDUAL_USER|self::COACH|self::INSTITUTION_ADMIN|string
     */
    public static function productRole(?User $user): string
    {
        if (! $user) {
            return self::INDIVIDUAL_USER;
        }

        if ($user->isAdmin() || in_array((string) $user->role, ['super_admin', 'admin'], true)) {
            return self::PLATFORM_ADMIN;
        }

        if ($user->isEmployee()) {
            return self::PLATFORM_ADMIN;
        }

        if ($user->isInstructor() || $user->isTeacher()
            || in_array(strtolower((string) $user->role), ['instructor', 'teacher'], true)) {
            return self::COACH;
        }

        if (self::isInstitutionCoordinator($user)) {
            return self::INSTITUTION_ADMIN;
        }

        return self::INDIVIDUAL_USER;
    }

    public static function isInstitutionCoordinator(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('institution_members')) {
            return false;
        }

        return \App\Models\InstitutionMember::query()
            ->where('user_id', $user->id)
            ->where('member_role', \App\Models\InstitutionMember::ROLE_COORDINATOR)
            ->where('is_active', true)
            ->exists();
    }

    public static function isPlatformAdmin(?User $user): bool
    {
        return self::productRole($user) === self::PLATFORM_ADMIN;
    }

    public static function isCoach(?User $user): bool
    {
        return self::productRole($user) === self::COACH;
    }

    public static function isTeacherLearner(?User $user): bool
    {
        return self::productRole($user) === self::INDIVIDUAL_USER;
    }

    /** Arabic label for UI. */
    public static function labelAr(?User $user): string
    {
        return match (self::productRole($user)) {
            self::PLATFORM_ADMIN => 'مدير المنصة',
            self::COACH => 'مدرب',
            self::INSTITUTION_ADMIN => 'منسق جهة',
            default => 'معلم',
        };
    }

    public static function labelEn(?User $user): string
    {
        return match (self::productRole($user)) {
            self::PLATFORM_ADMIN => 'Platform admin',
            self::COACH => 'Coach',
            self::INSTITUTION_ADMIN => 'Institution coordinator',
            default => 'Teacher',
        };
    }

    public static function homeRouteName(?User $user): string
    {
        if (! $user) {
            return 'home';
        }

        if ($user->isEmployee() && $user->roles()->exists()) {
            $adminRoute = \App\Support\RbacAdminRouteAccess::firstPostLoginAdminRouteName($user);
            if ($adminRoute) {
                return $adminRoute;
            }

            return 'employee.dashboard';
        }

        return match (self::productRole($user)) {
            self::PLATFORM_ADMIN => 'admin.dashboard',
            self::COACH => $user->canAccessInstructorPanel() ? 'dashboard' : 'public.tutor.apply.profile',
            self::INSTITUTION_ADMIN => 'institution.portal.index',
            default => 'dashboard',
        };
    }

    public static function homeRedirect(?User $user): RedirectResponse
    {
        $name = self::homeRouteName($user);

        return redirect()->route($name);
    }
}
