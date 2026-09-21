<?php

namespace Database\Seeders;

use App\Models\AdvancedCourse;
use App\Models\CourseCategory;
use App\Models\LearningPath;
use App\Models\LearningPathUnit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * بيانات واجهة قوية للمسارات والكورسات مع صور عالية الجودة.
 *
 * php artisan db:seed --class=TadrisHomeShowcaseSeeder
 * php artisan storage:link
 */
class TadrisHomeShowcaseSeeder extends Seeder
{
    private function u(string $photoPath): string
    {
        return 'https://images.unsplash.com/'.$photoPath.'?auto=format&fit=crop&w=1600&q=85&fm=jpg';
    }

    public function run(): void
    {
        if (! Schema::hasTable('learning_paths') || ! Schema::hasTable('advanced_courses')) {
            $this->command?->warn('جداول المسارات/الكورسات غير موجودة.');

            return;
        }

        $instructor = User::query()->where('role', 'instructor')->where('is_active', true)->first()
            ?? User::query()->where('role', 'teacher')->where('is_active', true)->first()
            ?? User::query()->where('role', 'admin')->where('is_active', true)->first()
            ?? User::query()->first();

        if (! $instructor) {
            $this->command?->error('لا يوجد مستخدم لربطه كمدرّب.');

            return;
        }

        $disk = Storage::disk('public');
        foreach (['showcase-paths', 'showcase-courses'] as $dir) {
            if (! $disk->exists($dir)) {
                $disk->makeDirectory($dir);
            }
        }

        $this->seedPaths($instructor->id);
        $this->seedCourses($instructor->id);

        $this->command?->info('تم تجهيز المسارات والكورسات بالصور. تأكد من: php artisan storage:link');
    }

