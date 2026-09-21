<?php

namespace Tests\Feature;

use App\Models\ClassroomMeeting;
use App\Models\ConsultationRequest;
use App\Models\Institution;
use App\Models\InstitutionMember;
use App\Models\InstitutionProgram;
use App\Models\InstitutionProgramParticipant;
use App\Models\Package;
use App\Models\User;
use App\Models\UserPackageEntitlement;
use App\Services\PackageEntitlementService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class TadrisConsultInstitutionInstructorCloseTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->ensureDomainTables();
    }

    protected function ensureDomainTables(): void
    {
        if (! Schema::hasTable('classroom_meetings')) {
            Schema::create('classroom_meetings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable();
                $table->unsignedBigInteger('consultation_request_id')->nullable();
                $table->unsignedBigInteger('one_to_one_session_id')->nullable();
                $table->string('code', 32)->unique();
                $table->string('room_name', 64)->nullable();
                $table->string('title')->nullable();
                $table->timestamp('scheduled_for')->nullable();
                $table->unsignedInteger('planned_duration_minutes')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->json('settings')->nullable();
                $table->timestamps();
            });
        } elseif (! Schema::hasColumn('classroom_meetings', 'consultation_request_id')) {
            Schema::table('classroom_meetings', function (Blueprint $table) {
                $table->unsignedBigInteger('consultation_request_id')->nullable();
            });
        }

        if (! Schema::hasTable('consultation_requests')) {
            Schema::create('consultation_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('consultation_service_id')->nullable();
                $table->string('consultation_type', 40)->nullable();
                $table->foreignId('instructor_id')->nullable();
                $table->foreignId('student_id')->nullable();
                $table->decimal('price_amount', 10, 2)->default(0);
                $table->string('currency', 8)->nullable();
                $table->unsignedSmallInteger('duration_minutes')->default(45);
                $table->string('status', 40)->default('new');
                $table->string('payment_reference')->nullable();
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('reminder_sent_at')->nullable();
                $table->unsignedBigInteger('classroom_meeting_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('packages')) {
            Schema::create('packages', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->string('package_type')->nullable();
                $table->string('cta_mode')->nullable();
                $table->unsignedInteger('consultation_sessions')->default(0);
                $table->boolean('includes_tools')->default(false);
                $table->unsignedInteger('participant_seats')->nullable();
                $table->unsignedInteger('duration_days')->nullable();
                $table->json('tools_resources')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('user_package_entitlements')) {
            Schema::create('user_package_entitlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('package_id');
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('activated_by')->nullable();
                $table->unsignedInteger('consultation_sessions_total')->default(0);
                $table->unsignedInteger('consultation_sessions_remaining')->default(0);
                $table->unsignedInteger('participant_seats')->nullable();
                $table->boolean('includes_tools')->default(false);
                $table->json('tools_resources')->nullable();
                $table->string('status', 24)->default('active');
                $table->timestamp('activated_at')->nullable();
                $table->timestamp('expires_at')->nullable();
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

        if (! Schema::hasTable('institution_members')) {
            Schema::create('institution_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institution_id');
                $table->foreignId('user_id')->nullable();
                $table->string('member_role', 32)->default('participant');
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
                $table->foreignId('institution_id');
                $table->string('service_key')->nullable();
                $table->string('program_kind', 32)->default('training');
                $table->string('title_ar')->nullable();
                $table->string('title_en')->nullable();
                $table->string('status', 32)->default('inquiry');
                $table->text('proposal_notes')->nullable();
                $table->decimal('price', 12, 2)->nullable();
                $table->string('currency', 8)->nullable();
                $table->unsignedInteger('progress_percent')->default(0);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('institution_program_participants')) {
            Schema::create('institution_program_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institution_program_id');
                $table->unsignedBigInteger('institution_member_id')->nullable();
                $table->foreignId('user_id')->nullable();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('status', 32)->default('enrolled');
                $table->unsignedInteger('progress_percent')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('advanced_courses')) {
            Schema::create('advanced_courses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->foreignId('instructor_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('instructor_course_assignments')) {
            Schema::create('instructor_course_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('advanced_course_id');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function test_confirmed_consultation_is_scheduled_and_completes_on_room_end(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);

        $consultation = ConsultationRequest::create([
            'instructor_id' => $instructor->id,
            'student_id' => $student->id,
            'price_amount' => 0,
            'duration_minutes' => 45,
            'status' => ConsultationRequest::STATUS_CONFIRMED,
            'scheduled_at' => now()->addDay(),
        ]);

        $meeting = ClassroomMeeting::create([
            'user_id' => $instructor->id,
            'consultation_request_id' => $consultation->id,
            'code' => 'CONS'.strtoupper(substr(uniqid(), -6)),
            'room_name' => 'consultation-test',
            'title' => 'استشارة',
            'started_at' => now()->subMinutes(30),
            'settings' => ['consultation' => true, 'allow_guest_join' => false],
        ]);

        $consultation->update(['classroom_meeting_id' => $meeting->id]);
        $consultation->refresh();

        $this->assertTrue($consultation->isActiveBooking());
        $this->assertTrue($consultation->isScheduled());

        $response = $this->actingAs($instructor)
            ->post(route('instructor.classroom.end', $meeting));
        $response->assertRedirect();

        $this->assertSame(ConsultationRequest::STATUS_COMPLETED, $consultation->fresh()->status);
        $this->assertNotNull($meeting->fresh()->ended_at);
    }

    public function test_cancel_restores_package_consultation_session(): void
    {
        $user = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $package = Package::create([
            'name' => 'باقة اختبار',
            'consultation_sessions' => 3,
        ]);
        $entitlement = UserPackageEntitlement::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'consultation_sessions_total' => 3,
            'consultation_sessions_remaining' => 2,
            'status' => 'active',
            'activated_at' => now(),
        ]);

        $this->assertTrue(
            PackageEntitlementService::restoreConsultationSessionFromReference('package_entitlement:'.$entitlement->id)
        );
        $this->assertSame(3, (int) $entitlement->fresh()->consultation_sessions_remaining);
    }

    public function test_institution_coordinator_accepts_proposal_and_tracks_progress(): void
    {
        $coord = User::factory()->create(['role' => 'student', 'is_active' => true, 'email' => 'coord-'.uniqid().'@test.local']);
        $institution = Institution::create([
            'slug' => 'school-'.uniqid(),
            'name_ar' => 'مدرسة اختبار',
            'org_type' => 'school',
            'contact_email' => $coord->email,
            'is_active' => true,
        ]);
        InstitutionMember::create([
            'institution_id' => $institution->id,
            'user_id' => $coord->id,
            'member_role' => InstitutionMember::ROLE_COORDINATOR,
            'name' => $coord->name,
            'email' => $coord->email,
            'is_active' => true,
        ]);
        $participantMember = InstitutionMember::create([
            'institution_id' => $institution->id,
            'member_role' => InstitutionMember::ROLE_PARTICIPANT,
            'name' => 'معلم مشارك',
            'email' => 'teacher-'.uniqid().'@test.local',
            'is_active' => true,
        ]);
        $program = InstitutionProgram::create([
            'institution_id' => $institution->id,
            'service_key' => 'pd_programs',
            'program_kind' => InstitutionProgram::KIND_TRAINING,
            'title_ar' => 'برنامج PD',
            'status' => InstitutionProgram::STATUS_PROPOSAL,
            'proposal_notes' => 'عرض تجريبي',
            'price' => 500,
            'currency' => 'QAR',
            'progress_percent' => 0,
        ]);

        $this->assertTrue($program->canCoordinatorAccept());
        $this->assertTrue($program->canTransitionTo(InstitutionProgram::STATUS_APPROVED));
        $this->assertFalse($program->canTransitionTo(InstitutionProgram::STATUS_COMPLETED));

        $this->actingAs($coord)
            ->post(route('institution.portal.program.accept', [$institution, $program]))
            ->assertRedirect();

        $this->assertSame(InstitutionProgram::STATUS_APPROVED, $program->fresh()->status);

        $this->actingAs($coord)
            ->post(route('institution.portal.program.enroll', [$institution, $program]), [
                'institution_member_id' => $participantMember->id,
            ])
            ->assertRedirect();

        $row = InstitutionProgramParticipant::query()
            ->where('institution_program_id', $program->id)
            ->where('institution_member_id', $participantMember->id)
            ->first();
        $this->assertNotNull($row);

        $program->update(['status' => InstitutionProgram::STATUS_IN_PROGRESS]);

        $this->actingAs($coord)
            ->post(route('institution.portal.program.participant.progress', [$institution, $program, $row]), [
                'progress_percent' => 100,
                'status' => 'completed',
            ])
            ->assertRedirect();

        $this->assertSame(100, (int) $row->fresh()->progress_percent);
        $this->assertSame(InstitutionProgram::STATUS_COMPLETED, $program->fresh()->status);
    }

    public function test_instructor_live_create_is_404_when_ui_flag_off(): void
    {
        config(['instructor_ui.show_live_broadcast' => false]);

        $instructor = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($instructor)
            ->get(route('instructor.live-sessions.create'))
            ->assertNotFound();
    }

    public function test_instructor_tutoring_is_404_when_ui_flag_off(): void
    {
        config(['instructor_ui.show_tutoring' => false]);

        $instructor = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($instructor)
            ->get(route('instructor.tutoring-bookings.index'))
            ->assertNotFound();
    }
}
