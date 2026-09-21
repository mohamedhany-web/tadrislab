<?php

namespace App\Services\Notifications\Channels;

use App\Support\Notifications\NotificationRecipient;

interface NotificationChannel
{
    public function key(): string;

    /**
     * @return array{success: bool, skipped?: bool, error?: string}
     */
    public function send(NotificationRecipient $recipient, string $subject, string $body): array;
}
