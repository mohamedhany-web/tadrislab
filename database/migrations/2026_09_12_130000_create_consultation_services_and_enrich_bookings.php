<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TADRIS LAB Consultations: admin-managed service catalogue + booking journey fields.
 * Independent of Learning Paths.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('consultation_services')) {
            Schema::create('consultation_services', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('consultation_type', 64); // teacher_individual|specialized|institution|coaching_mentoring
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->text('summary_ar')->nullable();
                $table->text('summary_en')->nullable();
                $table->text('description_ar')->nullable();
                $table->text('description_en')->nullable();
                $table->unsignedInteger('duration_minutes')->default(30);
                $table->decimal('price', 10, 2)->default(0);
                $table->string('currency', 10)->default('QAR');
                $table->boolean('requires_instructor')->default(true);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_published')->default(false);
                $table->boolean('is_mvp')->default(true); // coaching_mentoring = false until later
                $table->unsignedInteger('sort_order')->default(0);
                $table->foreignId('default_instructor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->index(['consultation_type', 'is_active', 'is_published']);
            });
        }

        if (Schema::hasTable('consultation_requests')) {
            Schema::table('consultation_requests', function (Blueprint $table) {
                if (! Schema::hasColumn('consultation_requests', 'consultation_service_id')) {
                    $table->foreignId('consultation_service_id')->nullable()->after('id')
                        ->constrained('consultation_services')->nullOnDelete();
                }
                if (! Schema::hasColumn('consultation_requests', 'consultation_type')) {
                    $table->string('consultation_type', 64)->nullable()->after('consultation_service_id');
                }
                if (! Schema::hasColumn('consultation_requests', 'preferred_slot_at')) {
                    $table->timestamp('preferred_slot_at')->nullable()->after('scheduled_at');
                }
                if (! Schema::hasColumn('consultation_requests', 'contact_name')) {
                    $table->string('contact_name')->nullable()->after('student_message');
                }
                if (! Schema::hasColumn('consultation_requests', 'contact_phone')) {
                    $table->string('contact_phone', 40)->nullable()->after('contact_name');
                }
                if (! Schema::hasColumn('consultation_requests', 'contact_email')) {
                    $table->string('contact_email')->nullable()->after('contact_phone');
                }
                if (! Schema::hasColumn('consultation_requests', 'organization_name')) {
                    $table->string('organization_name')->nullable()->after('contact_email');
                }
                if (! Schema::hasColumn('consultation_requests', 'form_payload')) {
                    $table->json('form_payload')->nullable()->after('organization_name');
                }
                if (! Schema::hasColumn('consultation_requests', 'outcome_notes')) {
                    $table->text('outcome_notes')->nullable()->after('admin_notes');
                }
                if (! Schema::hasColumn('consultation_requests', 'recommendations')) {
                    $table->text('recommendations')->nullable()->after('outcome_notes');
                }
                if (! Schema::hasColumn('consultation_requests', 'rescheduled_from')) {
                    $table->timestamp('rescheduled_from')->nullable()->after('scheduled_at');
                }
                if (! Schema::hasColumn('consultation_requests', 'currency')) {
                    $table->string('currency', 10)->nullable()->after('price_amount');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('consultation_requests')) {
            Schema::table('consultation_requests', function (Blueprint $table) {
                foreach ([
                    'recommendations', 'outcome_notes', 'form_payload', 'organization_name',
                    'contact_email', 'contact_phone', 'contact_name', 'preferred_slot_at',
                    'rescheduled_from', 'consultation_type', 'currency',
                ] as $col) {
                    if (Schema::hasColumn('consultation_requests', $col)) {
                        $table->dropColumn($col);
                    }
                }
                if (Schema::hasColumn('consultation_requests', 'consultation_service_id')) {
                    $table->dropConstrainedForeignId('consultation_service_id');
                }
            });
        }

        Schema::dropIfExists('consultation_services');
    }
};
