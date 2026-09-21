<?php

namespace App\Events;

use App\Contracts\PlatformNotifiableEvent;
use App\Models\Order;
use App\Support\Notifications\NotificationRecipient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged implements PlatformNotifiableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $previousStatus,
        public string $newStatus,
        public string $extra = '',
    ) {
        $this->order->loadMissing('user');
    }

    public function notificationKey(): string
    {
        return 'order_status_changed';
    }

    public function recipients(): array
    {
        return $this->order->user
            ? [NotificationRecipient::fromUser($this->order->user)]
            : [];
    }

    public function templateData(): array
    {
        $labels = [
            Order::STATUS_PENDING => 'في الانتظار',
            Order::STATUS_APPROVED => 'مقبول',
            Order::STATUS_REJECTED => 'مرفوض',
        ];

        return [
            'order_id' => $this->order->id,
            'status_label' => $labels[$this->newStatus] ?? $this->newStatus,
            'previous_status' => $this->previousStatus,
            'extra' => $this->extra,
            'amount' => number_format((float) $this->order->amount, 2),
            'currency' => $this->order->currencyCode() ?: platform_currency(),
        ];
    }
}
