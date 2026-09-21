<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** تطبيق / أداة عملية مرتبطة بوحدة المسار. */
class LearningPathPractice extends Model
{
    public const TYPES = ['tool', 'application', 'template', 'checklist', 'activity', 'assessment'];

    protected $fillable = [
        'learning_path_unit_id',
        'title_ar',
        'title_en',
        'summary_ar',
        'summary_en',
        'practice_type',
        'body_ar',
        'body_en',
        'resource_url',
        'file_path',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function summary(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'en'
            ? ($this->summary_en ?: (string) $this->summary_ar)
            : ($this->summary_ar ?: (string) $this->summary_en);
    }

    public function body(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'en'
            ? ($this->body_en ?: (string) $this->body_ar)
            : ($this->body_ar ?: (string) $this->body_en);
    }

    public function typeLabel(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $labels = [
            'tool' => ['ar' => 'أداة', 'en' => 'Tool'],
            'application' => ['ar' => 'تطبيق عملي', 'en' => 'Application'],
            'template' => ['ar' => 'قالب', 'en' => 'Template'],
            'checklist' => ['ar' => 'قائمة تحقق', 'en' => 'Checklist'],
            'activity' => ['ar' => 'نشاط', 'en' => 'Activity'],
            'assessment' => ['ar' => 'تقييم', 'en' => 'Assessment'],
        ];

        return $labels[$this->practice_type][$locale === 'en' ? 'en' : 'ar']
            ?? $this->practice_type;
    }
}
