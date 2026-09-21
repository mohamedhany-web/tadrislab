<?php

namespace App\Events;

use App\Contracts\PlatformNotifiableEvent;
use App\Models\ConsultationRequest;
use App\Services\ClassroomMeetingAccessService;
use App\Support\Notifications\NotificationRecipient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmed implements PlatformNotifiableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public ConsultationRequest $consultation)
    {
        $this->consultation->loadMissing(['student', 'instructor', 'service', 'classroomMeeting']);
    }

    public function notificationKey(): string
    {
        return 'booking_confirmed';
    }

    public function recipients(): array
    {
        $list = [];
        if ($this->consultation->student) {
            $list[] = NotificationRecipient::fromUser($this->consultation->student);
        }
        if ($this->consultation->instructor) {
            $list[] = NotificationRecipient::fromUser($this->consultation->instructor);
        }
        if ($this->consultation->contact_email || $this->consultation->contact_phone) {
            $list[] = NotificationRecipient::contact(
                $this->consultation->contact_name,
                $this->consultation->contact_email,
                $this->consultation->contact_phone
            );
        }

        return $list;
    }

    public function templateData(): array
    {
        $when = $this->consultation->scheduled_at
            ? $this->consultation->scheduled_at->timezone(config('app.timezone'))->format('Y-m-d H:i')
            : '—';
        $joinUrl = $this->consultation->classroomMeeting
            ? ClassroomMeetingAccessService::platformEnterUrl($this->consultation->classroomMeeting)
            : null;

        return [
            'service' => $this->consultation->service?->title() ?? 'استشارة',
            'when' => $when,
            'join_line' => $joinUrl ? 'رابط الجلسة: '.$joinUrl : '',
            'booking_id' => $this->consultation->id,
        ];
    }
}
