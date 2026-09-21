<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** وحدة قصيرة داخل مسار تطوير مهني. */
class LearningPathUnit extends Model
{
    protected $fillable = [
        'learning_path_id',
        'title_ar',
        'title_en',
        'summary_ar',
        'summary_en',
        'estimated_minutes',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'estimated_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    public function path(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class, 'learning_path_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(LearningPathLesson::class)->orderBy('sort_order')->orderBy('id');
    }

    public function practices(): HasMany
    {
        return $this->hasMany(LearningPathPractice::class)->orderBy('sort_order')->orderBy('id');
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
}
