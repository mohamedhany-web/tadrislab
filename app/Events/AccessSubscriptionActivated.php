<?php

namespace App\Events;

use App\Contracts\PlatformNotifiableEvent;
use App\Models\User;
use App\Support\Notifications\NotificationRecipient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccessSubscriptionActivated implements PlatformNotifiableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
        public string $productLabel,
        public string $extra = '',
        public ?int $orderId = null,
    ) {}

    public function notificationKey(): string
    {
        return 'access_subscription_activated';
    }

    public function recipients(): array
    {
        return [NotificationRecipient::fromUser($this->user)];
    }

    public function templateData(): array
    {
        return [
            'product' => $this->productLabel,
            'extra' => $this->extra,
            'order_id' => $this->orderId ?? '',
        ];
    }
}
