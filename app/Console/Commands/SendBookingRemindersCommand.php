<?php

namespace App\Console\Commands;

use App\Events\BookingReminder;
use App\Models\ConsultationRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class SendBookingRemindersCommand extends Command
{
    protected $signature = 'bookings:send-reminders
        {--minutes= : Minutes ahead window (default from config/notifications.php)}';

    protected $description = 'Dispatch Booking Reminder notifications for upcoming confirmed consultations';

    public function handle(): int
    {
        if (! Schema::hasTable('consultation_requests')) {
            $this->warn('consultation_requests table missing.');

            return self::SUCCESS;
        }

        $minutes = (int) ($this->option('minutes') ?: config('notifications.booking_reminder_minutes', 60));
        $minutes = max(5, min(24 * 60, $minutes));

        $from = now();
        $to = now()->addMinutes($minutes);

        $query = ConsultationRequest::query()
            ->with(['student', 'service', 'classroomMeeting'])
            ->whereIn('status', [
                ConsultationRequest::STATUS_CONFIRMED,
                ConsultationRequest::STATUS_RESCHEDULED,
                ConsultationRequest::STATUS_SCHEDULED,
            ])
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [$from, $to]);

        if (Schema::hasColumn('consultation_requests', 'reminder_sent_at')) {
            $query->whereNull('reminder_sent_at');
        }

        $count = 0;
        $query->orderBy('scheduled_at')->chunkById(50, function ($bookings) use (&$count) {
            foreach ($bookings as $booking) {
                event(new BookingReminder($booking));
                if (Schema::hasColumn('consultation_requests', 'reminder_sent_at')) {
                    $booking->forceFill(['reminder_sent_at' => now()])->saveQuietly();
                }
                $count++;
            }
        });

        $this->info("Booking reminders dispatched: {$count} (window {$minutes}m).");

        return self::SUCCESS;
    }
}
