<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** درس / محتوى داخل وحدة المسار. */
class LearningPathLesson extends Model
{
    public const TYPES = ['text', 'video', 'file', 'link', 'mixed'];

    protected $fillable = [
        'learning_path_unit_id',
        'title_ar',
        'title_en',
        'content_type',
        'body_ar',
        'body_en',
        'video_url',
        'file_path',
        'external_url',
        'duration_minutes',
        'sort_order',
        'is_preview',
        'is_active',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
        'is_active' => 'boolean',
        'duration_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(LearningPathUnit::class, 'learning_path_unit_id');
    }

    public function title(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'en'
            ? ($this->title_en ?: $this->title_ar)
            : ($this->title_ar ?: (string) $this->title_en);
    }

    public function body(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'en'
            ? ($this->body_en ?: (string) $this->body_ar)
            : ($this->body_ar ?: (string) $this->body_en);
    }
}
