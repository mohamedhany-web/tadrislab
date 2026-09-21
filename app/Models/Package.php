<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory;

    public const TYPE_FREE = 'free';

    public const TYPE_INDIVIDUAL = 'individual';

    public const TYPE_ADVANCED = 'advanced';

    public const TYPE_SCHOOL = 'school_institution';

    public const TYPE_CUSTOM = 'custom';

    /** @deprecated legacy TADRIS LAB tracks — kept for old rows only */
    public const TRACK_ISLAMIC = 'islamic';

    public const TRACK_ENGLISH = 'english';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'card_summary',
        'features',
        'price',
        'original_price',
        'currency',
        'track',
        'package_type',
        'thumbnail',
        'duration_days',
        'courses_count',
        'consultation_sessions',
        'participant_seats',
        'includes_tools',
        'tools_resources',
        'discount_note',
        'cta_mode',
        'order',
        'is_active',
        'is_featured',
        'is_popular',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'features' => 'array',
        'tools_resources' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'includes_tools' => 'boolean',
        'consultation_sessions' => 'integer',
        'participant_seats' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * @return array<string, array{ar: string, en: string}>
     */
    public static function typeLabels(): array
    {
        $types = config('platform.packages.types', []);
        $out = [];
        foreach ($types as $key => $labels) {
            $out[$key] = [
                'ar' => $labels['ar'] ?? $key,
                'en' => $labels['en'] ?? $key,
            ];
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
     * @deprecated use typeLabels()
     * @return array<string, string>
     */
    public static function trackLabels(): array
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $out = [];
        foreach (self::typeLabels() as $key => $labels) {
            $out[$key] = $labels[$locale] ?? $key;
        }

        return $out;
    }

    public function typeLabel(?string $locale = null): string
    {
        $locale = $locale ?: (app()->getLocale() === 'ar' ? 'ar' : 'en');
        $key = $this->package_type ?: self::TYPE_INDIVIDUAL;
        $labels = self::typeLabels()[$key] ?? null;

        return $labels[$locale] ?? $key;
    }

    public function currencyCode(): string
    {
        return strtoupper((string) ($this->currency ?: platform_currency()));
    }

    public function trackLabel(): ?string
    {
        return $this->typeLabel();
    }

    public function isQuoteOnly(): bool
    {
        return in_array($this->cta_mode, ['contact', 'quote'], true)
            || in_array($this->package_type, [self::TYPE_SCHOOL, self::TYPE_CUSTOM], true);
    }

    public function isFreePackage(): bool
    {
        return $this->package_type === self::TYPE_FREE || (float) $this->price <= 0 && ! $this->isQuoteOnly();
    }

    public function formattedPrice(?int $decimals = 0): string
    {
        if ($this->isQuoteOnly() && (float) $this->price <= 0) {
            return app()->getLocale() === 'ar' ? 'عرض مخصص' : 'Custom quote';
        }

        if ((float) $this->price <= 0) {
            return app()->getLocale() === 'ar' ? 'مجاني' : 'Free';
        }

        return number_format((float) $this->price, $decimals).' '.$this->currencyCode();
    }

    public function formattedOriginalPrice(?int $decimals = 0): ?string
    {
        if ($this->original_price === null || (float) $this->original_price <= (float) $this->price) {
            return null;
        }

        return number_format((float) $this->original_price, $decimals).' '.$this->currencyCode();
    }

    public function durationLabel(): ?string
    {
        if (! $this->duration_days) {
            return app()->getLocale() === 'ar' ? 'وصول مرن' : 'Flexible access';
        }

        $days = (int) $this->duration_days;
        if ($days % 30 === 0) {
            $months = (int) ($days / 30);
            return app()->getLocale() === 'ar'
                ? ($months === 1 ? 'شهر واحد' : $months.' أشهر')
                : ($months === 1 ? '1 month' : $months.' months');
        }

        return app()->getLocale() === 'ar' ? $days.' يوم' : $days.' days';
    }

    public function coursesBundleSavings(): float
    {
        $coursesTotal = (float) $this->total_courses_price;
        $price = (float) $this->price;

        return max(0, $coursesTotal - $price);
    }

    public function ctaUrl(): string
    {
        if ($this->isQuoteOnly()) {
            return route('public.contact', ['topic' => 'package', 'package' => $this->slug]);
        }

        return route('public.packages.checkout', $this->slug);
    }

    public function ctaLabel(): string
    {
        if ($this->isQuoteOnly()) {
            return app()->getLocale() === 'ar' ? 'اطلب عرضًا' : 'Request a quote';
        }

        if ($this->isFreePackage()) {
            return app()->getLocale() === 'ar' ? 'ابدأ مجانًا' : 'Start free';
        }

        return app()->getLocale() === 'ar' ? 'اشترك الآن' : 'Subscribe';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
            if (empty($package->package_type)) {
                $package->package_type = self::TYPE_INDIVIDUAL;
            }
            if (empty($package->cta_mode)) {
                $package->cta_mode = in_array($package->package_type, [self::TYPE_SCHOOL, self::TYPE_CUSTOM], true)
                    ? 'contact'
                    : 'register';
            }
        });

        static::saving(function ($package) {
            if ($package->exists) {
                $package->courses_count = $package->courses()->count();
            }
        });
    }

    public function courses()
    {
        return $this->belongsToMany(AdvancedCourse::class, 'package_course', 'package_id', 'course_id')
            ->withPivot('order')
            ->orderBy('package_course.order')
            ->withTimestamps();
    }

    public function learningPaths()
    {
        return $this->belongsToMany(LearningPath::class, 'package_learning_path')
            ->withTimestamps();
    }

    public function entitlements()
    {
        return $this->hasMany(UserPackageEntitlement::class);
    }

    public function teacherTools()
    {
        return $this->belongsToMany(TeacherTool::class, 'package_teacher_tool')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function getDiscountAttribute()
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return $this->original_price - $this->price;
        }

        return 0;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price && $this->original_price > 0) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100, 0);
        }

        return 0;
    }

    public function getTotalCoursesPriceAttribute()
    {
        return $this->courses()->sum('price');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('package_type', $type);
    }
}
