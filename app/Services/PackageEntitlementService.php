<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use App\Models\UserPackageEntitlement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * تفعيل باقة تدريس لاب: مسارات + جلسات استشارة + أدوات/مقاعد.
 */
class PackageEntitlementService
{
    /**
     * @return array{entitlement: ?UserPackageEntitlement, path_enrollments: list}
     */
    public static function activateForUser(
        Package $package,
        User $user,
        ?Order $order = null,
        ?User $activatedBy = null
    ): array {
        $expiresAt = null;
        if ($package->duration_days) {
            $expiresAt = Carbon::now()->addDays((int) $package->duration_days);
        }

        $entitlement = null;

        DB::transaction(function () use ($package, $user, $order, $activatedBy, $expiresAt, &$entitlement) {
            if (Schema::hasTable('user_package_entitlements')) {
                $sessions = (int) ($package->consultation_sessions ?? 0);
                $includesTools = (bool) $package->includes_tools
                    || (method_exists($package, 'teacherTools') && $package->teacherTools()->exists());

                $entitlement = UserPackageEntitlement::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'package_id' => $package->id,
                    ],
                    [
                        'order_id' => $order?->id,
                        'activated_by' => $activatedBy?->id,
                        'consultation_sessions_total' => $sessions,
                        'consultation_sessions_remaining' => $sessions,
                        'participant_seats' => $package->participant_seats,
                        'includes_tools' => $includesTools,
                        'tools_resources' => $package->tools_resources,
                        'status' => 'active',
                        'activated_at' => now(),
                        'expires_at' => $expiresAt,
                    ]
                );
            }
        });

        $pathEnrollments = LearningPathAccessService::activatePackageForUser(
            $package,
            $user,
            $order,
            $activatedBy
        );

        try {
            event(new \App\Events\AccessSubscriptionActivated(
                $user,
                'باقة: '.($package->name ?: ('#'.$package->id)),
                count($pathEnrollments).' مسار · '.
                ((int) ($package->consultation_sessions ?? 0)).' جلسة استشارة',
                $order?->id
            ));
        } catch (\Throwable $e) {
            report($e);
        }

        return [
            'entitlement' => $entitlement,
            'path_enrollments' => $pathEnrollments,
        ];
    }

    /**
     * خصم جلسة استشارة من أحدث باقة نشطة لديها رصيد.
     */
    public static function consumeConsultationSessionForUser(User $user): ?UserPackageEntitlement
    {
        if (! Schema::hasTable('user_package_entitlements')) {
            return null;
        }

        $entitlement = UserPackageEntitlement::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('consultation_sessions_remaining', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->orderByDesc('activated_at')
            ->first();

        if (! $entitlement || ! $entitlement->consumeConsultationSession()) {
            return null;
        }

        return $entitlement->fresh();
    }

    public static function remainingConsultationSessions(User $user): int
    {
        if (! Schema::hasTable('user_package_entitlements')) {
            return 0;
        }

        return (int) UserPackageEntitlement::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->sum('consultation_sessions_remaining');
    }

    /**
     * استرجاع جلسة استشارة عند إلغاء حجز دُفع من رصيد الباقة.
     */
    public static function restoreConsultationSessionFromReference(?string $paymentReference): bool
    {
        if (! $paymentReference || ! Schema::hasTable('user_package_entitlements')) {
            return false;
        }

        if (! preg_match('/^package_entitlement:(\d+)$/', trim($paymentReference), $m)) {
            return false;
        }

        $entitlement = UserPackageEntitlement::query()->find((int) $m[1]);
        if (! $entitlement) {
            return false;
        }

        return $entitlement->restoreConsultationSession();
    }
}
