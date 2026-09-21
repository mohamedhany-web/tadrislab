<?php

namespace App\Listeners;

use App\Contracts\PlatformNotifiableEvent;
use App\Services\Notifications\PlatformNotificationDispatcher;

class SendPlatformNotification
{
    public function __construct(private readonly PlatformNotificationDispatcher $dispatcher) {}

    public function handle(PlatformNotifiableEvent $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}
