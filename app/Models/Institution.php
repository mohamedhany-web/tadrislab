<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * حساب جهة تعليمية (مدرسة / مركز / مؤسسة) — محور تجاري واحد.
 */
class Institution extends Model
{
    public const ORG_TYPES = ['school', 'center', 'institution', 'other'];

    public const ENGAGEMENT_DIRECT = 'direct_delivery';

    public const ENGAGEMENT_PLATFORM = 'platform_access';

    protected $fillable = [
        'slug',
        'name_ar',
        'name_en',
        'org_type',
        'default_engagement_mode',
        'seat_limit',
        'country',
        'city',
        'contact_name',
        'contact_email',
        'contact_phone',
        'notes',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'seat_limit' => 'integer',
    ];

    public static function engagementModeLabels(?string $locale = null): array
    {
        return \App\Services\InstitutionEngagementService::modes();
    }

    public function engagementModeLabel(?string $locale = null): string
    {
        return \App\Services\InstitutionEngagementService::modeLabel(
            $this->default_engagement_mode ?: self::ENGAGEMENT_PLATFORM,
            $locale
        );
    }

    public function isPlatformAccessDefault(): bool
    {
        return ($this->default_engagement_mode ?: self::ENGAGEMENT_PLATFORM) === self::ENGAGEMENT_PLATFORM;
    }

    public function isDirectDeliveryDefault(): bool
    {
        return ($this->default_engagement_mode ?: self::ENGAGEMENT_PLATFORM) === self::ENGAGEMENT_DIRECT;
    }

    protected static function booted(): void
    {
        static::saving(function (self $org) {
            if (blank($org->slug)) {
                $org->slug = static::uniqueSlugFrom($org->name_en ?: $org->name_ar ?: 'institution', $org->id);
            }
        });
    }

    public static function uniqueSlugFrom(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'institution-'.Str::random(6);
        $slug = $base;
        $i = 2;
        while (static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public static function orgTypeLabels(): array
    {
        return [
            'school' => 'مدرسة',
            'center' => 'مركز',
            'institution' => 'مؤسسة تعليمية',
            'other' => 'جهة تعليمية',
        ];
    }

    public function name(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'en'
            ? ($this->name_en ?: $this->name_ar)
            : ($this->name_ar ?: (string) $this->name_en);
    }

    public function orgTypeLabel(): string
    {
        return self::orgTypeLabels()[$this->org_type] ?? $this->org_type;
    }

    public function members(): HasMany
    {
        return $this->hasMany(InstitutionMember::class);
    }

    public function coordinators(): HasMany
    {
        return $this->members()->where('member_role', InstitutionMember::ROLE_COORDINATOR);
    }

    public function participants(): HasMany
    {
        return $this->members()->where('member_role', InstitutionMember::ROLE_PARTICIPANT);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(InstitutionProgram::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ConsultationRequest::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
