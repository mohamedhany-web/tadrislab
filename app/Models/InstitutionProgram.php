<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * برنامج تدريبي أو مشروع تطوير مؤسسي تحت جهة.
 */
class InstitutionProgram extends Model
{
    public const KIND_TRAINING = 'training';

    public const KIND_DEVELOPMENT = 'development';

    public const STATUS_INQUIRY = 'inquiry';
    public const STATUS_PROPOSAL = 'proposal';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'institution_id',
        'service_key',
        'program_kind',
        'engagement_mode',
        'title_ar',
        'title_en',
        'summary_ar',
        'summary_en',
        'status',
        'planned_participants',
        'seat_limit',
        'duration_hours',
        'delivery_mode',
        'price',
        'currency',
        'starts_on',
        'ends_on',
        'scheduled_at',
        'inquiry_notes',
        'proposal_notes',
        'diagnosis_notes',
        'improvement_plan',
        'result_notes',
        'progress_percent',
        'coordinator_user_id',
        'assigned_instructor_id',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'planned_participants' => 'integer',
        'seat_limit' => 'integer',
        'duration_hours' => 'integer',
        'price' => 'decimal:2',
        'progress_percent' => 'integer',
        'starts_on' => 'date',
        'ends_on' => 'date',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function resolvedEngagementMode(): string
    {
        return \App\Services\InstitutionEngagementService::resolveMode($this);
    }

    public function engagementModeLabel(?string $locale = null): string
    {
        return \App\Services\InstitutionEngagementService::modeLabel($this->resolvedEngagementMode(), $locale);
    }

    public function isPlatformAccess(): bool
    {
        return \App\Services\InstitutionEngagementService::isPlatformAccess($this);
    }

    public function isDirectDelivery(): bool
    {
        return \App\Services\InstitutionEngagementService::isDirectDelivery($this);
    }

    public function seatsRemaining(): ?int
    {
        return \App\Services\InstitutionEngagementService::seatsRemaining($this);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_INQUIRY => 'Inquiry — استفسار',
            self::STATUS_PROPOSAL => 'Proposal — عرض',
            self::STATUS_APPROVED => 'Approved — موافقة',
            self::STATUS_SCHEDULED => 'Scheduled — مجدول',
            self::STATUS_IN_PROGRESS => 'In Progress — قيد التنفيذ',
            self::STATUS_COMPLETED => 'Completed — مكتمل',
            self::STATUS_CANCELLED => 'Cancelled — ملغى',
        ];
    }

    public static function serviceKeys(): array
    {
        return array_keys(config('platform.schools_institutions.services', []));
    }

    public static function serviceLabels(?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $out = [];
        foreach (config('platform.schools_institutions.services', []) as $key => $meta) {
            $out[$key] = is_array($meta)
                ? ($locale === 'en' ? ($meta['en'] ?? $meta['ar'] ?? $key) : ($meta['ar'] ?? $meta['en'] ?? $key))
                : (string) $meta;
        }

        return $out;
    }

    public static function deliveryModes(): array
    {
        return [
            'onsite' => 'حضوري',
            'online' => 'عن بُعد',
            'hybrid' => 'مدمج',
            'custom' => 'حسب الاتفاق',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function serviceLabel(?string $locale = null): string
    {
        return self::serviceLabels($locale)[$this->service_key] ?? $this->service_key;
    }

    public function title(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'en'
            ? ($this->title_en ?: $this->title_ar)
            : ($this->title_ar ?: (string) $this->title_en);
    }

    public function kindLabel(): string
    {
        return $this->program_kind === self::KIND_DEVELOPMENT
            ? 'تطوير مؤسسي'
            : 'تدريب';
    }

    public function isDevelopment(): bool
    {
        return $this->program_kind === self::KIND_DEVELOPMENT
            || $this->service_key === 'institutional_development'
            || $this->service_key === 'needs_assessment';
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(InstitutionProgramParticipant::class);
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_user_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_instructor_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recalculateProgress(): void
    {
        $total = $this->participants()->count();
        if ($total === 0) {
            return;
        }
        $avg = (int) round($this->participants()->avg('progress_percent'));
        $this->update(['progress_percent' => min(100, max(0, $avg))]);
    }

    /** هل يمكن للمنسق قبول العرض؟ */
    public function canCoordinatorAccept(): bool
    {
        return $this->status === self::STATUS_PROPOSAL;
    }

    /** هل يمكن للمنسق رفض/إلغاء العرض؟ */
    public function canCoordinatorReject(): bool
    {
        return in_array($this->status, [self::STATUS_PROPOSAL, self::STATUS_INQUIRY], true);
    }

    public function isDeliverable(): bool
    {
        return in_array($this->status, [
            self::STATUS_APPROVED,
            self::STATUS_SCHEDULED,
            self::STATUS_IN_PROGRESS,
        ], true);
    }

    /**
     * انتقالات الحالة المسموحة (أدمن).
     *
     * @return list<string>
     */
    public static function allowedTransitionsFrom(string $from): array
    {
        return match ($from) {
            self::STATUS_INQUIRY => [self::STATUS_PROPOSAL, self::STATUS_CANCELLED],
            self::STATUS_PROPOSAL => [self::STATUS_APPROVED, self::STATUS_INQUIRY, self::STATUS_CANCELLED],
            self::STATUS_APPROVED => [self::STATUS_SCHEDULED, self::STATUS_IN_PROGRESS, self::STATUS_CANCELLED],
            self::STATUS_SCHEDULED => [self::STATUS_IN_PROGRESS, self::STATUS_CANCELLED],
            self::STATUS_IN_PROGRESS => [self::STATUS_COMPLETED, self::STATUS_CANCELLED],
            self::STATUS_COMPLETED => [],
            self::STATUS_CANCELLED => [self::STATUS_INQUIRY],
            default => array_keys(self::statuses()),
        };
    }

    public function canTransitionTo(string $to): bool
    {
        if ($to === $this->status) {
            return true;
        }

        return in_array($to, self::allowedTransitionsFrom((string) $this->status), true);
    }
}
