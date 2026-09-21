<?php

namespace App\Events;

use App\Contracts\PlatformNotifiableEvent;
use App\Models\InstitutionProgram;
use App\Support\Notifications\NotificationRecipient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InstitutionProgramStatusChanged implements PlatformNotifiableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public InstitutionProgram $program,
        public string $previousStatus,
        public string $newStatus,
    ) {
        $this->program->loadMissing(['institution', 'createdBy']);
    }

    public function notificationKey(): string
    {
        return 'institution_program_status_changed';
    }

    public function recipients(): array
    {
        $list = [];
        $institution = $this->program->institution;
        if ($institution) {
            $list[] = NotificationRecipient::contact(
                $institution->contact_name ?: $institution->name_ar,
                $institution->contact_email,
                $institution->contact_phone
            );
        }
        if ($this->program->createdBy) {
            $list[] = NotificationRecipient::fromUser($this->program->createdBy);
        }

        return $list;
    }

    public function templateData(): array
    {
        return [
            'program' => $this->program->title() ?: ('#'.$this->program->id),
            'institution' => $this->program->institution?->name_ar ?? '—',
            'status_label' => $this->program->statusLabel(),
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