    private function seedPaths(int $instructorId): void
    {
        $paths = [
            [
                'slug' => 'classroom-management',
                'title_ar' => 'إدارة الصف',
                'title_en' => 'Classroom Management',
                'skill_ar' => 'انضباط وتفاعل',
                'skill_en' => 'Discipline & engagement',
                'summary_ar' => 'ابنِ مناخًا صفّيًا آمنًا: قواعد واضحة، روتين يومي، وإشراك الطلاب بهدوء وثقة.',
                'summary_en' => 'Build a calm classroom climate: clear rules, daily routines, and confident engagement.',
                'minutes' => 180,
                'image' => $this->u('photo-1580582932707-520aed937b7b'),
                'units' => [
                    ['ar' => 'قواعد الصف والروتين', 'en' => 'Rules & routines'],
                    ['ar' => 'إدارة السلوك بهدوء', 'en' => 'Calm behavior management'],
                    ['ar' => 'إشراك الجميع', 'en' => 'Engage every student'],
                ],
            ],
            [
                'slug' => 'teaching-strategies',
                'title_ar' => 'استراتيجيات التدريس',
                'title_en' => 'Teaching Strategies',
                'skill_ar' => 'تنويع التعليم',
                'skill_en' => 'Differentiated instruction',
                'summary_ar' => 'اختر الاستراتيجية المناسبة للهدف: شرح، مجموعات، تعلم نشط، ودعم متنوع داخل الحصة.',
                'summary_en' => 'Match strategy to goal: explanation, groups, active learning, and in-class differentiation.',
                'minutes' => 210,
                'image' => $this->u('photo-1509062522246-3755977927d7'),
                'units' => [
                    ['ar' => 'التعلم النشط', 'en' => 'Active learning'],
                    ['ar' => 'العمل التعاوني', 'en' => 'Collaborative work'],
                    ['ar' => 'التمايز داخل الصف', 'en' => 'Classroom differentiation'],
                ],
            ],
            [
                'slug' => 'lesson-time-management',
                'title_ar' => 'إدارة وقت الحصة',
                'title_en' => 'Lesson Time Management',
                'skill_ar' => 'إيقاع الحصة',
                'skill_en' => 'Lesson pacing',
                'summary_ar' => 'وزّع وقت الحصة بذكاء: افتتاح، نشاط مركزي، تقويم سريع، وخاتمة تُثبّت التعلم.',
                'summary_en' => 'Pace the lesson well: opener, core activity, quick check, and a closing that sticks.',
                'minutes' => 150,
                'image' => $this->u('photo-1434030216411-0b793f4b4173'),
                'units' => [
                    ['ar' => 'تخطيط الإيقاع', 'en' => 'Pacing plan'],
                    ['ar' => 'الانتقالات السلسة', 'en' => 'Smooth transitions'],
                ],
            ],
            [
                'slug' => 'lesson-planning',
                'title_ar' => 'التخطيط للدرس',
                'title_en' => 'Lesson Planning',
                'skill_ar' => 'أهداف وأنشطة',
                'skill_en' => 'Goals & activities',
                'summary_ar' => 'صمّم درسًا قابلًا للتنفيذ: هدف واضح، أنشطة مرتبطة، ومواد جاهزة قبل الدخول للصف.',
                'summary_en' => 'Design a teachable lesson: clear goal, aligned activities, and materials ready before class.',
                'minutes' => 200,
                'image' => $this->u('photo-1454165804606-c3d57bc86b40'),
                'units' => [
                    ['ar' => 'صياغة الأهداف', 'en' => 'Writing objectives'],
                    ['ar' => 'تصميم الأنشطة', 'en' => 'Designing activities'],
                    ['ar' => 'تجهيز المواد', 'en' => 'Preparing materials'],
                ],
            ],
            [
                'slug' => 'assessment',
                'title_ar' => 'التقويم وقياس التعلم',
                'title_en' => 'Assessment',
                'skill_ar' => 'تقييم تكويني',
                'skill_en' => 'Formative assessment',
                'summary_ar' => 'قِس الفهم أثناء الحصة وبعدها بأدوات بسيطة: أسئلة فورية، بطاقات خروج، وتغذية راجعة.',
                'summary_en' => 'Measure understanding during and after class with simple tools: checks, exit tickets, feedback.',
                'minutes' => 190,
                'image' => $this->u('photo-1606326608606-aa0b62935f2b'),
                'units' => [
                    ['ar' => 'تقييم تكويني سريع', 'en' => 'Quick formative checks'],
                    ['ar' => 'التغذية الراجعة', 'en' => 'Feedback that helps'],
                ],
            ],
            [
                'slug' => 'teacher-skills',
                'title_ar' => 'مهارات المعلم',
                'title_en' => 'Teacher Skills',
                'skill_ar' => 'تواصل وحضور',
                'skill_en' => 'Presence & communication',
                'summary_ar' => 'طوّر حضورك المهني: لغة جسد، شرح واضح، إدارة أسئلة الطلاب، وثقة أمام الصف.',
                'summary_en' => 'Grow professional presence: body language, clear explanation, Q&A, and classroom confidence.',
                'minutes' => 170,
                'image' => $this->u('photo-1577896851231-70ef18881754'),
                'units' => [
                    ['ar' => 'التواصل الفعّال', 'en' => 'Effective communication'],
                    ['ar' => 'إدارة الحوار', 'en' => 'Facilitating discussion'],
                ],
            ],
            [
                'slug' => 'professional-development',
                'title_ar' => 'التطوير المهني للمعلم',
                'title_en' => 'Professional Development',
                'skill_ar' => 'نمو مستمر',
                'skill_en' => 'Continuous growth',
                'summary_ar' => 'ابنِ خطة تطوير شخصية: تشخيص احتياج، ممارسة مقصودة، وتأمل بعد الحصة.',
                'summary_en' => 'Build a personal growth plan: diagnose needs, deliberate practice, and post-lesson reflection.',
                'minutes' => 160,
                'image' => $this->u('photo-1524178232363-1fb2b075b655'),
                'units' => [
                    ['ar' => 'تشخيص الاحتياج', 'en' => 'Needs diagnosis'],
                    ['ar' => 'الممارسة المقصودة', 'en' => 'Deliberate practice'],
                    ['ar' => 'التأمل والقياس', 'en' => 'Reflect & measure'],
                ],
            ],
        ];

        $order = 1;
        foreach ($paths as $row) {
            $thumbRel = 'showcase-paths/'.$row['slug'].'.jpg';
            $relative = $this->downloadImage($row['image'], $thumbRel);

            $path = LearningPath::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'title_ar' => $row['title_ar'],
                    'title_en' => $row['title_en'],
                    'skill_focus_ar' => $row['skill_ar'],
                    'skill_focus_en' => $row['skill_en'],
                    'summary_ar' => $row['summary_ar'],
                    'summary_en' => $row['summary_en'],
                    'description_ar' => $row['summary_ar'].' مسار تطوير مهني عملي من تدريس لاب.',
                    'description_en' => $row['summary_en'].' A practical TADRIS LAB professional path.',
                    'estimated_minutes' => $row['minutes'],
                    'sort_order' => $order++,
                    'is_active' => true,
                    'is_published' => true,
                    'instructor_id' => $instructorId,
                    'thumbnail' => $relative ?: $row['image'],
                    'currency' => 'QAR',
                    'is_sellable_standalone' => true,
                ]
            );

