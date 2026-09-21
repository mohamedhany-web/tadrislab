<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 بدء عملية Seed للبيانات...');
        $this->command->newLine();

        if (\Illuminate\Support\Facades\Schema::hasTable('permissions') && \Illuminate\Support\Facades\Schema::hasTable('roles')) {
            $this->command->info('📋 1. إنشاء الأدوار والصلاحيات...');
            $this->call([
                PermissionsAndRolesSeeder::class,
                PermissionsSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء الأدوار والصلاحيات');
            $this->command->newLine();
        } else {
            $this->command->warn('⚠️  جداول permissions/roles غير موجودة. سيتم تخطي هذا الخطوة.');
            $this->command->newLine();
        }

        $this->command->info('📚 2. إنشاء السنوات الأكاديمية والمواد...');
        $this->call([
            AcademicYearSeeder::class,
            SubjectsSeeder::class,
            SchoolProgramSeeder::class,
        ]);
        $this->command->info('✅ تم إنشاء السنوات الأكاديمية والمواد + برنامج المدرسة');
        $this->command->newLine();

        if (\Illuminate\Support\Facades\Schema::hasTable('employee_jobs')) {
            $this->command->info('💼 3. إنشاء الوظائف الثابتة للموظفين...');
            $this->call([
                EmployeeJobSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء الوظائف الثابتة');
            $this->command->newLine();
        }

        $this->command->info('👥 4. إنشاء مستخدمي تدريس لاب...');
        $this->call([
            TadrisAcademyUserSeeder::class,
        ]);
        $this->command->info('✅ تم إنشاء المستخدمين');
        $this->command->newLine();

        if (\Illuminate\Support\Facades\Schema::hasTable('wallets') && \Illuminate\Support\Facades\Schema::hasTable('orders')) {
            $this->command->info('💰 5. إنشاء نظام المحاسبة...');
            try {
                $this->call([
                    AccountingSystemSeeder::class,
                ]);
                $this->command->info('✅ تم إنشاء نظام المحاسبة');
            } catch (\Exception $e) {
                $this->command->warn('⚠️  فشل إنشاء بيانات المحاسبة: '.$e->getMessage());
            }
            $this->command->newLine();
        } else {
            $this->command->warn('⚠️  جداول المحاسبة غير موجودة. سيتم تخطي هذا الخطوة.');
            $this->command->newLine();
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('message_templates')) {
            $this->command->info('📧 6. إنشاء قوالب الرسائل...');
            $this->call([
                MessageTemplateSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء قوالب الرسائل');
            $this->command->newLine();
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('referral_programs')) {
            $this->command->info('🎁 7. إنشاء برامج الإحالة...');
            $this->call([
                ReferralProgramSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء برامج الإحالة');
            $this->command->newLine();
        }

        // Non-interactive: seed catalog content by default (no confirm prompts)
        if (\Illuminate\Support\Facades\Schema::hasTable('packages')) {
            $this->command->info('📦 باقات ومسارات وأدوات واستشارات...');
            $this->call([
                TadrisPackagesSeeder::class,
                LearningPathsSeeder::class,
                TadrisPathContentSeeder::class,
                TeacherToolsSeeder::class,
                ConsultationServicesSeeder::class,
            ]);
            $this->command->info('✅ تم تجهيز باقات المنصة ومسارات التعلم وخدمات الاستشارات');
            $this->command->newLine();
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('advanced_courses')) {
            $this->command->info('🖼️ واجهة العرض الرئيسية...');
            try {
                $this->call([
                    TadrisHomeShowcaseSeeder::class,
                ]);
            } catch (\Exception $e) {
                $this->command->warn('⚠️  Showcase: '.$e->getMessage());
            }
            $this->command->newLine();
        }

        try {
            $this->call([TeacherPlatformFaqSeeder::class]);
        } catch (\Throwable $e) {
            $this->command->warn('⚠️  FAQ: '.$e->getMessage());
        }

        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✨ تم إكمال عملية Seed بنجاح!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->newLine();
        $this->command->info('📋 بيانات الدخول الافتراضية (كلمة المرور: password123):');
        $this->command->info('   👨‍💼 مدير المنصة: admin@tadrislab.com أو 0500000000');
        $this->command->info('   👩‍💼 مديرة أكاديمية: academy@tadrislab.com أو 0500000001');
        $this->command->info('   👨‍🏫 مدرب: instructor1@tadrislab.com أو 0500000010');
        $this->command->info('   👩‍🎓 طالب: student1@tadrislab.com أو 0500000020');
        $this->command->newLine();
    }
}
