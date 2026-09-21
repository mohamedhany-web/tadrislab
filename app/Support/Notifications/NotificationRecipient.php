<?php

namespace App\Support\Notifications;

use App\Models\User;

final class NotificationRecipient
{
    public function __construct(
        public readonly ?User $user = null,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
    ) {}

    public static function fromUser(User $user): self
    {
        return new self(
            user: $user,
            name: $user->name,
            email: $user->email,
            phone: $user->phone,
        );
    }

    public static function contact(?string $name = null, ?string $email = null, ?string $phone = null): self
    {
        return new self(
            name: $name,
            email: $email ? trim($email) : null,
            phone: $phone ? trim($phone) : null,
        );
    }

    public function displayName(): string
    {
        return $this->name
            ?: $this->user?->name
            ?: 'عميل تدريس لاب';
    }

    public function resolvedEmail(): ?string
    {
        $email = $this->email ?: $this->user?->email;

        return filled($email) ? trim((string) $email) : null;
    }

    public function resolvedPhone(): ?string
    {
        $phone = $this->phone ?: $this->user?->phone;

        return filled($phone) ? trim((string) $phone) : null;
    }

    public function hasChannelTarget(): bool
    {
        return $this->resolvedEmail() !== null || $this->resolvedPhone() !== null;
    }
}
