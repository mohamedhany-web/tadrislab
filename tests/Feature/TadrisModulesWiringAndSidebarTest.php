<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Institution;
use App\Models\InstitutionProgram;
use App\Models\LearningPath;
use App\Models\LearningPathLesson;
use App\Models\LearningPathPractice;
use App\Models\LearningPathUnit;
use App\Models\NotificationDelivery;
use App\Models\Package;
use App\Models\TeacherPathEnrollment;
use App\Models\TeacherPathProgressItem;
use App\Models\User;
use App\Services\InquiryService;
use App\Support\AdminNav;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class TadrisModulesWiringAndSidebarTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->ensureDomainTables();
        config(['admin_nav.enabled' => true, 'admin_nav.show_legacy_ops' => false]);
    }

    protected function ensureDomainTables(): void
    {
        if (! Schema::hasTable('packages')) {
            Schema::create('packages', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->text('description')->nullable();
                $table->text('card_summary')->nullable();
                $table->text('features')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->decimal('original_price', 10, 2)->nullable();
                $table->string('currency', 8)->nullable();
                $table->string('track')->nullable();
                $table->string('package_type')->nullable();
                $table->string('thumbnail')->nullable();
                $table->integer('duration_days')->nullable();
                $table->integer('courses_count')->default(0);
                $table->unsignedInteger('consultation_sessions')->default(0);
                $table->unsignedInteger('participant_seats')->nullable();
                $table->boolean('includes_tools')->default(false);
                $table->json('tools_resources')->nullable();
                $table->string('discount_note')->nullable();
                $table->string('cta_mode')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_popular')->default(false);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('package_course')) {
            Schema::create('package_course', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id');
                $table->unsignedBigInteger('course_id');
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('learning_paths')) {
            Schema::create('learning_paths', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->string('skill_focus_ar')->nullable();
                $table->string('skill_focus_en')->nullable();
                $table->string('summary_ar', 500)->nullable();
                $table->string('summary_en', 500)->nullable();
                $table->text('description_ar')->nullable();
                $table->text('description_en')->nullable();
                $table->string('thumbnail')->nullable();
                $table->unsignedInteger('estimated_minutes')->nullable();
                $table->decimal('price', 10, 2)->nullable();
                $table->string('currency', 8)->nullable();
                $table->boolean('is_sellable_standalone')->default(false);
                $table->unsignedInteger('access_days')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_published')->default(false);
                $table->foreignId('instructor_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('learning_path_units')) {
            Schema::create('learning_path_units', function (Blueprint $table) {
                $table->id();
                $table->foreignId('learning_path_id');
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->string('summary_ar', 500)->nullable();
                $table->string('summary_en', 500)->nullable();
                $table->unsignedInteger('estimated_minutes')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('learning_path_lessons')) {
            Schema::create('learning_path_lessons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('learning_path_unit_id');
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->string('content_type', 32)->default('text');
                $table->longText('body_ar')->nullable();
                $table->longText('body_en')->nullable();
                $table->string('video_url')->nullable();
                $table->string('file_path')->nullable();
                $table->string('external_url')->nullable();
                $table->unsignedInteger('duration_minutes')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_preview')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('learning_path_practices')) {
            Schema::create('learning_path_practices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('learning_path_unit_id');
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->string('summary_ar', 500)->nullable();
                $table->string('summary_en', 500)->nullable();
                $table->string('practice_type', 32)->default('application');
                $table->longText('body_ar')->nullable();
                $table->longText('body_en')->nullable();
                $table->string('resource_url')->nullable();
                $table->string('file_path')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('package_learning_path')) {
            Schema::create('package_learning_path', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id');
                $table->foreignId('learning_path_id');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('teacher_path_enrollments')) {
            Schema::create('teacher_path_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('learning_path_id');
                $table->foreignId('package_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->string('status', 32)->default('active');
                $table->decimal('progress', 5, 2)->default(0);
                $table->timestamp('enrolled_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->unsignedBigInteger('activated_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('teacher_path_progress_items')) {
            Schema::create('teacher_path_progress_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('learning_path_id');
                $table->string('item_type', 32);
                $table->unsignedBigInteger('item_id');
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('institutions')) {
            Schema::create('institutions', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name_ar');
                $table->string('name_en')->nullable();
                $table->string('org_type', 40)->default('school');
                $table->string('contact_name')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_phone')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('institution_programs')) {
            Schema::create('institution_programs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institution_id');
                $table->string('service_key')->nullable();
                $table->string('program_kind', 32)->default('training');
                $table->string('title_ar')->nullable();
                $table->string('title_en')->nullable();
                $table->string('status', 32)->default('inquiry');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('inquiries')) {
            Schema::create('inquiries', function (Blueprint $table) {
                $table->id();
                $table->string('reference', 32)->nullable()->unique();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone', 40)->nullable();
                $table->string('inquiry_type', 40)->default('general');
                $table->string('status', 24)->default('new');
                $table->string('source', 32)->default('contact_form');
                $table->foreignId('user_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('consultation_request_id')->nullable();
                $table->foreignId('institution_id')->nullable();
                $table->foreignId('institution_program_id')->nullable();
                $table->unsignedBigInteger('contact_message_id')->nullable();
                $table->string('subject', 255)->nullable();
                $table->text('message')->nullable();
                $table->text('admin_notes')->nullable();
                $table->foreignId('assigned_to')->nullable();
                $table->timestamp('inquired_at')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
            });
        } elseif (! Schema::hasColumn('inquiries', 'institution_program_id')) {
            Schema::table('inquiries', function (Blueprint $table) {
                $table->foreignId('institution_program_id')->nullable();
            });
        }

        if (! Schema::hasTable('notification_deliveries')) {
            Schema::create('notification_deliveries', function (Blueprint $table) {
                $table->id();
                $table->string('event_key', 80);
                $table->string('channel', 32);
                $table->string('status', 32);
                $table->nullableMorphs('notifiable');
                $table->string('recipient_email')->nullable();
                $table->string('recipient_phone', 40)->nullable();
                $table->string('recipient_name')->nullable();
                $table->string('subject')->nullable();
                $table->text('body')->nullable();
                $table->json('provider_response')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
            });
        }
    }

    private function makeAdmin(): User
    {
        return User::query()->create([
            'name' => 'Admin Wire',
            'email' => 'admin-wire-'.uniqid().'@example.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    private function makeStudent(): User
    {
        return User::query()->create([
            'name' => 'Teacher Learner',
            'email' => 'learner-'.uniqid().'@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);
    }

    /** @return array{0: LearningPath, 1: LearningPathUnit, 2: LearningPathLesson, 3: LearningPathPractice} */
    private function seedPathContent(): array
    {
        $path = LearningPath::query()->create([
            'slug' => 'classroom-mgmt-'.uniqid(),
            'title_ar' => 'إدارة الصف',
            'title_en' => 'Classroom Management',
            'is_active' => true,
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $unit = LearningPathUnit::query()->create([
            'learning_path_id' => $path->id,
            'title_ar' => 'وحدة القواعد',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $lesson = LearningPathLesson::query()->create([
            'learning_path_unit_id' => $unit->id,
            'title_ar' => 'درس التوقعات',
            'content_type' => 'text',
            'body_ar' => '<p>محتوى الدرس للمعلم</p>',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $practice = LearningPathPractice::query()->create([
            'learning_path_unit_id' => $unit->id,
            'title_ar' => 'ممارسة خطة الحصة',
            'practice_type' => 'application',
            'body_ar' => '<p>تطبيق عملي</p>',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        return [$path, $unit, $lesson, $practice];
    }

    public function test_admin_nav_hubs_are_numbered_and_include_crm_without_legacy_default(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);
        app()->setLocale('ar');

        $hubs = AdminNav::hubsFor($admin);
        $labels = collect($hubs)->pluck('label')->all();

        $this->assertContains('١ · المنتجات والمحتوى', $labels);
        $this->assertContains('٢ · المدارس والمؤسسات', $labels);
        $this->assertContains('٣ · الاستشارات', $labels);
        $this->assertContains('٤ · التشغيل التجاري', $labels);
        $this->assertContains('٥ · التواصل والإعدادات', $labels);

        $sectionKeys = collect($hubs)->flatMap(fn ($h) => collect($h['sections'])->pluck('key'))->all();
        $this->assertContains('crm', $sectionKeys);
        $this->assertContains('learning_paths', $sectionKeys);
        $this->assertContains('inquiries', $sectionKeys);
        $this->assertFalse((bool) config('admin_nav.show_legacy_ops'));
    }

    public function test_admin_nav_uses_english_labels_when_locale_en(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);
        app()->setLocale('en');

        $hubs = AdminNav::hubsFor($admin);
        $labels = collect($hubs)->pluck('label')->all();

        $this->assertContains('1 · Products & Content', $labels);
        $this->assertContains('4 · Commerce Ops', $labels);
        $this->assertContains('5 · Comms & Settings', $labels);

        $itemLabels = collect($hubs)->flatMap(fn ($h) => collect($h['sections'])->flatMap(fn ($s) => collect($s['items'])->pluck('label')))->all();
        $this->assertContains('Manage packages', $itemLabels);
        $this->assertContains('CRM dashboard', $itemLabels);
        $this->assertContains('Notification center', $itemLabels);
    }

    public function test_teacher_can_read_lesson_practice_and_complete(): void
    {
        [$path, , $lesson, $practice] = $this->seedPathContent();
        $student = $this->makeStudent();

        TeacherPathEnrollment::query()->create([
            'user_id' => $student->id,
            'learning_path_id' => $path->id,
            'status' => 'active',
            'progress' => 0,
            'enrolled_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('student.learning-paths.lesson', [$path->slug, $lesson]))
            ->assertOk()
            ->assertSee('محتوى الدرس للمعلم', false)
            ->assertSee('تسجيل إكمال الدرس', false);

        $this->actingAs($student)
            ->get(route('student.learning-paths.practice', [$path->slug, $practice]))
            ->assertOk()
            ->assertSee('تطبيق عملي', false);

        $this->actingAs($student)
            ->post(route('student.learning-paths.complete', $path->slug), [
                'item_type' => TeacherPathProgressItem::TYPE_LESSON,
                'item_id' => $lesson->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('teacher_path_progress_items', [
            'user_id' => $student->id,
            'learning_path_id' => $path->id,
            'item_type' => 'lesson',
            'item_id' => $lesson->id,
            'is_completed' => 1,
        ]);

        $this->actingAs($student)
            ->post(route('student.learning-paths.complete', $path->slug), [
                'item_type' => TeacherPathProgressItem::TYPE_PRACTICE,
                'item_id' => $practice->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('teacher_path_progress_items', [
            'user_id' => $student->id,
            'item_type' => 'practice',
            'item_id' => $practice->id,
            'is_completed' => 1,
        ]);
    }

    public function test_admin_can_edit_path_units_and_lessons(): void
    {
        [$path] = $this->seedPathContent();
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->get(route('admin.learning-paths.show', $path))
            ->assertOk()
            ->assertSee('إدارة الصف', false);

        $this->actingAs($admin)
            ->post(route('admin.learning-paths.units.store', $path), [
                'title_ar' => 'وحدة جديدة من الاختبار',
                'is_active' => 1,
            ])
            ->assertRedirect();

        $unit = LearningPathUnit::query()
            ->where('learning_path_id', $path->id)
            ->where('title_ar', 'وحدة جديدة من الاختبار')
            ->first();
        $this->assertNotNull($unit);

        $this->actingAs($admin)
            ->post(route('admin.learning-paths.units.lessons.store', $unit), [
                'title_ar' => 'درس إداري جديد',
                'content_type' => 'text',
                'body_ar' => '<p>نص محرّر</p>',
                'is_active' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('learning_path_lessons', [
            'learning_path_unit_id' => $unit->id,
            'title_ar' => 'درس إداري جديد',
        ]);
    }

    public function test_packages_index_hides_legacy_catalog_tabs_by_default(): void
    {
        $admin = $this->makeAdmin();
        Package::query()->create([
            'name' => 'باقة المعلم',
            'slug' => 'teacher-pkg-'.uniqid(),
            'price' => 99,
            'is_active' => true,
            'order' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.packages.index'))
            ->assertOk()
            ->assertSee('باقة المعلم', false)
            ->assertDontSee('أسعار البرامج', false)
            ->assertDontSee('باقات الحصص', false);
    }

    public function test_inquiry_links_to_institution_program(): void
    {
        $institution = Institution::query()->create([
            'slug' => 'school-'.uniqid(),
            'name_ar' => 'مدرسة الاختبار',
            'contact_email' => 'school@example.com',
            'contact_phone' => '0500000000',
            'is_active' => true,
        ]);

        $program = InstitutionProgram::query()->create([
            'institution_id' => $institution->id,
            'service_key' => 'teacher_training',
            'program_kind' => InstitutionProgram::KIND_TRAINING,
            'title_ar' => 'برنامج تطوير المعلمين',
            'status' => InstitutionProgram::STATUS_INQUIRY,
        ]);

        $inquiry = InquiryService::fromInstitutionInquiry(
            $institution,
            'منسق المدرسة',
            'نحتاج مسار إدارة الصف',
            'تدريب معلمين',
            (int) $program->id
        );

        $this->assertSame((int) $program->id, (int) $inquiry->institution_program_id);
        $this->assertSame(Inquiry::TYPE_SCHOOL_INSTITUTION, $inquiry->inquiry_type);
        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
            'institution_id' => $institution->id,
            'institution_program_id' => $program->id,
        ]);
    }

    public function test_notification_center_shows_delivery_ledger(): void
    {
        $admin = $this->makeAdmin();

        NotificationDelivery::query()->create([
            'event_key' => 'new_inquiry',
            'channel' => 'email',
            'status' => NotificationDelivery::STATUS_DELIVERED,
            'recipient_email' => 'ops@example.com',
            'recipient_name' => 'Ops',
            'subject' => 'استفسار جديد',
            'body' => 'نص',
            'sent_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.notification-center.index'))
            ->assertOk()
            ->assertSee('سجل التسليمات', false)
            ->assertSee('new_inquiry', false)
            ->assertSee('Ops', false);
    }

    public function test_admin_sidebar_renders_brief_hubs_without_legacy_ops(): void
    {
        $admin = $this->makeAdmin();
        app()->setLocale('ar');

        $this->actingAs($admin)
            ->get(route('admin.packages.index'))
            ->assertOk()
            ->assertSee('١ · المنتجات والمحتوى', false)
            ->assertSee('CRM المؤسسات والمعلمين', false)
            ->assertDontSee('تشغيل داخلي (إرث)', false);
    }

    public function test_admin_sidebar_english_locale_shows_english_hubs(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->get(route('admin.packages.index', ['lang' => 'en']))
            ->assertOk()
            ->assertSee('1 · Products & Content')
            ->assertSee('Institution & teacher CRM')
            ->assertSee('العربية', false)
            ->assertSee('Pricing and Packages');
    }
}
