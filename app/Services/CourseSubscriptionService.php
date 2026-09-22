<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\Order;
use App\Models\Package;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use App\Services\InstructorCoursePercentageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

/**
 * اشتراك شهري في الكورسات (جماعي أو 1:1 مع معلم).
 */
class CourseSubscriptionService
{
    public const BILLING_ONE_TIME = 'one_time';

    public const BILLING_MONTHLY = 'monthly';

    public const DELIVERY_GROUP = 'group';

    public const DELIVERY_ONE_TO_ONE = 'one_to_one';

    public static function billingModeLabels(): array
    {
        return [
            self::BILLING_ONE_TIME => 'دفعة واحدة',
            self::BILLING_MONTHLY => 'اشتراك شهري',
        ];
    }

    public static function deliveryTypeLabels(): array
    {
        return [
            self::DELIVERY_GROUP => 'كورس جماعي',
            self::DELIVERY_ONE_TO_ONE => 'كورس فردي (1:1)',
        ];
    }

    public static function subscriptionEndDate(?Carbon $from = null): Carbon
    {
        return ($from ?? now())->copy()->addMonth();
    }

    /**
     * تفعيل أو تجديد اشتراك شهري بعد الدفع.
     */
    public static function activateMonthlyEnrollment(
        StudentCourseEnrollment $enrollment,
        AdvancedCourse $course,
        bool $autoRenew = false
    ): void {
        $endsAt = self::subscriptionEndDate(
            $enrollment->expires_at && $enrollment->expires_at->isFuture()
                ? $enrollment->expires_at
                : now()
        );

        $enrollment->update([
            'status' => 'active',
            'enrollment_type' => 'subscription',
            'access_type' => 'subscription',
            'expires_at' => $endsAt,
            'auto_renew' => $autoRenew,
            'activated_at' => now(),
        ]);
    }

    /**
     * تفعيل شراء لمرة واحدة (وصول دائم).
     */
    public static function activateLifetimeEnrollment(StudentCourseEnrollment $enrollment): void
    {
        $enrollment->update([
            'status' => 'active',
            'enrollment_type' => 'purchase',
            'access_type' => 'lifetime',
            'expires_at' => null,
            'auto_renew' => false,
            'activated_at' => now(),
        ]);
    }

    /**
     * تفعيل كورسات مربوطة بباقة (مدة الباقة أو وصول دائم إن لم تُحدد مدة).
     *
     * @return list<StudentCourseEnrollment>
     */
    public static function activatePackageCoursesForUser(
        Package $package,
        User $user,
        ?Order $order = null,
        ?User $activatedBy = null
    ): array {
        if (! Schema::hasTable('student_course_enrollments') || ! Schema::hasTable('package_course')) {
            return [];
        }

        $package->loadMissing('courses');
        if ($package->courses->isEmpty()) {
            return [];
        }

        $expiresAt = null;
        if ($package->duration_days) {
            $expiresAt = Carbon::now()->addDays((int) $package->duration_days);
        }

        $created = [];

        foreach ($package->courses as $course) {
            $enrollment = StudentCourseEnrollment::query()->firstOrNew([
                'user_id' => $user->id,
                'advanced_course_id' => $course->id,
            ]);

            $enrollment->fill([
                'status' => 'active',
                'enrollment_type' => 'package',
                'access_type' => $expiresAt ? 'limited' : 'lifetime',
                'expires_at' => $expiresAt,
                'auto_renew' => false,
                'activated_at' => now(),
                'activated_by' => $activatedBy?->id ?? $user->id,
                'enrolled_at' => $enrollment->enrolled_at ?? now(),
                'progress' => $enrollment->progress ?? 0,
                'final_price' => $order?->amount,
                'payment_method' => $order ? 'package' : 'admin',
            ]);
            $enrollment->save();

            try {
                InstructorCoursePercentageService::processEnrollmentActivation($enrollment->fresh());
            } catch (\Throwable $e) {
                report($e);
            }

            $created[] = $enrollment->fresh();
        }

        return $created;
    }

