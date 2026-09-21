<?php

namespace App\Contracts;

use App\Support\Notifications\NotificationRecipient;

/**
 * Domain events that feed the central Notification Layer.
 */
interface PlatformNotifiableEvent
{
    /**
     * Key matching config('platform.notifications.events') / config('notifications.events').
     */
    public function notificationKey(): string;

    /**
     * @return list<NotificationRecipient>
     */
    public function recipients(): array;

    /**
     * Template placeholders (:name, :order_id, …).
     *
     * @return array<string, string|int|float|null>
     */
    public function templateData(): array;
}
