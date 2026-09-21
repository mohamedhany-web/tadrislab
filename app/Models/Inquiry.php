<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * استفسار داخلي قابل للمتابعة (Brief V3 §11) — حتى لو بدأ من واتساب.
 */
class Inquiry extends Model
{
    public const STATUS_NEW = 'new';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_RESOLVED = 'resolved';

    public const TYPE_LEARNING_PATH = 'learning_path';

    public const TYPE_PACKAGE = 'package';

    public const TYPE_CONSULTATION = 'consultation';

    public const TYPE_PAYMENT = 'payment';

    public const TYPE_TECHNICAL = 'technical';

    public const TYPE_SCHOOL_INSTITUTION = 'school_institution';

    public const TYPE_GENERAL = 'general';

    public const SOURCE_CONTACT = 'contact_form';

    public const SOURCE_WHATSAPP = 'whatsapp';

    public const SOURCE_ADMIN = 'admin';

    public const SOURCE_INSTITUTION = 'institution_form';

    public const SOURCE_OTHER = 'other';

    protected $fillable = [
        'reference',
        'name',
        'email',
        'phone',
        'inquiry_type',
        'status',
        'source',
        'user_id',
        'order_id',
        'consultation_request_id',
        'institution_id',
        'institution_program_id',
        'contact_message_id',
        'subject',
        'message',
        'admin_notes',
        'assigned_to',
        'inquired_at',
        'resolved_at',
    ];

    protected $casts = [
        'inquired_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $inquiry) {
            if (blank($inquiry->reference)) {
                $inquiry->reference = static::nextReference();
            }
            if (blank($inquiry->inquired_at)) {
                $inquiry->inquired_at = now();
            }
            if (blank($inquiry->status)) {
                $inquiry->status = self::STATUS_NEW;
            }
        });

        static::saving(function (self $inquiry) {
            if ($inquiry->isDirty('status')) {
                if ($inquiry->status === self::STATUS_RESOLVED && ! $inquiry->resolved_at) {
                    $inquiry->resolved_at = now();
                }
                if ($inquiry->status !== self::STATUS_RESOLVED) {
                    $inquiry->resolved_at = null;
                }
            }
        });
    }

    public static function nextReference(): string
    {
        do {
            $ref = 'INQ-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (static::query()->where('reference', $ref)->exists());

        return $ref;
    }

    /**
     * @return array<string, string>
     */
    public static function typeLabels(?string $locale = null): array
    {
        $locale = $locale ?: (app()->getLocale() === 'ar' ? 'ar' : 'en');
        $map = [
            self::TYPE_LEARNING_PATH => ['ar' => 'مسار تعليمي', 'en' => 'Learning Path'],
            self::TYPE_PACKAGE => ['ar' => 'باقة', 'en' => 'Package'],
            self::TYPE_CONSULTATION => ['ar' => 'استشارة', 'en' => 'Consultation'],
            self::TYPE_PAYMENT => ['ar' => 'دفع', 'en' => 'Payment'],
            self::TYPE_TECHNICAL => ['ar' => 'تقني', 'en' => 'Technical'],
            self::TYPE_SCHOOL_INSTITUTION => ['ar' => 'مدرسة / مؤسسة', 'en' => 'School & Institution'],
            self::TYPE_GENERAL => ['ar' => 'عام', 'en' => 'General'],
        ];
        $out = [];
        foreach ($map as $key => $labels) {
            $out[$key] = $labels[$locale] ?? $labels['ar'];
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    public static function typeKeys(): array
    {
        return array_keys(self::typeLabels());
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(?string $locale = null): array
    {
        $locale = $locale ?: (app()->getLocale() === 'ar' ? 'ar' : 'en');
        $map = [
            self::STATUS_NEW => ['ar' => 'جديد', 'en' => 'New'],
            self::STATUS_IN_PROGRESS => ['ar' => 'قيد المعالجة', 'en' => 'In Progress'],
            self::STATUS_RESOLVED => ['ar' => 'محلول', 'en' => 'Resolved'],
        ];
        $out = [];
        foreach ($map as $key => $labels) {
            $out[$key] = $labels[$locale] ?? $labels['ar'];
        }

        return $out;
    }

    /**
     * @return array<string, string>
     */
    public static function sourceLabels(?string $locale = null): array
    {
        $locale = $locale ?: (app()->getLocale() === 'ar' ? 'ar' : 'en');
        $map = [
            self::SOURCE_CONTACT => ['ar' => 'نموذج التواصل', 'en' => 'Contact form'],
            self::SOURCE_WHATSAPP => ['ar' => 'واتساب', 'en' => 'WhatsApp'],
            self::SOURCE_ADMIN => ['ar' => 'إدخال إداري', 'en' => 'Admin'],
            self::SOURCE_INSTITUTION => ['ar' => 'استفسار مؤسسة', 'en' => 'Institution form'],
            self::SOURCE_OTHER => ['ar' => 'أخرى', 'en' => 'Other'],
        ];
        $out = [];
        foreach ($map as $key => $labels) {
            $out[$key] = $labels[$locale] ?? $labels['ar'];
        }

        return $out;
    }

    public function typeLabel(): string
    {
        return self::typeLabels()[$this->inquiry_type] ?? $this->inquiry_type;
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function sourceLabel(): string
    {
        return self::sourceLabels()[$this->source] ?? $this->source;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function consultationRequest(): BelongsTo
    {
        return $this->belongsTo(ConsultationRequest::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function institutionProgram(): BelongsTo
    {
        return $this->belongsTo(InstitutionProgram::class, 'institution_program_id');
    }

    public function contactMessage(): BelongsTo
    {
        return $this->belongsTo(ContactMessage::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if ($status && array_key_exists($status, self::statusLabels())) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        if ($type && in_array($type, self::typeKeys(), true)) {
            $query->where('inquiry_type', $type);
        }

        return $query;
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_NEW, self::STATUS_IN_PROGRESS]);
    }
}
