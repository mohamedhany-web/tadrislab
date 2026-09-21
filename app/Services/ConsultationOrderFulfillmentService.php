<?php

namespace App\Services;

use App\Models\ConsultationRequest;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Mark consultation booking as paid after Order approval (online gateway or admin bank review).
 * Scheduling remains a separate admin step (Confirmed).
 */
class ConsultationOrderFulfillmentService
{
    public static function isConsultationOrder(Order $order): bool
    {
        return $order->order_type === Order::TYPE_CONSULTATION
            || filled(data_get($order->custom_package_data, 'consultation_request_id'));
    }

    public static function fulfill(Order $order, ?User $actor = null): void
    {
        if (! self::isConsultationOrder($order)) {
            return;
        }

        $consultation = self::resolveConsultation($order);
        if (! $consultation) {
            Log::warning('Consultation fulfill: booking not found for order', ['order_id' => $order->id]);

            return;
        }

        $alreadyPaid = $consultation->paid_confirmed_at !== null;
        $awaitingStatuses = [
            ConsultationRequest::STATUS_NEW,
            ConsultationRequest::STATUS_PENDING,
            ConsultationRequest::STATUS_PAYMENT_REPORTED,
            ConsultationRequest::STATUS_AWAITING_VERIFICATION,
            ConsultationRequest::STATUS_PAID,
        ];

        $payload = [
            'order_id' => $order->id,
            'payment_reported_at' => $consultation->payment_reported_at ?? now(),
            'paid_confirmed_at' => $consultation->paid_confirmed_at ?? now(),
            'paid_confirmed_by' => $consultation->paid_confirmed_by ?? $actor?->id,
        ];

        if (in_array($consultation->status, $awaitingStatuses, true)
            && ! in_array($consultation->status, [
                ConsultationRequest::STATUS_CONFIRMED,
                ConsultationRequest::STATUS_RESCHEDULED,
                ConsultationRequest::STATUS_COMPLETED,
                ConsultationRequest::STATUS_CANCELLED,
            ], true)) {
            // Signal: paid, waiting admin to Confirm + schedule.
            $payload['status'] = ConsultationRequest::STATUS_PAID;
        }

        $method = (string) ($order->payment_method ?: $consultation->payment_method ?: 'online');
        if ($method !== '') {
            $payload['payment_method'] = $method;
        }

        $consultation->update($payload);

        if ($alreadyPaid) {
            return;
        }

        try {
            Notification::create([
                'user_id' => $consultation->student_id,
                'sender_id' => $actor?->id,
                'title' => 'تم تأكيد دفع الاستشارة',
                'message' => 'استلمنا دفع حجز الاستشارة #'.$consultation->id.'. فريق المنصة سيؤكّد الموعد قريبًا ويصلك التفاصيل عبر واتساب والبريد.',
                'type' => 'general',
                'priority' => 'normal',
                'audience' => 'student',
                'action_url' => route('consultations.show', $consultation),
                'action_text' => 'تفاصيل الحجز',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Consultation fulfill notification failed', [
                'consultation_id' => $consultation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public static function resolveConsultation(Order $order): ?ConsultationRequest
    {
        $id = (int) data_get($order->custom_package_data, 'consultation_request_id', 0);
        if ($id > 0) {
            $found = ConsultationRequest::query()->find($id);
            if ($found) {
                return $found;
            }
        }

        return ConsultationRequest::query()->where('order_id', $order->id)->first();
    }
}
