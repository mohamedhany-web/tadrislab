<?php

namespace App\Services;

use App\Models\ConsultationRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * إشعارات تأكيد/تذكير الاستشارات عبر In-app + Email + WhatsApp.
 */
class ConsultationNotificationService
{
    public static function notifyBookingConfirmed(ConsultationRequest $consultation, ?User $actor = null): void
    {
        $consultation->loadMissing(['student', 'instructor', 'service', 'classroomMeeting']);
        $when = $consultation->scheduled_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—';
        $serviceTitle = $consultation->service?->title() ?? 'استشارة';
        $joinUrl = $consultation->classroomMeeting
            ? ClassroomMeetingAccessService::platformEnterUrl($consultation->classroomMeeting)
            : null;

        $learnerMsg = "تم تأكيد حجز استشارتك: {$serviceTitle}\n"
            ."الموعد: {$when}\n"
            .($joinUrl ? "رابط الجلسة: {$joinUrl}\n" : '')
            .'تدريس لاب | TADRIS LAB';

        $instructorMsg = "تم تأكيد استشارة: {$serviceTitle}\n"
            .'المعلم: '.($consultation->student->name ?? '—')."\n"
            ."الموعد: {$when}";

        self::notifyUser(
            $consultation->student,
            'تأكيد حجز الاستشارة',
            $learnerMsg,
            'student',
            route('consultations.show', $consultation),
            $actor?->id
        );

        if ($consultation->instructor) {
            self::notifyUser(
                $consultation->instructor,
                'استشارة مؤكدة',
                $instructorMsg,
                'instructor',
                route('instructor.consultations.show', $consultation),
                $actor?->id
            );
        }

        // Channel fan-out (WhatsApp + Email) via central Notification Layer
        event(new \App\Events\BookingConfirmed($consultation));
    }

    public static function notifyRescheduled(ConsultationRequest $consultation, ?User $actor = null): void
    {
        $consultation->loadMissing(['student', 'instructor', 'service']);
        $when = $consultation->scheduled_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—';
        $msg = 'تم إعادة جدولة الاستشارة إلى: '.$when."\nتدريس لاب";

        if ($consultation->student) {
            self::notifyUser($consultation->student, 'إعادة جدولة الاستشارة', $msg, 'student', route('consultations.show', $consultation), $actor?->id);
            self::sendWhatsAppAndEmail($consultation->student, $msg, 'إعادة جدولة الاستشارة — تدريس لاب');
        }
        if ($consultation->instructor) {
            self::notifyUser($consultation->instructor, 'إعادة جدولة استشارة', $msg, 'instructor', route('instructor.consultations.show', $consultation), $actor?->id);
            self::sendWhatsAppAndEmail($consultation->instructor, $msg, 'إعادة جدولة استشارة — تدريس لاب');
        }
    }

    public static function notifyCancelled(ConsultationRequest $consultation, string $extra = '', ?User $actor = null): void
    {
        $consultation->loadMissing(['student']);
        $msg = 'تم إلغاء طلب الاستشارة.'.($extra ? ' '.$extra : '');
        if ($consultation->student) {
            self::notifyUser($consultation->student, 'إلغاء الاستشارة', $msg, 'student', route('consultations.show', $consultation), $actor?->id);
            self::sendWhatsAppAndEmail($consultation->student, $msg, 'إلغاء الاستشارة — تدريس لاب');
        }
    }

    private static function notifyUser(
        ?User $user,
        string $title,
        string $message,
        string $audience,
        ?string $actionUrl,
        ?int $senderId
    ): void {
        if (! $user) {
            return;
        }

        try {
            Notification::create([
                'user_id' => $user->id,
                'sender_id' => $senderId,
                'title' => $title,
                'message' => $message,
                'type' => 'reminder',
                'priority' => 'high',
                'audience' => $audience,
                'action_url' => $actionUrl,
                'action_text' => 'التفاصيل',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Consultation in-app notify failed', ['error' => $e->getMessage()]);
        }
    }

    private static function sendWhatsAppAndEmail(?User $user, string $message, string $subject): void
    {
        if (! $user) {
            return;
        }
        if ($user->phone) {
            self::tryWhatsApp($user->phone, $message);
        }
        if ($user->email) {
            try {
                app(EmailNotificationService::class)->sendToUser($user, $message, $subject);
            } catch (\Throwable $e) {
                Log::warning('Consultation email failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }
    }

    private static function tryWhatsApp(string $phone, string $message): void
    {
        try {
            if (function_exists('sendWhatsAppMessage')) {
                sendWhatsAppMessage($phone, $message);
            }
        } catch (\Throwable $e) {
            Log::warning('Consultation WhatsApp failed', ['phone' => $phone, 'error' => $e->getMessage()]);
        }
    }

    private static function tryEmailAddress(string $email, string $message, string $subject): void
    {
        try {
            \Illuminate\Support\Facades\Mail::raw($message, function ($mail) use ($email, $subject) {
                $mail->to($email)->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::warning('Consultation contact email failed', ['email' => $email, 'error' => $e->getMessage()]);
        }
    }
}
