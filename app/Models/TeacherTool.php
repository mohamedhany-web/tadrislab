<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * أداة أو مورد مهني للمعلم — كيان قابل للإدارة (Brief V3 §7).
 */
class TeacherTool extends Model
{
    public const TYPE_TEMPLATES = 'templates';

    public const TYPE_CHECKLISTS = 'checklists';

    public const TYPE_CLASSROOM = 'classroom_tools';

    public const TYPE_PLANNING = 'planning_tools';

    public const TYPE_ASSESSMENT = 'assessment_resources';

    public const TYPE_DOWNLOADABLE = 'downloadable';

    public const ACCESS_FREE = 'free';

    public const ACCESS_PACKAGE = 'package';

    public const ACCESS_PATH = 'path';

    public const ACCESS_LOGIN = 'login';

    protected $fillable = [
        'slug',
        'tool_type',
        'title_ar',
        'title_en',
        'summary_ar',
        'summary_en',
        'description_ar',
        'description_en',
        'thumbnail',
        'file_path',
        'file_name',
        'file_mime',
        'file_size',
        'external_url',
        'access_mode',
        'is_standalone_product',
        'price',
        'currency',
        'sort_order',
        'is_active',
        'is_published',
    ];

    protected $casts = [
        'is_standalone_product' => 'boolean',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'file_size' => 'integer',
        'price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $tool) {
            if (blank($tool->slug)) {
                $source = $tool->title_en ?: $tool->title_ar ?: 'tool';
                $tool->slug = static::uniqueSlugFrom($source, $tool->id);
            }
            if (blank($tool->currency) && $tool->is_standalone_product) {
                $tool->currency = platform_currency();
            }
        });
    }

    public static function uniqueSlugFrom(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'tool-'.Str::random(6);
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

    /**
     * @return array<string, string>
     */
    public static function typeLabels(?string $locale = null): array
    {
        $locale = $locale ?: (app()->getLocale() === 'ar' ? 'ar' : 'en');
        $out = [];
        foreach (config('platform.tools_resources.types', []) as $key => $labels) {
            $out[$key] = is_array($labels) ? ($labels[$locale] ?? $labels['ar'] ?? $key) : (string) $labels;
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
    public static function accessModeLabels(): array
    {
        $ar = app()->getLocale() === 'ar';

        return [
            self::ACCESS_FREE => $ar ? 'مجاني / عام' : 'Free / public',
            self::ACCESS_LOGIN => $ar ? 'يتطلب تسجيل الدخول' : 'Requires login',
            self::ACCESS_PACKAGE => $ar ? 'ضمن باقة' : 'Via package',
            self::ACCESS_PATH => $ar ? 'ضمن مسار' : 'Via learning path',
        ];
    }

    public function typeLabel(?string $locale = null): string
    {
        $labels = self::typeLabels($locale);

        return $labels[$this->tool_type] ?? $this->tool_type;
    }

    public function title(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return $locale === 'en' && filled($this->title_en)
            ? (string) $this->title_en
            : (string) $this->title_ar;
    }

    public function summary(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $value = $locale === 'en' && filled($this->summary_en)
            ? $this->summary_en
            : $this->summary_ar;

        return filled($value) ? (string) $value : null;
    }

    public function description(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $value = $locale === 'en' && filled($this->description_en)
            ? $this->description_en
            : $this->description_ar;

        return filled($value) ? (string) $value : null;
    }

    public function currencyCode(): string
    {
        return normalize_currency($this->currency);
    }

    public function formattedPrice(?int $decimals = 0): ?string
    {
        if (! $this->is_standalone_product || $this->price === null) {
            return null;
        }

        return format_money($this->price, $this->currencyCode(), $decimals);
    }

    public function hasDownloadableFile(): bool
    {
        return filled($this->file_path);
    }

    public function hasExternalLink(): bool
    {
        return filled($this->external_url);
    }

    public function learningPaths(): BelongsToMany
    {
        return $this->belongsToMany(LearningPath::class, 'learning_path_teacher_tool')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_teacher_tool')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('is_published', true);
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        if ($type && in_array($type, self::typeKeys(), true)) {
            $query->where('tool_type', $type);
        }

        return $query;
    }
}
