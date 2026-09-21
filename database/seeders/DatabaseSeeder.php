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

        // 1. إنشاء الأدوار والصلاحيات أولاً (إذا كانت الجداول موجودة)
        if (\Illuminate\Support\Facades\Schema::hasTable('permissions') && \Illuminate\Support\Facades\Schema::hasTable('roles')) {
            $this->command->info('📋 1. إنشاء الأدوار والصلاحيات...');
            $this->call([
                PermissionsAndRolesSeeder::class,
                // Seeder إضافي يحتوي على صلاحيات كثيرة بأسماء مختلفة مستخدمة في أجزاء من النظام
                PermissionsSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء الأدوار والصلاحيات');
            $this->command->newLine();
        } else {
            $this->command->warn('⚠️  جداول permissions/roles غير موجودة. سيتم تخطي هذا الخطوة.');
            $this->command->newLine();
        }

        // 2. إنشاء السنوات الأكاديمية والمواد
        $this->command->info('📚 2. إنشاء السنوات الأكاديمية والمواد...');
        $this->call([
            AcademicYearSeeder::class,
            SubjectsSeeder::class,
            SchoolProgramSeeder::class,
        ]);
        $this->command->info('✅ تم إنشاء السنوات الأكاديمية والمواد + برنامج المدرسة');
        $this->command->newLine();

        // 3. الوظائف الثابتة للموظفين (محاسب، اشراف عام، HR، مشرفه، سيلز)
        if (\Illuminate\Support\Facades\Schema::hasTable('employee_jobs')) {
            $this->command->info('💼 3. إنشاء الوظائف الثابتة للموظفين...');
            $this->call([
                EmployeeJobSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء الوظائف الثابتة');
            $this->command->newLine();
        }

        // 4. إنشاء مستخدمي تدريس لاب
        $this->command->info('👥 4. إنشاء مستخدمي تدريس لاب...');
        $this->call([
            TADRIS LABAcademyUserSeeder::class,
        ]);
        $this->command->info('✅ تم إنشاء المستخدمين');
        $this->command->newLine();

        // 5. إنشاء نظام المحاسبة (اختياري - يحتاج كورسات وطلاب)
        if (\Illuminate\Support\Facades\Schema::hasTable('wallets') && \Illuminate\Support\Facades\Schema::hasTable('orders')) {
            $this->command->info('💰 5. إنشاء نظام المحاسبة...');
            try {
                $this->call([
                    AccountingSystemSeeder::class,
                ]);
                $this->command->info('✅ تم إنشاء نظام المحاسبة');
            } catch (\Exception $e) {
                $this->command->warn('⚠️  فشل إنشاء بيانات المحاسبة: ' . $e->getMessage());
            }
            $this->command->newLine();
        } else {
            $this->command->warn('⚠️  جداول المحاسبة غير موجودة. سيتم تخطي هذا الخطوة.');
            $this->command->newLine();
        }

        // 6. إنشاء قوالب الرسائل
        if (\Illuminate\Support\Facades\Schema::hasTable('message_templates')) {
            $this->command->info('📧 6. إنشاء قوالب الرسائل...');
            $this->call([
                MessageTemplateSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء قوالب الرسائل');
            $this->command->newLine();
        } else {
            $this->command->warn('⚠️  جدول message_templates غير موجود. سيتم تخطي هذا الخطوة.');
            $this->command->newLine();
        }

        // 7. إنشاء برامج الإحالة
        if (\Illuminate\Support\Facades\Schema::hasTable('referral_programs')) {
            $this->command->info('🎁 7. إنشاء برامج الإحالة...');
            $this->call([
                ReferralProgramSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء برامج الإحالة');
            $this->command->newLine();
        } else {
            $this->command->warn('⚠️  جدول referral_programs غير موجود. سيتم تخطي هذا الخطوة.');
            $this->command->newLine();
        }

        // 8. إنشاء كورسات تجريبية (اختياري)
        if ($this->command->confirm('هل تريد إنشاء كورسات تجريبية؟', false)) {
            $this->command->info('📖 8. إنشاء كورسات تجريبية...');
            $this->call([
                CoursesSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء كورسات تجريبية');
            $this->command->newLine();
        }

        // 8b. دورات واجهة تجريبية بصور عالية الجودة (اختياري — يحتاج اتصالاً لتنزيل الصور)
        if ($this->command->confirm('هل تريد إضافة/تحديث دورات الواجهة التجريبية مع الصور؟', false)) {
            $this->command->info('🖼️  تشغيل ShowcaseDemoCoursesSeeder...');
            $this->call([
                ShowcaseDemoCoursesSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء/تحديث دورات الواجهة التجريبية');
            $this->command->newLine();
        }

        // 8c. باقات تدريس لاب العامة (/pricing)
        if (\Illuminate\Support\Facades\Schema::hasTable('packages')) {
            $this->command->info('📦 باقات تدريس لاب...');
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

        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✨ تم إكمال عملية Seed بنجاح!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->newLine();
        $this->command->info('📋 بيانات الدخول الافتراضية (كلمة المرور: password123):');
        $this->command->info('   👨‍💼 مدير المنصة: admin@TADRIS LAB.com أو 0500000000');
        $this->command->info('   👩‍💼 مديرة أكاديمية: academy@TADRIS LAB.com أو 0500000001');
        $this->command->info('   👨‍🏫 مدرب: instructor1@TADRIS LAB.com أو 0500000010');
        $this->command->info('   👩‍🎓 طالب: student1@TADRIS LAB.com أو 0500000020');
        $this->command->newLine();
    }
}
