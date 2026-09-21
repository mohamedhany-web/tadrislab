<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TADRIS LAB — Schools & Institutions (single commercial axis).
 * Institution Account → Coordinator → Participants → Programs → Progress.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('institutions')) {
            Schema::create('institutions', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name_ar');
                $table->string('name_en')->nullable();
                $table->string('org_type', 32)->default('school'); // school|center|institution|other — display only, one commercial axis
                $table->string('country', 80)->nullable();
                $table->string('city', 80)->nullable();
                $table->string('contact_name')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_phone', 40)->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('institution_members')) {
            Schema::create('institution_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('member_role', 32)->default('participant'); // coordinator|participant
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone', 40)->nullable();
                $table->string('title')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['institution_id', 'member_role']);
                $table->index(['institution_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('institution_programs')) {
            Schema::create('institution_programs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
                $table->string('service_key', 64); // from platform.schools_institutions.services
                $table->string('program_kind', 32)->default('training'); // training|development
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->text('summary_ar')->nullable();
                $table->text('summary_en')->nullable();
                $table->string('status', 32)->default('inquiry'); // inquiry|proposal|approved|scheduled|in_progress|completed|cancelled
                $table->unsignedInteger('planned_participants')->nullable();
                $table->unsignedInteger('duration_hours')->nullable();
                $table->string('delivery_mode', 64)->nullable(); // onsite|online|hybrid|custom
                $table->decimal('price', 12, 2)->nullable();
                $table->string('currency', 10)->default('QAR');
                $table->date('starts_on')->nullable();
                $table->date('ends_on')->nullable();
                $table->timestamp('scheduled_at')->nullable();
                $table->text('inquiry_notes')->nullable();
                $table->text('proposal_notes')->nullable();
                $table->text('diagnosis_notes')->nullable(); // institutional development
                $table->text('improvement_plan')->nullable();
                $table->text('result_notes')->nullable();
                $table->unsignedTinyInteger('progress_percent')->default(0);
                $table->foreignId('coordinator_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('assigned_instructor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->index(['status', 'service_key']);
                $table->index(['institution_id', 'status']);
            });
        }

        if (! Schema::hasTable('institution_program_participants')) {
            Schema::create('institution_program_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institution_program_id')->constrained('institution_programs')->cascadeOnDelete();
                $table->foreignId('institution_member_id')->nullable()->constrained('institution_members')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone', 40)->nullable();
                $table->string('status', 32)->default('enrolled'); // enrolled|attended|completed|withdrawn
                $table->unsignedTinyInteger('progress_percent')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['institution_program_id', 'status'], 'ipp_program_status_idx');
            });
        } elseif (Schema::hasTable('institution_program_participants')) {
            try {
                Schema::table('institution_program_participants', function (Blueprint $table) {
                    $table->index(['institution_program_id', 'status'], 'ipp_program_status_idx');
                });
            } catch (\Throwable) {
                // Index already present
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_program_participants');
        Schema::dropIfExists('institution_programs');
        Schema::dropIfExists('institution_members');
        Schema::dropIfExists('institutions');
    }
};
