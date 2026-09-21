<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * خدمة استشارية قابلة للإدارة من الأدمن (قسم مستقل عن المسارات).
 */
class ConsultationService extends Model
{
    public const TYPES = [
        'teacher_individual',
        'specialized',
        'institution',
        'coaching_mentoring',
    ];

    /**
     * Allowed consultation type keys — driven by platform taxonomy (not a second hard-coded catalogue).
     *
     * @return list<string>
     */
    public static function allowedTypes(): array
    {
        $fromConfig = array_keys(config('platform.consultations.types', []));

        return $fromConfig !== [] ? array_values($fromConfig) : self::TYPES;
    }

    protected $fillable = [
        'slug',
        'consultation_type',
        'title_ar',
        'title_en',
        'summary_ar',
        'summary_en',
        'description_ar',
        'description_en',
        'duration_minutes',
        'price',
        'currency',
        'requires_instructor',
        'is_active',
        'is_published',
        'is_mvp',
        'sort_order',
        'default_instructor_id',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'price' => 'decimal:2',
        'requires_instructor' => 'boolean',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'is_mvp' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $service) {
            if (blank($service->slug)) {
                $source = $service->title_en ?: $service->title_ar ?: 'consultation';
                $service->slug = static::uniqueSlugFrom($source, $service->id);
            }
        });
    }

    public static function uniqueSlugFrom(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'consultation-'.Str::random(6);
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

    public static function typeLabels(): array
    {
        $types = config('platform.consultations.types', []);
        $out = [];
        foreach ($types as $key => $meta) {
            $out[$key] = is_array($meta) ? ($meta['ar'] ?? $key) : (string) $meta;
        }

        return $out;
    }

    public function typeLabel(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $meta = config('platform.consultations.types.'.$this->consultation_type, []);
        if (! is_array($meta)) {
            return (string) $this->consultation_type;
        }

        return $locale === 'en'
            ? ($meta['en'] ?? $meta['ar'] ?? $this->consultation_type)
            : ($meta['ar'] ?? $meta['en'] ?? $this->consultation_type);
    }

    public function title(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'en'
            ? ($this->title_en ?: $this->title_ar)
            : ($this->title_ar ?: (string) $this->title_en);
    }

    public function summary(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'en'
            ? ($this->summary_en ?: (string) $this->summary_ar)
            : ($this->summary_ar ?: (string) $this->summary_en);
    }

    public function description(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'en'
            ? ($this->description_en ?: (string) $this->description_ar)
            : ($this->description_ar ?: (string) $this->description_en);
    }

    public function scopeBookable($query)
    {
        return $query->where('is_active', true)
            ->where('is_published', true)
            ->where('is_mvp', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function defaultInstructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'default_instructor_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(ConsultationRequest::class, 'consultation_service_id');
    }
}
