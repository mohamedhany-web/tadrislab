<?php

namespace Tests\Feature;

use App\Models\AdvancedCourse;
use App\Models\InstructorCourseAssignment;
use App\Models\InstructorLearningPathAssignment;
use App\Models\InstructorServiceAssignment;
use App\Models\LearningPath;
use App\Models\LearningPathUnit;
use App\Models\Package;
use App\Models\StudentCourseEnrollment;
use App\Models\TeacherPathEnrollment;
use App\Models\User;
use App\Services\PackageEntitlementService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class PathsCoursesPackageWiringTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->ensureWiringTables();
    }

    protected function ensureWiringTables(): void
    {
        if (! Schema::hasColumn('users', 'instructor_grants_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('instructor_grants_enabled')->default(false);
            });
        }

        if (! Schema::hasTable('packages')) {
            Schema::create('packages', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->string('currency', 8)->nullable();
                $table->string('package_type')->nullable();
                $table->string('cta_mode')->nullable();
                $table->integer('duration_days')->nullable();
                $table->unsignedInteger('consultation_sessions')->default(0);
                $table->unsignedInteger('participant_seats')->nullable();
                $table->boolean('includes_tools')->default(false);
                $table->json('tools_resources')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
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

        if (! Schema::hasTable('package_learning_path')) {
            Schema::create('package_learning_path', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id');
                $table->foreignId('learning_path_id');
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
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
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
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->foreignId('activated_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('advanced_courses')) {
            Schema::create('advanced_courses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->foreignId('instructor_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('student_course_enrollments')) {
            Schema::create('student_course_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('advanced_course_id');
                $table->timestamp('enrolled_at')->nullable();
                $table->timestamp('activated_at')->nullable();
                $table->foreignId('activated_by')->nullable();
                $table->string('status')->default('pending');
                $table->decimal('progress', 5, 2)->default(0);
                $table->string('enrollment_type')->nullable();
                $table->string('access_type')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('auto_renew')->default(false);
                $table->decimal('final_price', 10, 2)->nullable();
                $table->string('payment_method')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('user_package_entitlements')) {
            Schema::create('user_package_entitlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('package_id');
                $table->unsignedBigInteger('order_id')->nullable();
                $table->foreignId('activated_by')->nullable();
                $table->unsignedInteger('consultation_sessions_total')->default(0);
                $table->unsignedInteger('consultation_sessions_remaining')->default(0);
                $table->unsignedInteger('participant_seats')->nullable();
                $table->boolean('includes_tools')->default(false);
                $table->json('tools_resources')->nullable();
                $table->string('status', 32)->default('active');
                $table->timestamp('activated_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('instructor_learning_path_assignments')) {
            Schema::create('instructor_learning_path_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('learning_path_id');
                $table->boolean('is_active')->default(true);
                $table->foreignId('assigned_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('instructor_course_assignments')) {
            Schema::create('instructor_course_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('advanced_course_id');
                $table->boolean('is_active')->default(true);
                $table->foreignId('assigned_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('instructor_service_assignments')) {
            Schema::create('instructor_service_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->string('service_key');
                $table->boolean('is_active')->default(true);
                $table->foreignId('assigned_by')->nullable();
                $table->timestamps();
            });
        }
    }

    private function makeLearner(): User
    {
        return User::query()->create([
            'name' => 'Learner',
            'email' => 'learner-'.uniqid().'@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);
    }

    private function makeCoach(bool $grantsEnabled = true): User
    {
        return User::query()->create([
            'name' => 'Coach',
            'email' => 'coach-'.uniqid().'@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'is_active' => true,
            'instructor_grants_enabled' => $grantsEnabled,
        ]);
    }

    private function makePath(array $extra = []): LearningPath
    {
        return LearningPath::query()->create(array_merge([
            'slug' => 'path-'.uniqid(),
            'title_ar' => 'مسار اختبار',
            'summary_ar' => 'ملخص قديم',
            'description_ar' => 'وصف قديم',
            'is_active' => true,
            'is_published' => true,
        ], $extra));
    }

    private function makeCourse(array $extra = []): AdvancedCourse
    {
        return AdvancedCourse::query()->create(array_merge([
            'title' => 'كورس مسجّل '.uniqid(),
            'price' => 50,
            'is_active' => true,
        ], $extra));
    }

    public function test_package_paths_only_activates_path_enrollments(): void
    {
        $learner = $this->makeLearner();
        $path = $this->makePath();
        $package = Package::query()->create([
            'name' => 'باقة مسارات',
            'slug' => 'pkg-paths-'.uniqid(),
            'price' => 100,
            'duration_days' => 30,
            'is_active' => true,
        ]);
        $package->learningPaths()->attach($path->id);

        $result = PackageEntitlementService::activateForUser($package, $learner);

        $this->assertCount(1, $result['path_enrollments']);
        $this->assertCount(0, $result['course_enrollments']);
        $this->assertDatabaseHas('teacher_path_enrollments', [
            'user_id' => $learner->id,
            'learning_path_id' => $path->id,
            'status' => 'active',
        ]);
        $this->assertSame(0, StudentCourseEnrollment::query()->where('user_id', $learner->id)->count());
    }

    public function test_package_courses_only_activates_course_enrollments(): void
    {
        $learner = $this->makeLearner();
        $course = $this->makeCourse();
        $package = Package::query()->create([
            'name' => 'باقة كورسات',
            'slug' => 'pkg-courses-'.uniqid(),
            'price' => 80,
            'duration_days' => 14,
            'is_active' => true,
        ]);
        $package->courses()->attach($course->id, ['order' => 0]);

        $result = PackageEntitlementService::activateForUser($package, $learner);

        $this->assertCount(0, $result['path_enrollments']);
        $this->assertCount(1, $result['course_enrollments']);
        $this->assertDatabaseHas('student_course_enrollments', [
            'user_id' => $learner->id,
            'advanced_course_id' => $course->id,
            'status' => 'active',
            'enrollment_type' => 'package',
        ]);
        $this->assertSame(0, TeacherPathEnrollment::query()->where('user_id', $learner->id)->count());
    }

    public function test_mixed_package_activates_paths_and_courses(): void
    {
        $learner = $this->makeLearner();
        $path = $this->makePath();
        $course = $this->makeCourse();
        $package = Package::query()->create([
            'name' => 'باقة مختلطة',
            'slug' => 'pkg-mixed-'.uniqid(),
            'price' => 150,
            'is_active' => true,
        ]);
        $package->learningPaths()->attach($path->id);
        $package->courses()->attach($course->id, ['order' => 0]);

        $result = PackageEntitlementService::activateForUser($package, $learner);

        $this->assertCount(1, $result['path_enrollments']);
        $this->assertCount(1, $result['course_enrollments']);
        $this->assertTrue(
            TeacherPathEnrollment::query()
                ->where('user_id', $learner->id)
                ->where('learning_path_id', $path->id)
                ->exists()
        );
        $this->assertTrue(
            StudentCourseEnrollment::query()
                ->where('user_id', $learner->id)
                ->where('advanced_course_id', $course->id)
                ->where('status', 'active')
                ->exists()
        );
    }

    public function test_ungranted_coach_cannot_update_path_description(): void
    {
        $coach = $this->makeCoach();
        $path = $this->makePath();

        $this->actingAs($coach)
            ->put(route('instructor.learning-paths.description.update', $path), [
                'summary_ar' => 'محاولة بدون صلاحية',
            ])
            ->assertForbidden();
    }

    public function test_granted_coach_updates_description_but_not_units(): void
    {
        $coach = $this->makeCoach();
        $path = $this->makePath(['summary_ar' => 'قبل', 'description_ar' => 'وصف قبل']);
        LearningPathUnit::query()->create([
            'learning_path_id' => $path->id,
            'title_ar' => 'وحدة إدارية',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        InstructorServiceAssignment::query()->create([
            'user_id' => $coach->id,
            'service_key' => 'learning_paths',
            'is_active' => true,
        ]);
        InstructorLearningPathAssignment::query()->create([
            'user_id' => $coach->id,
            'learning_path_id' => $path->id,
            'is_active' => true,
        ]);

        $this->actingAs($coach)
            ->put(route('instructor.learning-paths.description.update', $path), [
                'summary_ar' => 'ملخص جديد من المدرب',
                'description_ar' => 'وصف جديد ظاهر للمتعلم',
                'skill_focus_ar' => 'إدارة الصف',
            ])
            ->assertRedirect(route('instructor.learning-paths.show', $path));

        $path->refresh();
        $this->assertSame('ملخص جديد من المدرب', $path->summary_ar);
        $this->assertSame('وصف جديد ظاهر للمتعلم', $path->description_ar);
        $this->assertSame('إدارة الصف', $path->skill_focus_ar);
        $this->assertSame(1, $path->units()->count());
        $this->assertSame('وحدة إدارية', $path->units()->first()->title_ar);
    }

    public function test_course_grant_allows_curriculum_management(): void
    {
        $owner = $this->makeCoach();
        $grantee = $this->makeCoach();
        $course = $this->makeCourse(['instructor_id' => $owner->id]);

        $this->assertFalse($grantee->canManageCourseCurriculum($course));

        InstructorServiceAssignment::query()->create([
            'user_id' => $grantee->id,
            'service_key' => 'courses',
            'is_active' => true,
        ]);
        InstructorCourseAssignment::query()->create([
            'user_id' => $grantee->id,
            'advanced_course_id' => $course->id,
            'is_active' => true,
        ]);

        $grantee->refresh();
        $this->assertTrue($grantee->canManageCourseCurriculum($course));
        $this->assertTrue($grantee->teachingAdvancedCourseIds()->contains((int) $course->id));
    }
}
