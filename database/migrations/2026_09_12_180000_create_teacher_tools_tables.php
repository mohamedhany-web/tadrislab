<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tools & Resources (Brief V3 §7): manageable catalogue entities.
 * May attach to learning paths, packages, or stand alone later.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('teacher_tools')) {
            Schema::create('teacher_tools', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('tool_type', 40); // templates|checklists|classroom_tools|planning_tools|assessment_resources|downloadable
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->string('summary_ar', 500)->nullable();
                $table->string('summary_en', 500)->nullable();
                $table->text('description_ar')->nullable();
                $table->text('description_en')->nullable();
                $table->string('thumbnail')->nullable();
                $table->string('file_path')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_mime', 120)->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->string('external_url', 500)->nullable();
                $table->string('access_mode', 24)->default('free'); // free|package|path|login
                $table->boolean('is_standalone_product')->default(false);
                $table->decimal('price', 10, 2)->nullable();
                $table->string('currency', 8)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_published')->default(false);
                $table->timestamps();

                $table->index(['tool_type', 'is_active', 'is_published']);
                $table->index(['access_mode', 'is_published']);
            });
        }

        if (! Schema::hasTable('learning_path_teacher_tool')) {
            Schema::create('learning_path_teacher_tool', function (Blueprint $table) {
                $table->id();
                $table->foreignId('learning_path_id')->constrained('learning_paths')->cascadeOnDelete();
                $table->foreignId('teacher_tool_id')->constrained('teacher_tools')->cascadeOnDelete();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->unique(['learning_path_id', 'teacher_tool_id'], 'path_tool_unique');
            });
        }

        if (! Schema::hasTable('package_teacher_tool')) {
            Schema::create('package_teacher_tool', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
                $table->foreignId('teacher_tool_id')->constrained('teacher_tools')->cascadeOnDelete();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->unique(['package_id', 'teacher_tool_id'], 'package_tool_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('package_teacher_tool');
        Schema::dropIfExists('learning_path_teacher_tool');
        Schema::dropIfExists('teacher_tools');
    }
};
