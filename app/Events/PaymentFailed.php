<?php

namespace App\Events;

use App\Contracts\PlatformNotifiableEvent;
use App\Models\Order;
use App\Support\Notifications\NotificationRecipient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed implements PlatformNotifiableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $reason = '',
    ) {
        $this->order->loadMissing('user');
    }

    public function notificationKey(): string
    {
        return 'payment_failed';
    }

    public function recipients(): array
    {
        return $this->order->user
            ? [NotificationRecipient::fromUser($this->order->user)]
            : [];
    }

    public function templateData(): array
    {
        return [
            'order_id' => $this->order->id,
            'amount' => number_format((float) $this->order->amount, 2),
            'currency' => $this->order->currencyCode() ?: platform_currency(),
            'reason' => $this->reason !== '' ? $this->reason : 'لم تكتمل عملية الدفع.',
        ];
    }
}
