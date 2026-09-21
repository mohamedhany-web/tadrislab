<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TADRIS LAB Learning Paths hierarchy:
 * Path → Units → Lessons/content + Practices/tools
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('learning_paths')) {
            Schema::create('learning_paths', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->string('skill_focus_ar')->nullable()->comment('المهارة المستهدفة لدى المعلم');
                $table->string('skill_focus_en')->nullable();
                $table->string('summary_ar', 500)->nullable();
                $table->string('summary_en', 500)->nullable();
                $table->text('description_ar')->nullable();
                $table->text('description_en')->nullable();
                $table->string('thumbnail')->nullable();
                $table->unsignedInteger('estimated_minutes')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_published')->default(false);
                $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['is_active', 'is_published', 'sort_order']);
            });
        }

        if (! Schema::hasTable('learning_path_units')) {
            Schema::create('learning_path_units', function (Blueprint $table) {
                $table->id();
                $table->foreignId('learning_path_id')->constrained('learning_paths')->cascadeOnDelete();
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->string('summary_ar', 500)->nullable();
                $table->string('summary_en', 500)->nullable();
                $table->unsignedInteger('estimated_minutes')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['learning_path_id', 'sort_order']);
            });
        }

        if (! Schema::hasTable('learning_path_lessons')) {
            Schema::create('learning_path_lessons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('learning_path_unit_id')->constrained('learning_path_units')->cascadeOnDelete();
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->string('content_type', 32)->default('text')->comment('text|video|file|link|mixed');
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

                $table->index(['learning_path_unit_id', 'sort_order'], 'lp_lessons_unit_sort_idx');
            });
        }

        if (! Schema::hasTable('learning_path_practices')) {
            Schema::create('learning_path_practices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('learning_path_unit_id')->constrained('learning_path_units')->cascadeOnDelete();
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->string('summary_ar', 500)->nullable();
                $table->string('summary_en', 500)->nullable();
                $table->string('practice_type', 32)->default('application')
                    ->comment('tool|application|template|checklist|activity');
                $table->longText('body_ar')->nullable();
                $table->longText('body_en')->nullable();
                $table->string('resource_url')->nullable();
                $table->string('file_path')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['learning_path_unit_id', 'sort_order'], 'lp_practices_unit_sort_idx');
            });
        }

        if (! Schema::hasTable('package_learning_path')) {
            Schema::create('package_learning_path', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
                $table->foreignId('learning_path_id')->constrained('learning_paths')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['package_id', 'learning_path_id'], 'package_path_unique');
            });
        }

        if (! Schema::hasTable('teacher_path_enrollments')) {
            Schema::create('teacher_path_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('learning_path_id')->constrained('learning_paths')->cascadeOnDelete();
                $table->string('status', 32)->default('active'); // active|completed|paused
                $table->decimal('progress', 5, 2)->default(0);
                $table->timestamp('enrolled_at')->useCurrent();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'learning_path_id'], 'teacher_path_enrollment_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_path_enrollments');
        Schema::dropIfExists('package_learning_path');
        Schema::dropIfExists('learning_path_practices');
        Schema::dropIfExists('learning_path_lessons');
        Schema::dropIfExists('learning_path_units');
        Schema::dropIfExists('learning_paths');
    }
};
