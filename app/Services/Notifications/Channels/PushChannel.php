<?php

namespace App\Services\Notifications\Channels;

use App\Support\Notifications\NotificationRecipient;
use Illuminate\Support\Facades\Log;

/**
 * Reserved for later (Brief V3 channels_later).
 */
final class PushChannel implements NotificationChannel
{
    public function key(): string
    {
        return 'push';
    }

    public function send(NotificationRecipient $recipient, string $subject, string $body): array
    {
        Log::debug('Push channel not enabled yet', [
            'user_id' => $recipient->user?->id,
            'subject' => $subject,
        ]);

        return ['success' => false, 'skipped' => true, 'error' => 'push_not_implemented'];
    }
}
