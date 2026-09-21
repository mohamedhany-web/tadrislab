<?php

namespace App\Services\Notifications\Channels;

use App\Support\Notifications\NotificationRecipient;
use Illuminate\Support\Facades\Log;

final class WhatsAppChannel implements NotificationChannel
{
    public function key(): string
    {
        return 'whatsapp';
    }

    public function send(NotificationRecipient $recipient, string $subject, string $body): array
    {
        $phone = $recipient->resolvedPhone();
        if (! $phone) {
            return ['success' => false, 'skipped' => true, 'error' => 'no_phone'];
        }

        $message = trim($subject."\n\n".$body);

        try {
            if (! function_exists('sendWhatsAppMessage')) {
                return ['success' => false, 'error' => 'whatsapp_helper_missing'];
            }

            $result = sendWhatsAppMessage($phone, $message);

            return [
                'success' => (bool) ($result['success'] ?? false),
                'error' => $result['error'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::warning('Notification WhatsAppChannel failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