    /**
     * هل التسجيل نشط وصالح (يشمل انتهاء الاشتراك الشهري)؟
     */
    public static function enrollmentGrantsAccess(?StudentCourseEnrollment $enrollment): bool
    {
        if (! $enrollment || $enrollment->status !== 'active') {
            return false;
        }

        if ($enrollment->access_type === 'subscription' || $enrollment->enrollment_type === 'subscription') {
            return $enrollment->expires_at === null || $enrollment->expires_at->isFuture();
        }

        if ($enrollment->access_type === 'limited' && $enrollment->expires_at) {
            return $enrollment->expires_at->isFuture();
        }

        return true;
    }

    /**
     * نمط الفوترة من الطلب أو من إعدادات الكورس.
     */
    public static function resolveBillingMode(Order $order, ?AdvancedCourse $course): string
    {
        $fromOrder = (string) ($order->billing_mode ?? '');
        if (in_array($fromOrder, [self::BILLING_ONE_TIME, self::BILLING_MONTHLY], true)) {
            return $fromOrder;
        }

        return $course && $course->isMonthlyBilling()
            ? self::BILLING_MONTHLY
            : self::BILLING_ONE_TIME;
    }

    /**
     * إنشاء/تحديث تسجيل الكورس بعد موافقة الطلب أو نجاح الدفع.
     */
    public static function syncEnrollmentFromOrder(
        Order $order,
        ?int $invoiceId = null,
        ?int $paymentId = null,
        string $paymentMethod = 'online',
        ?int $activatedBy = null,
    ): ?StudentCourseEnrollment {
        if (! $order->advanced_course_id) {
            return null;
        }

        $order->loadMissing('course');
        $course = $order->course ?? AdvancedCourse::query()->find($order->advanced_course_id);
        $billingMode = self::resolveBillingMode($order, $course);
        $activatedBy = $activatedBy ?? (int) $order->user_id;

        $enrollment = StudentCourseEnrollment::query()
            ->where('user_id', $order->user_id)
            ->where('advanced_course_id', $order->advanced_course_id)
            ->first();

        $base = [
            'status' => 'active',
            'invoice_id' => $invoiceId,
            'payment_id' => $paymentId,
            'payment_method' => $paymentMethod,
            'final_price' => $order->amount,
            'original_price' => $order->original_amount ?? $order->amount,
            'discount_amount' => $order->discount_amount ?? 0,
            'coupon_id' => $order->coupon_id,
            'activated_by' => $activatedBy,
        ];

        if (! $enrollment) {
            $enrollment = StudentCourseEnrollment::create(array_merge($base, [
                'user_id' => $order->user_id,
                'advanced_course_id' => $order->advanced_course_id,
                'enrolled_at' => now(),
                'progress' => 0,
            ]));
        } else {
            $enrollment->update($base);
            $enrollment = $enrollment->fresh();
        }

        if ($billingMode === self::BILLING_MONTHLY && $course) {
            self::activateMonthlyEnrollment(
                $enrollment,
                $course,
                (bool) ($order->auto_renew ?? false)
            );
        } else {
            self::activateLifetimeEnrollment($enrollment);
        }

        $enrollment = $enrollment->fresh();
        InstructorCoursePercentageService::processEnrollmentActivation($enrollment);

        if ($course && $course->isOneToOne()) {
            OneToOneSessionService::provisionSessionsForEnrollment($enrollment, $course);
        }

        return $enrollment;
    }

    /**
     * إنهاء الاشتراكات المنتهية (يُشغَّل يومياً).
     */
    public static function expireDueEnrollments(): int
    {
        return StudentCourseEnrollment::query()
            ->where('status', 'active')
            ->whereIn('access_type', ['subscription', 'limited'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }
}
