<?php

namespace App\Services\Notifications\Channels;

use App\Services\EmailNotificationService;
use App\Support\Notifications\NotificationRecipient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class EmailChannel implements NotificationChannel
{
    public function __construct(private readonly EmailNotificationService $email) {}

    public function key(): string
    {
        return 'email';
    }

    public function send(NotificationRecipient $recipient, string $subject, string $body): array
    {
        $address = $recipient->resolvedEmail();
        if (! $address) {
            return ['success' => false, 'skipped' => true, 'error' => 'no_email'];
        }

        try {
            if ($recipient->user) {
                return $this->email->sendToUser($recipient->user, $body, $subject);
            }

            Mail::raw($body, function ($mail) use ($address, $subject) {
                $mail->to($address)->subject($subject);
            });

            return ['success' => true];
        } catch (\Throwable $e) {
            Log::warning('Notification EmailChannel failed', [
                'email' => $address,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
