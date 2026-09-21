<?php

namespace Database\Seeders;

use App\Models\LearningPath;
use App\Models\Package;
use App\Models\TeacherTool;
use Illuminate\Database\Seeder;

class TeacherToolsSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            [
                'slug' => 'lesson-plan-template',
                'tool_type' => TeacherTool::TYPE_TEMPLATES,
                'title_ar' => 'قالب تخطيط درس',
                'title_en' => 'Lesson plan template',
                'summary_ar' => 'نموذج جاهز لتخطيط درس يومي بأهداف وأنشطة وتقويم.',
                'access_mode' => TeacherTool::ACCESS_FREE,
                'sort_order' => 10,
            ],
            [
                'slug' => 'classroom-management-checklist',
                'tool_type' => TeacherTool::TYPE_CHECKLISTS,
                'title_ar' => 'قائمة تحقق لإدارة الصف',
                'title_en' => 'Classroom management checklist',
                'summary_ar' => 'بنود سريعة قبل الحصة وأثناءها وبعدها.',
                'access_mode' => TeacherTool::ACCESS_LOGIN,
                'sort_order' => 20,
            ],
            [
                'slug' => 'exit-ticket-tool',
                'tool_type' => TeacherTool::TYPE_CLASSROOM,
                'title_ar' => 'أداة تذكرة الخروج',
                'title_en' => 'Exit ticket classroom tool',
                'summary_ar' => 'أداة صفية سريعة لقياس الفهم في نهاية الحصة.',
                'access_mode' => TeacherTool::ACCESS_PACKAGE,
                'sort_order' => 30,
            ],
            [
                'slug' => 'unit-planning-board',
                'tool_type' => TeacherTool::TYPE_PLANNING,
                'title_ar' => 'لوحة تخطيط وحدة',
                'title_en' => 'Unit planning board',
                'summary_ar' => 'تخطيط وحدة دراسية عبر أسابيع وأهداف ومخرجات.',
                'access_mode' => TeacherTool::ACCESS_PATH,
                'sort_order' => 40,
            ],
            [
                'slug' => 'formative-assessment-bank',
                'tool_type' => TeacherTool::TYPE_ASSESSMENT,
                'title_ar' => 'بنك أفكار تقويم تكويني',
                'title_en' => 'Formative assessment ideas',
                'summary_ar' => 'موارد تقويم قصيرة قابلة للتطبيق داخل الصف.',
                'access_mode' => TeacherTool::ACCESS_PACKAGE,
                'sort_order' => 50,
            ],
            [
                'slug' => 'printable-rubric-pack',
                'tool_type' => TeacherTool::TYPE_DOWNLOADABLE,
                'title_ar' => 'حزمة معايير تقييم قابلة للطباعة',
                'title_en' => 'Printable rubric pack',
                'summary_ar' => 'مورد قابل للتحميل لمعايير تقييم واضحة.',
                'access_mode' => TeacherTool::ACCESS_LOGIN,
                'sort_order' => 60,
            ],
        ];

        $pathIds = LearningPath::query()->where('is_active', true)->orderBy('id')->limit(3)->pluck('id');
        $packageIds = Package::query()->whereIn('package_type', ['individual', 'advanced'])->pluck('id');

        foreach ($catalog as $row) {
            $tool = TeacherTool::query()->firstOrCreate(
                ['slug' => $row['slug']],
                array_merge($row, [
                    'is_active' => true,
                    'is_published' => true,
                    'currency' => platform_currency(),
                ])
            );

            if ($tool->wasRecentlyCreated || $tool->learningPaths()->count() === 0) {
                if (in_array($row['access_mode'], [TeacherTool::ACCESS_PATH, TeacherTool::ACCESS_PACKAGE], true) && $pathIds->isNotEmpty()) {
                    $tool->learningPaths()->syncWithoutDetaching(
                        $pathIds->mapWithKeys(fn ($id, $i) => [$id => ['sort_order' => ($i + 1) * 10]])->all()
                    );
                }
            }

            if (($tool->wasRecentlyCreated || $tool->packages()->count() === 0)
                && in_array($row['access_mode'], [TeacherTool::ACCESS_PACKAGE], true)
                && $packageIds->isNotEmpty()
            ) {
                $tool->packages()->syncWithoutDetaching(
                    $packageIds->mapWithKeys(fn ($id, $i) => [$id => ['sort_order' => ($i + 1) * 10]])->all()
                );
            }
        }
    }
}
