<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class ConsultationRequest extends Model
{
    /** Brief V3 booking statuses */
    public const STATUS_NEW = 'new';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_RESCHEDULED = 'rescheduled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /** Legacy payment-pipeline statuses (still readable) */
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAYMENT_REPORTED = 'payment_reported';
    public const STATUS_AWAITING_VERIFICATION = 'awaiting_verification';
    public const STATUS_PAID = 'paid';
    /** @deprecated use STATUS_CONFIRMED */
    public const STATUS_SCHEDULED = 'scheduled';

    protected $fillable = [
        'consultation_service_id',
        'consultation_type',
        'instructor_id',
        'student_id',
        'order_id',
        'institution_id',
        'price_amount',
        'currency',
        'duration_minutes',
        'student_message',
        'contact_name',
        'contact_phone',
        'contact_email',
        'organization_name',
        'form_payload',
        'preferred_slot_at',
        'payment_reference',
        'status',
        'payment_reported_at',
        'paid_confirmed_at',
        'paid_confirmed_by',
        'scheduled_at',
        'reminder_sent_at',
        'rescheduled_from',
        'admin_notes',
        'outcome_notes',
        'recommendations',
        'classroom_meeting_id',
        'wallet_transaction_id',
        'platform_wallet_id',
        'payment_method',
        'payment_proof',
    ];

    protected function casts(): array
    {
        return [
            'price_amount' => 'decimal:2',
            'duration_minutes' => 'integer',
            'payment_reported_at' => 'datetime',
            'paid_confirmed_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'preferred_slot_at' => 'datetime',
            'rescheduled_from' => 'datetime',
            'form_payload' => 'array',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_NEW => 'جديد',
            self::STATUS_CONFIRMED => 'مؤكد',
            self::STATUS_RESCHEDULED => 'معاد جدولته',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_CANCELLED => 'ملغى',
            // legacy
            self::STATUS_PENDING => 'بانتظار الدفع (قديم)',
            self::STATUS_PAYMENT_REPORTED => 'أبلغ عن التحويل (قديم)',
            self::STATUS_AWAITING_VERIFICATION => 'بانتظار التحقق (قديم)',
            self::STATUS_PAID => 'مدفوع — بانتظار التأكيد (قديم)',
            self::STATUS_SCHEDULED => 'مجدول (قديم → مؤكد)',
        ];
    }

    /** Brief statuses for admin filters / UX */
    public static function briefStatusLabels(): array
    {
        return [
            self::STATUS_NEW => 'New — جديد',
            self::STATUS_CONFIRMED => 'Confirmed — مؤكد',
            self::STATUS_RESCHEDULED => 'Rescheduled — معاد جدولته',
            self::STATUS_COMPLETED => 'Completed — مكتمل',
            self::STATUS_CANCELLED => 'Cancelled — ملغى',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(ConsultationService::class, 'consultation_service_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function paidConfirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_confirmed_by');
    }

    public function classroomMeeting(): BelongsTo
    {
        return $this->belongsTo(ClassroomMeeting::class, 'classroom_meeting_id');
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'wallet_transaction_id');
    }

    public function platformWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'platform_wallet_id');
    }

    public function paidViaWallet(): bool
    {
        return $this->wallet_transaction_id !== null;
    }

    public function paidViaPlatformAccounts(): bool
    {
        return $this->wallet_transaction_id === null && $this->payment_proof !== null;
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    /** Normalize legacy statuses onto Brief V3 labels for display. */
    public function briefStatus(): string
    {
        return match ($this->status) {
            self::STATUS_NEW,
            self::STATUS_PENDING,
            self::STATUS_PAYMENT_REPORTED,
            self::STATUS_AWAITING_VERIFICATION,
            self::STATUS_PAID => self::STATUS_NEW,
            self::STATUS_CONFIRMED,
            self::STATUS_SCHEDULED => self::STATUS_CONFIRMED,
            self::STATUS_RESCHEDULED => self::STATUS_RESCHEDULED,
            self::STATUS_COMPLETED => self::STATUS_COMPLETED,
            self::STATUS_CANCELLED => self::STATUS_CANCELLED,
            default => $this->status,
        };
    }

    public function briefStatusLabel(): string
    {
        return self::briefStatusLabels()[$this->briefStatus()] ?? $this->statusLabel();
    }

    public function isAwaitingAdmin(): bool
    {
        return in_array($this->status, [
            self::STATUS_NEW,
            self::STATUS_PENDING,
            self::STATUS_PAYMENT_REPORTED,
            self::STATUS_AWAITING_VERIFICATION,
            self::STATUS_PAID,
        ], true);
    }

    public function isActiveBooking(): bool
    {
        return in_array($this->status, [
            self::STATUS_CONFIRMED,
            self::STATUS_RESCHEDULED,
            self::STATUS_SCHEDULED,
        ], true);
    }

    public function isScheduled(): bool
    {
        return $this->isActiveBooking()
            && $this->scheduled_at
            && $this->classroom_meeting_id;
    }

    public function typeLabel(?string $locale = null): string
    {
        $type = $this->consultation_type ?: $this->service?->consultation_type;
        if (! $type) {
            return '—';
        }
        $locale ??= app()->getLocale();
        $meta = config('platform.consultations.types.'.$type, []);

        return is_array($meta)
            ? ($locale === 'en' ? ($meta['en'] ?? $meta['ar'] ?? $type) : ($meta['ar'] ?? $meta['en'] ?? $type))
            : (string) $type;
    }

    /**
     * @param  \Carbon\Carbon|string|null  $startDate
     * @param  \Carbon\Carbon|string|null  $endDate
     * @param  'student'|'instructor'  $perspective
     */
    public static function calendarItemsForUser(User $user, $startDate, $endDate, string $perspective): Collection
    {
        $q = static::query()
            ->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_RESCHEDULED, self::STATUS_SCHEDULED])
            ->whereNotNull('scheduled_at');

        if ($perspective === 'student') {
            $q->where('student_id', $user->id);
        } else {
            $q->where('instructor_id', $user->id);
        }

        if ($startDate) {
            $q->where('scheduled_at', '>=', $startDate);
        }
        if ($endDate) {
            $q->where('scheduled_at', '<=', $endDate);
        }

        return $q->with(['instructor', 'student', 'classroomMeeting', 'service'])->get()->map(function (self $cr) use ($perspective) {
            $end = $cr->scheduled_at->copy()->addMinutes($cr->duration_minutes ?? 30);
            $joinUrl = $cr->classroomMeeting
                ? \App\Services\ClassroomMeetingAccessService::platformEnterUrl($cr->classroomMeeting)
                : null;
            $isStudent = $perspective === 'student';

            $title = $isStudent
                ? ('استشارة مع: '.($cr->instructor->name ?? ''))
                : ('استشارة: '.($cr->student->name ?? ''));

            return (object) [
                'calendar_id' => 'consultation_'.$cr->id,
                'id' => $cr->id,
                'title' => $title,
                'description' => $joinUrl ? ('رابط الغرفة: '.$joinUrl) : null,
                'start_date' => $cr->scheduled_at,
                'end_date' => $end,
                'is_all_day' => false,
                'type' => 'consultation',
                'color' => '#059669',
                'priority' => 'high',
                'url' => $isStudent ? route('consultations.show', $cr) : route('instructor.consultations.show', $cr),
                'location' => $joinUrl,
            ];
        });
    }
}
