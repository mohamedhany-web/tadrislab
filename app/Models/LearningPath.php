<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

/**
 * مسار تعلم وتطوير مهني للمعلم (ليس كورسًا تقليديًا منفصلًا).
 * الهيكل: Path → Units → Lessons + Practices/Tools
 */
class LearningPath extends Model
{
    protected $fillable = [
        'slug',
        'title_ar',
        'title_en',
        'skill_focus_ar',
        'skill_focus_en',
        'summary_ar',
        'summary_en',
        'description_ar',
        'description_en',
        'thumbnail',
        'estimated_minutes',
        'price',
        'currency',
        'is_sellable_standalone',
        'access_days',
        'sort_order',
        'is_active',
        'is_published',
        'instructor_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'is_sellable_standalone' => 'boolean',
        'estimated_minutes' => 'integer',
        'access_days' => 'integer',
        'sort_order' => 'integer',
        'price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $path) {
            if (blank($path->slug)) {
                $source = $path->title_en ?: $path->title_ar ?: 'path';
                $path->slug = static::uniqueSlugFrom($source, $path->id);
            }
        });
    }

    public static function uniqueSlugFrom(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'path-'.Str::random(6);
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

    public function skillFocus(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'en'
            ? ($this->skill_focus_en ?: (string) $this->skill_focus_ar)
            : ($this->skill_focus_ar ?: (string) $this->skill_focus_en);
    }

    public function description(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'en'
            ? ($this->description_en ?: (string) $this->description_ar)
            : ($this->description_ar ?: (string) $this->description_en);
    }

    public function units(): HasMany
    {
        return $this->hasMany(LearningPathUnit::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeUnits(): HasMany
    {
        return $this->units()->where('is_active', true);
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(
            LearningPathLesson::class,
            LearningPathUnit::class,
            'learning_path_id',
            'learning_path_unit_id'
        );
    }

    public function practices(): HasManyThrough
    {
        return $this->hasManyThrough(
            LearningPathPractice::class,
            LearningPathUnit::class,
            'learning_path_id',
            'learning_path_unit_id'
        );
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_learning_path')
            ->withTimestamps();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(TeacherPathEnrollment::class);
    }

    public function teacherTools(): BelongsToMany
    {
        return $this->belongsToMany(TeacherTool::class, 'learning_path_teacher_tool')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('is_active', true)->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeSellableStandalone($query)
    {
        return $query->where('is_sellable_standalone', true)->where('is_active', true);
    }

    public function formattedPrice(?int $decimals = 2): ?string
    {
        if ($this->price === null) {
            return null;
        }

        $currency = strtoupper((string) ($this->currency ?: 'QAR'));

        return number_format((float) $this->price, $decimals).' '.$currency;
    }

    public function isStandaloneProduct(): bool
    {
        return (bool) $this->is_sellable_standalone;
    }
}
