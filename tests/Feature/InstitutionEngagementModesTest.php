<?php

namespace Tests\Feature;

use App\Models\Institution;
use App\Models\InstitutionMember;
use App\Models\InstitutionProgram;
use App\Models\InstitutionProgramParticipant;
use App\Models\User;
use App\Services\InstitutionEngagementService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class InstitutionEngagementModesTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->ensureInstitutionTables();
    }

    protected function ensureInstitutionTables(): void
    {
        if (! Schema::hasColumn('users', 'instructor_grants_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('instructor_grants_enabled')->default(true);
            });
        }

        if (! Schema::hasTable('institutions')) {
            Schema::create('institutions', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name_ar');
                $table->string('name_en')->nullable();
                $table->string('org_type')->default('school');
                $table->string('default_engagement_mode', 32)->default('platform_access');
                $table->unsignedInteger('seat_limit')->nullable();
                $table->string('country')->nullable();
                $table->string('city')->nullable();
                $table->string('contact_name')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_phone')->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('institution_members')) {
            Schema::create('institution_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institution_id');
                $table->foreignId('user_id')->nullable();
                $table->string('member_role');
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('title')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('institution_programs')) {
            Schema::create('institution_programs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institution_id')->nullable();
                $table->string('service_key')->default('school_institutional_training');
                $table->string('program_kind')->default('training');
                $table->string('engagement_mode', 32)->nullable();
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->string('status')->default('approved');
                $table->unsignedInteger('planned_participants')->nullable();
                $table->unsignedInteger('seat_limit')->nullable();
                $table->string('delivery_mode')->nullable();
                $table->unsignedInteger('progress_percent')->default(0);
                $table->foreignId('assigned_instructor_id')->nullable();
                $table->foreignId('coordinator_user_id')->nullable();
                $table->foreignId('created_by')->nullable();
                $table->text('result_notes')->nullable();
                $table->text('improvement_plan')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('institution_program_participants')) {
            Schema::create('institution_program_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institution_program_id');
                $table->foreignId('institution_member_id')->nullable();
                $table->foreignId('user_id')->nullable();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('status')->default('enrolled');
                $table->unsignedInteger('progress_percent')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    private function makeUser(string $role = 'student'): User
    {
        return User::query()->create([
            'name' => ucfirst($role).' '.uniqid(),
            'email' => $role.'-'.uniqid().'@example.com',
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
            'instructor_grants_enabled' => true,
        ]);
    }

    public function test_platform_access_enforces_program_seat_limit(): void
    {
        $coord = $this->makeUser('student');
        $org = Institution::query()->create([
            'slug' => 'org-'.uniqid(),
            'name_ar' => 'مدرسة منصة',
            'org_type' => 'school',
            'default_engagement_mode' => Institution::ENGAGEMENT_PLATFORM,
            'is_active' => true,
        ]);
        InstitutionMember::query()->create([
            'institution_id' => $org->id,
            'user_id' => $coord->id,
            'member_role' => InstitutionMember::ROLE_COORDINATOR,
            'name' => $coord->name,
            'email' => $coord->email,
            'is_active' => true,
        ]);
        $m1 = InstitutionMember::query()->create([
            'institution_id' => $org->id,
            'member_role' => InstitutionMember::ROLE_PARTICIPANT,
            'name' => 'مشارك 1',
            'email' => 'p1-'.uniqid().'@ex.com',
            'is_active' => true,
        ]);
        $m2 = InstitutionMember::query()->create([
            'institution_id' => $org->id,
            'member_role' => InstitutionMember::ROLE_PARTICIPANT,
            'name' => 'مشارك 2',
            'email' => 'p2-'.uniqid().'@ex.com',
            'is_active' => true,
        ]);
        $program = InstitutionProgram::query()->create([
            'institution_id' => $org->id,
            'title_ar' => 'برنامج مقاعد',
            'engagement_mode' => InstitutionEngagementService::MODE_PLATFORM,
            'seat_limit' => 1,
            'status' => InstitutionProgram::STATUS_APPROVED,
            'service_key' => 'school_institutional_training',
        ]);

        $this->actingAs($coord)
            ->post(route('institution.portal.program.enroll', [$org, $program]), [
                'institution_member_id' => $m1->id,
            ])
            ->assertRedirect();

        $this->assertSame(1, InstitutionProgramParticipant::query()->where('institution_program_id', $program->id)->count());

        $this->actingAs($coord)
            ->post(route('institution.portal.program.enroll', [$org, $program]), [
                'institution_member_id' => $m2->id,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(1, InstitutionProgramParticipant::query()->where('institution_program_id', $program->id)->count());
    }

    public function test_direct_delivery_blocks_seat_enrollment(): void
    {
        $coord = $this->makeUser('student');
        $coach = $this->makeUser('instructor');
        $org = Institution::query()->create([
            'slug' => 'org-'.uniqid(),
            'name_ar' => 'مدرسة مباشرة',
            'org_type' => 'school',
            'default_engagement_mode' => Institution::ENGAGEMENT_DIRECT,
            'is_active' => true,
        ]);
        InstitutionMember::query()->create([
            'institution_id' => $org->id,
            'user_id' => $coord->id,
            'member_role' => InstitutionMember::ROLE_COORDINATOR,
            'name' => $coord->name,
            'email' => $coord->email,
            'is_active' => true,
        ]);
        $member = InstitutionMember::query()->create([
            'institution_id' => $org->id,
            'member_role' => InstitutionMember::ROLE_PARTICIPANT,
            'name' => 'معلم',
            'email' => 't-'.uniqid().'@ex.com',
            'is_active' => true,
        ]);
        $program = InstitutionProgram::query()->create([
            'institution_id' => $org->id,
            'title_ar' => 'ورشة مباشرة',
            'engagement_mode' => InstitutionEngagementService::MODE_DIRECT,
            'assigned_instructor_id' => $coach->id,
            'status' => InstitutionProgram::STATUS_APPROVED,
            'service_key' => 'workshops',
        ]);

        $this->actingAs($coord)
            ->post(route('institution.portal.program.enroll', [$org, $program]), [
                'institution_member_id' => $member->id,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(0, InstitutionProgramParticipant::query()->where('institution_program_id', $program->id)->count());
    }

    public function test_assigned_coach_can_update_direct_delivery(): void
    {
        $coach = $this->makeUser('instructor');
        $org = Institution::query()->create([
            'slug' => 'org-'.uniqid(),
            'name_ar' => 'جهة تنفيذ',
            'org_type' => 'institution',
            'default_engagement_mode' => Institution::ENGAGEMENT_DIRECT,
            'is_active' => true,
        ]);
        $program = InstitutionProgram::query()->create([
            'institution_id' => $org->id,
            'title_ar' => 'تدريب مباشر',
            'engagement_mode' => InstitutionEngagementService::MODE_DIRECT,
            'assigned_instructor_id' => $coach->id,
            'status' => InstitutionProgram::STATUS_APPROVED,
            'service_key' => 'pd_programs',
            'progress_percent' => 0,
        ]);

        $this->actingAs($coach)
            ->put(route('instructor.institution-delivery.update', $program), [
                'status' => InstitutionProgram::STATUS_IN_PROGRESS,
                'progress_percent' => 40,
                'result_notes' => 'جلسة أولى تمت',
            ])
            ->assertRedirect();

        $program->refresh();
        $this->assertSame(InstitutionProgram::STATUS_IN_PROGRESS, $program->status);
        $this->assertSame(40, (int) $program->progress_percent);
        $this->assertSame('جلسة أولى تمت', $program->result_notes);
    }

    public function test_coordinator_report_page_loads(): void
    {
        $coord = $this->makeUser('student');
        $org = Institution::query()->create([
            'slug' => 'org-'.uniqid(),
            'name_ar' => 'جهة تقرير',
            'org_type' => 'school',
            'default_engagement_mode' => Institution::ENGAGEMENT_PLATFORM,
            'is_active' => true,
        ]);
        InstitutionMember::query()->create([
            'institution_id' => $org->id,
            'user_id' => $coord->id,
            'member_role' => InstitutionMember::ROLE_COORDINATOR,
            'name' => $coord->name,
            'email' => $coord->email,
            'is_active' => true,
        ]);

        $this->actingAs($coord)
            ->get(route('institution.portal.report', $org))
            ->assertOk()
            ->assertSee('تقرير المتابعة', false);
    }
}
