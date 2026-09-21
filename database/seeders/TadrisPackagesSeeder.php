<?php

namespace Database\Seeders;

use App\Models\LearningPath;
use App\Models\Package;
use Illuminate\Database\Seeder;

class TadrisPackagesSeeder extends Seeder
{
    /**
     * Seed default TADRIS LAB packages only when missing.
     * Does not overwrite admin edits on existing rows.
     */
    public function run(): void
    {
        $catalog = [
            [
                'name' => 'باقة مجانية',
                'slug' => 'free',
                'package_type' => Package::TYPE_FREE,
                'card_summary' => 'تعرّف على المنصة واستكشف عينات من الموارد والمسارات.',
                'description' => 'باقة تعريفية للمعلم: إنشاء حساب، استعراض المسارات، وتجربة موارد محدودة قبل الاشتراك.',
                'features' => [
                    'إنشاء حساب معلم',
                    'استكشاف كتالوج المسارات والكورسات',
                    'عينات من الأدوات والموارد',
                    'الاطلاع على باقات الترقية',
                ],
                'tools_resources' => [
                    'عينة قالب تخطيط درس',
                ],
                'price' => 0,
                'currency' => 'QAR',
                'duration_days' => null,
                'consultation_sessions' => 0,
                'participant_seats' => 1,
                'includes_tools' => false,
                'cta_mode' => 'register',
                'order' => 10,
                'is_active' => true,
                'is_featured' => false,
                'is_popular' => false,
            ],
            [
                'name' => 'باقة فردية',
                'slug' => 'individual',
                'package_type' => Package::TYPE_INDIVIDUAL,
                'card_summary' => 'للمعلم الفردي: مسارات مختارة وأدوات تطبيقية ومتابعة تقدّم.',
                'description' => 'باقة للمعلم الذي يريد تطوير ممارسته عبر مسارات وكورسات وأدوات صفية، مع إمكانية إضافة استشارة.',
                'features' => [
                    'مسارات/كورسات تطوير مهني مختارة',
                    'أدوات وموارد تطبيقية',
                    'متابعة التقدّم داخل المنصة',
                    'دعم عبر المنصة',
                    'إمكانية إضافة جلسة استشارة',
                ],
                'tools_resources' => [
                    'قوالب تخطيط دروس',
                    'قوائم تحقق صفية',
                ],
                'discount_note' => 'خصم إطلاق لفترة محدودة',
                'price' => 299,
                'original_price' => 399,
                'currency' => 'QAR',
                'duration_days' => 90,
                'consultation_sessions' => 0,
                'participant_seats' => 1,
                'includes_tools' => true,
                'cta_mode' => 'register',
                'order' => 20,
                'is_active' => true,
                'is_featured' => false,
                'is_popular' => false,
            ],
            [
                'name' => 'باقة متقدمة',
                'slug' => 'advanced',
                'package_type' => Package::TYPE_ADVANCED,
                'card_summary' => 'الأكثر طلبًا: وصول أوسع + جلسات استشارة + قياس أوضح للتقدّم.',
                'description' => 'للمعلم النشط الذي يحتاج مكتبة أوسع، استشارات ضمن الباقة، ومتابعة أدق للأثر داخل الصف.',
                'features' => [
                    'وصول أوسع للمسارات والكورسات',
                    'مكتبة أدوات وموارد أشمل',
                    'جلسات استشارة ضمن الباقة',
                    'قياس تقدّم أوضح',
                    'أولوية في الدعم',
                ],
                'tools_resources' => [
                    'مكتبة أدوات صفية',
                    'نماذج تقييم ومتابعة',
                    'موارد تطوير مهني قابلة للتحميل',
                ],
                'discount_note' => 'الأكثر طلبًا — وفّر مقابل السعر الأصلي',
                'price' => 599,
                'original_price' => 799,
                'currency' => 'QAR',
                'duration_days' => 180,
                'consultation_sessions' => 2,
                'participant_seats' => 1,
                'includes_tools' => true,
                'cta_mode' => 'register',
                'order' => 30,
                'is_active' => true,
                'is_featured' => true,
                'is_popular' => true,
            ],
            [
                'name' => 'باقة مدارس ومؤسسات',
                'slug' => 'school-institution',
                'package_type' => Package::TYPE_SCHOOL,
                'card_summary' => 'تدريب وتطوير مؤسسي لعدة مشاركين مع منسق وتقارير أساسية.',
                'description' => 'للمدارس والمراكز والمؤسسات التعليمية: مقاعد متعددة، برامج تدريب، استشارات عند الحاجة، ومتابعة التنفيذ.',
                'features' => [
                    'عدة مشاركين تحت حساب الجهة',
                    'برامج تدريب وتطوير مهني',
                    'تنسيق مع مسؤول/منسق الجهة',
                    'تقارير مشاركة أساسية',
                    'استشارات مؤسسية عند الحاجة',
                ],
                'tools_resources' => [
                    'حزمة أدوات للمدرسة',
                    'قوالب متابعة للمنسق',
                ],
                'price' => 0,
                'currency' => 'QAR',
                'duration_days' => null,
                'consultation_sessions' => 4,
                'participant_seats' => 25,
                'includes_tools' => true,
                'cta_mode' => 'contact',
                'order' => 40,
                'is_active' => true,
                'is_featured' => false,
                'is_popular' => false,
            ],
            [
                'name' => 'باقة مخصصة',
                'slug' => 'custom',
                'package_type' => Package::TYPE_CUSTOM,
                'card_summary' => 'عرض يُبنى حسب احتياجكم: مسارات، تدريب، استشارات، وعدد مقاعد مرن.',
                'description' => 'للجهات التي تحتاج حلاً مخصصًا — نبدأ باستفسار ثم مقترح واتفاق على النطاق والسعر.',
                'features' => [
                    'تصميم حسب الاحتياج',
                    'دمج مسارات وخدمات واستشارات',
                    'عدد مقاعد مرن',
                    'متابعة وتنفيذ متفق عليه',
                ],
                'tools_resources' => [],
                'price' => 0,
                'currency' => 'QAR',
                'duration_days' => null,
                'consultation_sessions' => null,
                'participant_seats' => null,
                'includes_tools' => true,
                'cta_mode' => 'quote',
                'order' => 50,
                'is_active' => true,
                'is_featured' => false,
                'is_popular' => false,
            ],
        ];

        $pathIds = LearningPath::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->limit(6)
            ->pluck('id');

        foreach ($catalog as $row) {
            $row['track'] = $row['package_type'];

            $package = Package::query()->firstOrCreate(
                ['slug' => $row['slug']],
                $row
            );

            // Soft-fill optional catalog fields only when still empty (never overwrite admin edits).
            if (! $package->wasRecentlyCreated) {
                $soft = [];
                if ($package->tools_resources === null && ! empty($row['tools_resources'])) {
                    $soft['tools_resources'] = $row['tools_resources'];
                }
                if ($package->discount_note === null && ! empty($row['discount_note'])) {
                    $soft['discount_note'] = $row['discount_note'];
                }
                if ($soft !== []) {
                    $package->update($soft);
                }
            }

            if ($pathIds->isNotEmpty()
                && in_array($row['package_type'], [Package::TYPE_INDIVIDUAL, Package::TYPE_ADVANCED], true)
                && $package->learningPaths()->count() === 0
            ) {
                $attach = $row['package_type'] === Package::TYPE_ADVANCED
                    ? $pathIds->all()
                    : $pathIds->take(3)->all();
                $package->learningPaths()->syncWithoutDetaching($attach);
            }
        }
    }
}
