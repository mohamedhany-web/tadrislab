<?php

namespace Database\Seeders;

use App\Models\ConsultationService;
use App\Models\ConsultationSetting;
use Illuminate\Database\Seeder;

class ConsultationServicesSeeder extends Seeder
{
    public function run(): void
    {
        ConsultationSetting::current();

        $defaults = [
            [
                'slug' => 'teacher-individual-30',
                'consultation_type' => 'teacher_individual',
                'title_ar' => 'استشارة فردية للمعلم — 30 دقيقة',
                'title_en' => 'Individual teacher consultation — 30 min',
                'summary_ar' => 'جلسة فردية لمناقشة تحدٍ صفّي محدد والحصول على توصيات عملية.',
                'summary_en' => 'One-to-one session on a classroom challenge with practical recommendations.',
                'duration_minutes' => 30,
                'price' => 150,
                'is_published' => true,
                'is_mvp' => true,
                'sort_order' => 10,
            ],
            [
                'slug' => 'teacher-individual-60',
                'consultation_type' => 'teacher_individual',
                'title_ar' => 'استشارة فردية للمعلم — 60 دقيقة',
                'title_en' => 'Individual teacher consultation — 60 min',
                'summary_ar' => 'جلسة أعمق لخطة تطبيق ومتابعة قصيرة.',
                'summary_en' => 'Deeper session with an action plan.',
                'duration_minutes' => 60,
                'price' => 280,
                'is_published' => true,
                'is_mvp' => true,
                'sort_order' => 20,
            ],
            [
                'slug' => 'specialized-pedagogy',
                'consultation_type' => 'specialized',
                'title_ar' => 'استشارة تربوية متخصصة',
                'title_en' => 'Specialized educational consultation',
                'summary_ar' => 'استشارة في مجال تربوي متخصص (تقويم، إدارة صف، استراتيجيات…).',
                'summary_en' => 'Specialized pedagogy focus (assessment, classroom management, strategies…).',
                'duration_minutes' => 45,
                'price' => 220,
                'is_published' => true,
                'is_mvp' => true,
                'sort_order' => 30,
            ],
            [
                'slug' => 'institution-consult',
                'consultation_type' => 'institution',
                'title_ar' => 'استشارة للمدارس والمؤسسات',
                'title_en' => 'School / institution consultation',
                'summary_ar' => 'جلسة أولية لفهم احتياج الجهة وتحديد مسار الدعم.',
                'summary_en' => 'Initial session to scope institutional needs.',
                'duration_minutes' => 60,
                'price' => 500,
                'requires_instructor' => true,
                'is_published' => true,
                'is_mvp' => true,
                'sort_order' => 40,
            ],
            [
                'slug' => 'coaching-mentoring',
                'consultation_type' => 'coaching_mentoring',
                'title_ar' => 'توجيه / إرشاد (قريبًا)',
                'title_en' => 'Coaching / Mentoring (coming soon)',
                'summary_ar' => 'مسار توجيه مستمر — يُفعَّل لاحقًا.',
                'summary_en' => 'Ongoing coaching track — enabled later.',
                'duration_minutes' => 45,
                'price' => 0,
                'is_published' => false,
                'is_mvp' => false,
                'sort_order' => 90,
            ],
        ];

        foreach ($defaults as $row) {
            ConsultationService::query()->updateOrCreate(
                ['slug' => $row['slug']],
                array_merge([
                    'currency' => 'QAR',
                    'requires_instructor' => true,
                    'is_active' => true,
                    'description_ar' => null,
                    'description_en' => null,
                ], $row)
            );
        }
    }
}
