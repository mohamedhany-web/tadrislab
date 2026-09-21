<?php

namespace App\Events;

use App\Contracts\PlatformNotifiableEvent;
use App\Models\Order;
use App\Models\Payment;
use App\Support\Notifications\NotificationRecipient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessful implements PlatformNotifiableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Order $order,
        public ?Payment $payment = null,
    ) {
        $this->order->loadMissing('user');
    }

    public function notificationKey(): string
    {
        return 'payment_successful';
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
            'payment_id' => $this->payment?->id ?? $this->order->payment_id,
        ];
    }
}
