<?php

namespace App\Events;

use App\Contracts\PlatformNotifiableEvent;
use App\Models\Inquiry;
use App\Models\User;
use App\Support\Notifications\NotificationRecipient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewInquiry implements PlatformNotifiableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public Inquiry $inquiry) {}

    public function notificationKey(): string
    {
        return 'new_inquiry';
    }

    public function recipients(): array
    {
        $list = [];

        // Ops / admin inbox
        $admins = User::query()
            ->whereIn('role', ['admin', 'super_admin'])
            ->where('is_active', true)
            ->orderBy('id')
            ->limit(25)
            ->get(['id', 'name', 'email', 'phone']);

        foreach ($admins as $admin) {
            $list[] = NotificationRecipient::fromUser($admin);
        }

        // Ack to the inquirer when contact details exist
        if ($this->inquiry->email || $this->inquiry->phone) {
            $list[] = NotificationRecipient::contact(
                $this->inquiry->name,
                $this->inquiry->email,
                $this->inquiry->phone
            );
        }

        return $list;
    }

    public function templateData(): array
    {
        return [
            'reference' => $this->inquiry->reference,
            'type' => $this->inquiry->typeLabel(),
            'name' => $this->inquiry->name,
            'phone' => $this->inquiry->phone ?? '—',
            'email' => $this->inquiry->email ?? '—',
            'source' => $this->inquiry->sourceLabel(),
            'message' => $this->inquiry->message
                ? mb_substr((string) $this->inquiry->message, 0, 500)
                : ($this->inquiry->subject ?? ''),
        ];
    }
}
