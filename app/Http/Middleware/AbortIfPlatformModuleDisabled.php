<?php

namespace App\Http\Middleware;

use App\Support\PlatformModules;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Soft-close surplus / deferred modules by URI prefix.
 * Re-enable by flipping config/platform.php modules.{key}.enabled = true.
 */
class AbortIfPlatformModuleDisabled
{
    /**
     * Longest-prefix wins. Do NOT list KEEP paths (admin/packages, courses, pricing, …).
     *
     * @return array<string, string> pathPrefix => moduleKey
     */
    public static function pathModuleMap(): array
    {
        return [
            // Public surplus
            'free-trial' => 'free_trial',
            'parent-progress' => 'parent_progress',
            'groups' => 'tutoring',
            'service-packages' => 'tutoring',
            'tutor/apply' => 'tutor_hiring',
            'classroom' => 'live_classroom',
            'community' => 'community',

            // Student surplus surfaces
            'student/tutoring' => 'tutoring',
            'student/referrals' => 'loyalty_referrals',
            'referrals' => 'loyalty_referrals',
            'student/classroom' => 'live_classroom',
            'dashboard/tutoring' => 'tutoring',
            'my-school' => 'tutoring',
            'classes' => 'tutoring',
            'tutoring-bookings' => 'tutoring',
            'tutoring-subscriptions' => 'tutoring',
            'private-lectures' => 'tutoring',
            'one-to-one-sessions' => 'tutoring',
            'service-entitlements' => 'tutoring',
            'learn' => 'tutoring',
            'student/live-sessions' => 'live_classroom',
            'live-sessions' => 'live_classroom',
            'live-recordings' => 'live_classroom',

            // Instructor tutoring ops
            'instructor/tutoring' => 'tutoring',
            'instructor/tutoring-bookings' => 'tutoring',
            'instructor/tutoring-cohorts' => 'tutoring',
            'instructor/work-schedule' => 'tutoring',
            'instructor/tutor-work-schedule' => 'tutoring',
            'instructor/one-to-one' => 'tutoring',
            'instructor/withdrawals' => 'legacy_finance',
            'instructor/live-sessions' => 'live_classroom',
            'instructor/live-recordings' => 'live_classroom',

            // Employee CRM / sales desk
            'employee/crm' => 'crm_sales',
            'employee/sales' => 'crm_sales',

            // Admin — free trial / hiring
            'admin/free-trial-bookings' => 'free_trial',
            'admin/tutor-applications' => 'tutor_hiring',
            'admin/hiring-form' => 'tutor_hiring',

            // Admin — tutoring marketplace
            'admin/tutoring-groups' => 'tutoring',
            'admin/tutoring-groups-manage' => 'tutoring',
            'admin/tutoring-group-bookings' => 'tutoring',
            'admin/tutoring-subscriptions' => 'tutoring',
            'admin/one-to-one-sessions' => 'tutoring',
            'admin/tutor-work-schedules' => 'tutoring',
            'admin/service-packages' => 'tutoring',
            'admin/service-package-pricing-rules' => 'tutoring',
            'admin/student-entitlements' => 'tutoring',
            'admin/student-lesson-cleanup' => 'tutoring',
            'admin/students-accounts' => 'tutoring',

            // Admin — placement (language)
            'admin/placement' => 'placement',

            // Admin — CRM
            'admin/crm' => 'crm_sales',
            'admin/sales/leads' => 'crm_sales',

            // Admin — receiving accounts (manual payment) stay with payments; salaries/installments remain legacy
            'admin/wallets' => 'payments',
            'admin/salaries' => 'legacy_finance',
            'admin/installments' => 'legacy_finance',
            'admin/instructor-accounts' => 'legacy_finance',

            // Admin — marketing surplus
            'admin/popup-ads' => 'loyalty_referrals',
            'admin/referral-programs' => 'loyalty_referrals',
            'admin/referrals' => 'loyalty_referrals',
            'admin/coupon-commissions' => 'loyalty_referrals',

            // Admin — live classroom / n8n ops
            'admin/live-sessions' => 'live_classroom',
            'admin/live-servers' => 'live_classroom',
            'admin/live-recordings' => 'live_classroom',
            'admin/classroom-recordings' => 'live_classroom',
            'admin/n8n' => 'live_classroom',

            // API ops tied to live surplus (keep payment webhooks elsewhere)
            'api/n8n' => 'live_classroom',
            'api/live-recordings' => 'live_classroom',
        ];
    }

    public function handle(Request $request, Closure $next): Response
    {
        $path = trim($request->path(), '/');
        if ($path === '') {
            return $next($request);
        }

        $module = $this->resolveModule($path);
        if ($module !== null && PlatformModules::disabled($module)) {
            abort(404);
        }

        return $next($request);
    }

    private function resolveModule(string $path): ?string
    {
        $matchedPrefix = null;
        $matchedModule = null;

        foreach (self::pathModuleMap() as $prefix => $module) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                if ($matchedPrefix === null || strlen($prefix) > strlen($matchedPrefix)) {
                    $matchedPrefix = $prefix;
                    $matchedModule = $module;
                }
            }
        }

        return $matchedModule;
    }
}