            if ($path->units()->count() === 0) {
                $uOrder = 1;
                foreach ($row['units'] as $unit) {
                    LearningPathUnit::query()->create([
                        'learning_path_id' => $path->id,
                        'title_ar' => $unit['ar'],
                        'title_en' => $unit['en'],
                        'summary_ar' => 'وحدة تطبيقية ضمن مسار '.$row['title_ar'].'.',
                        'summary_en' => 'A practical unit within the '.$row['title_en'].' path.',
                        'sort_order' => $uOrder++,
                        'is_active' => true,
                    ]);
                }
            }

            $this->command?->info('✓ مسار: '.$row['title_ar']);
        }
    }

    private function seedCourses(int $instructorId): void
    {
        $cat = CourseCategory::query()->firstOrCreate(
            ['name' => 'تطوير مهني للمعلمين'],
            ['sort_order' => 1, 'is_active' => true]
        );

        $courses = [
            [
                'slug' => 'effective-digital-teaching',
                'title' => 'التدريس الفعّال في الصف الرقمي',
                'description' => 'مبادئ تخطيط الحصة أونلاين، إدارة الوقت، والتفاعل مع الطلاب مع الحفاظ على جودة التعلّم.',
                'level' => 'beginner',
                'hours' => 24,
                'price' => 0,
                'is_free' => true,
                'is_featured' => true,
                'image' => $this->u('photo-1516321318423-f06f85e504b3'),
            ],
            [
                'slug' => 'lesson-goals-planning',
                'title' => 'تخطيط الحصص وأهداف التعلّم',
                'description' => 'كتابة أهداف قابلة للقياس، اختيار الأنشطة، وربط التقييم بالهدف داخل بيئة تعليمية عربية.',
                'level' => 'beginner',
                'hours' => 18,
                'price' => 249,
                'is_free' => false,
                'is_featured' => true,
                'image' => $this->u('photo-1503676260728-1c00da094a0b'),
            ],
            [
                'slug' => 'classroom-assessment-feedback',
                'title' => 'التقييم الصفي والتغذية الراجعة',
                'description' => 'أساليب التقييم أثناء الحصة، التغذية الراجعة البناءة، ومتابعة تقدّم المتعلّم دون إرهاقه.',
                'level' => 'intermediate',
                'hours' => 20,
                'price' => 299,
                'is_free' => false,
                'is_featured' => true,
                'image' => $this->u('photo-1427504494785-3a9ca7044f45'),
            ],
            [
                'slug' => 'virtual-classroom-engagement',
                'title' => 'إدارة الصف الافتراضي والتفاعل',
                'description' => 'قواعد الانضباط الرقمي، إشراك الطلاب الصامتين، وإدارة النقاش والعمل الجماعي عن بُعد.',
                'level' => 'intermediate',
                'hours' => 22,
                'price' => 329,
                'is_free' => false,
                'is_featured' => true,
                'image' => $this->u('photo-1523240795612-9a054b0db644'),
            ],
            [
                'slug' => 'teacher-professional-presence',
                'title' => 'بناء حضورك المهني كمعلم',
                'description' => 'عرض الخبرة، التواصل مع المتعلمين، والمحافظة على ثقة الجمهور في برامجك التدريبية.',
                'level' => 'intermediate',
                'hours' => 16,
                'price' => 279,
                'is_free' => false,
                'is_featured' => false,
                'image' => $this->u('photo-1522202176988-66273c2fd55f'),
            ],
            [
                'slug' => 'safe-digital-tools-teaching',
                'title' => 'أدوات رقمية آمنة في خدمة التعليم',
                'description' => 'اختيار أدوات مناسبة للمرحلة، خصوصية الطلاب، وترشيد استخدام الشاشات داخل الحصة وخارجها.',
                'level' => 'beginner',
                'hours' => 14,
                'price' => 199,
                'is_free' => false,
                'is_featured' => false,
                'image' => $this->u('photo-1542744173-8e7e53415bb0'),
            ],
        ];

        foreach ($courses as $row) {
            $thumbRel = 'showcase-courses/'.$row['slug'].'.jpg';
            $relative = $this->downloadImage($row['image'], $thumbRel);

            $existing = AdvancedCourse::query()
                ->where(function ($q) use ($row) {
                    $q->where('title', $row['title'])
                        ->orWhere('thumbnail', 'like', '%'.$row['slug'].'.jpg');
                })
                ->first();

            $payload = [
                'instructor_id' => $instructorId,
                'title' => $row['title'],
                'description' => $row['description'],
                'objectives' => 'أهداف واضحة قابلة للقياس ضمن مسار الكورس.',
                'level' => $row['level'],
                'duration_hours' => $row['hours'],
                'duration_minutes' => (int) $row['hours'] * 60,
                'price' => $row['price'],
                'price_after_discount' => $row['is_free'] ? 0 : max(0, (float) $row['price'] - 50),
                'is_free' => $row['is_free'],
                'is_featured' => $row['is_featured'],
                'is_active' => true,
                'thumbnail' => $relative ?: $row['image'],
                'course_category_id' => $cat->id,
                'category' => 'تطوير مهني للمعلمين',
                'language' => 'ar',
                'requirements' => 'لا متطلبات مسبقة للمستوى المبتدئ.',
                'what_you_learn' => 'ممارسات صفية قابلة للتطبيق فورًا مع أدوات متابعة بسيطة.',
                'rating' => round(4.3 + (crc32($row['slug']) % 7) / 10, 1),
                'reviews_count' => 18 + (crc32($row['slug']) % 60),
                'students_count' => 80 + (crc32($row['slug']) % 320),
            ];

            if ($existing) {
                $existing->update($payload);
                $this->command?->info('✓ حُدّث كورس: '.$row['title']);
            } else {
                AdvancedCourse::query()->create($payload);
                $this->command?->info('✓ أُنشئ كورس: '.$row['title']);
            }
        }
    }

    private function downloadImage(string $url, string $relativePath): ?string
    {
        try {
            if (Storage::disk('public')->exists($relativePath)
                && Storage::disk('public')->size($relativePath) > 5000) {
                return $relativePath;
            }

            $response = Http::timeout(90)
                ->withHeaders([
                    'Accept' => 'image/jpeg,image/webp,*/*',
                    'User-Agent' => 'TADRIS-LAB-HomeShowcase/1.0',
                ])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $body = $response->body();
            if ($body === '' || strlen($body) < 5000) {
                return null;
            }

            Storage::disk('public')->put($relativePath, $body);

            return $relativePath;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}
