<?php

namespace App\Services\Notifications\Channels;

use App\Support\Notifications\NotificationRecipient;
use Illuminate\Support\Facades\Log;

/**
 * Reserved for later (Brief V3 channels_later).
 */
final class SmsChannel implements NotificationChannel
{
    public function key(): string
    {
        return 'sms';
    }

    public function send(NotificationRecipient $recipient, string $subject, string $body): array
    {
        Log::debug('SMS channel not enabled yet', [
            'phone' => $recipient->resolvedPhone(),
            'subject' => $subject,
        ]);

        return ['success' => false, 'skipped' => true, 'error' => 'sms_not_implemented'];
    }
}
