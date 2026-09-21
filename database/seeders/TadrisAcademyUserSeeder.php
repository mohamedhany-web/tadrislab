<?php

namespace Database\Seeders;

use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class TadrisAcademyUserSeeder extends Seeder
{
    /**
     * بيانات مستخدمي منصة تدريس لاب — تأهيل المعلمين للعمل أونلاين
     */
    public function run(): void
    {
        if (! Schema::hasTable('users')) {
            $this->command->warn('⚠️  جدول users غير موجود. يرجى تشغيل migrations أولاً.');

            return;
        }

        $password = Hash::make('password123');

        User::firstOrCreate(
            ['email' => 'admin@tadrislab.com'],
            [
                'name' => 'مدير منصة تدريس لاب',
                'phone' => '0500000000',
                'password' => $password,
                'role' => 'super_admin',
                'is_active' => true,
                'bio' => 'مدير المنصة — مسؤول عن إدارة أكاديمية تأهيل المعلمين للعمل أونلاين.',
            ]
        );

        User::firstOrCreate(
            ['email' => 'academy@tadrislab.com'],
            [
                'name' => 'سارة المديرة الأكاديمية',
                'phone' => '0500000001',
                'password' => $password,
                'role' => 'super_admin',
                'is_active' => true,
                'bio' => 'مديرة أكاديمية — متابعة البرامج التدريبية والشهادات والتوظيف.',
            ]
        );

        $instructors = [
            [
                'email' => 'instructor1@tadrislab.com',
                'phone' => '0500000010',
                'name' => 'د. أحمد الشمري',
                'bio' => 'مدرب معتمد في التدريس أونلاين — خبرة 12 سنة. متخصص في تصميم الحصص التفاعلية واستخدام أدوات التعلم الرقمي.',
            ],
            [
                'email' => 'instructor2@tadrislab.com',
                'phone' => '0500000011',
                'name' => 'نورة العتيبي',
                'bio' => 'معلمة لغة عربية أونلاين — تدريب المعلمين على تقديم حصص افتراضية احترافية وبناء البروفايل المهني.',
            ],
            [
                'email' => 'instructor3@tadrislab.com',
                'phone' => '0500000012',
                'name' => 'محمد المنصوري',
                'bio' => 'خبير في أدوات الذكاء الاصطناعي للمعلمين — ورش عملية على تحضير الدروس والأنشطة باستخدام AI.',
            ],
            [
                'email' => 'instructor4@tadrislab.com',
                'phone' => '0500000013',
                'name' => 'هدى الكويتية',
                'bio' => 'مدربة في التسويق للمعلمين والعمل بالدولار — مسارات تعلم للوصول لفرص عمل دولية.',
            ],
        ];

        foreach ($instructors as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => $password,
                    'role' => 'instructor',
                    'is_active' => true,
                    'bio' => $data['bio'],
                ]
            );

            if (Schema::hasTable('instructor_profiles') && ! $user->instructorProfile) {
                InstructorProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'headline' => $data['bio'],
                        'bio' => $data['bio'],
                        'experience' => 'خبرة في التدريب وتأهيل المعلمين',
                        'skills' => 'التدريس أونلاين، أدوات رقمية، تصميم الدروس',
                        'status' => InstructorProfile::STATUS_APPROVED,
                        'submitted_at' => now(),
                        'reviewed_at' => now(),
                    ]
                );
            }
        }

        $students = [
            ['email' => 'student1@tadrislab.com', 'phone' => '0500000020', 'name' => 'فاطمة الزهراء'],
            ['email' => 'student2@tadrislab.com', 'phone' => '0500000021', 'name' => 'عمر الطالب'],
            ['email' => 'student3@tadrislab.com', 'phone' => '0500000022', 'name' => 'مريم المعلمة المتدربة'],
            ['email' => 'student4@tadrislab.com', 'phone' => '0500000023', 'name' => 'خالد السعيد'],
            ['email' => 'student5@tadrislab.com', 'phone' => '0500000024', 'name' => 'لينا أحمد'],
            ['email' => 'student6@tadrislab.com', 'phone' => '0500000025', 'name' => 'يوسف المعلم'],
        ];

        foreach ($students as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => $password,
                    'role' => 'student',
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✅ تم إنشاء مستخدمي تدريس لاب بنجاح.');
        $this->command->newLine();
        $this->command->info('📋 بيانات الدخول (كلمة المرور لجميع الحسابات: password123)');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('👨‍💼 مدير المنصة:     admin@tadrislab.com     — 0500000000');
        $this->command->info('👩‍💼 مديرة أكاديمية: academy@tadrislab.com   — 0500000001');
        $this->command->info('👨‍🏫 مدربون:          instructor1@tadrislab.com … instructor4@tadrislab.com');
        $this->command->info('👩‍🎓 طلاب:            student1@tadrislab.com … student6@tadrislab.com');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
