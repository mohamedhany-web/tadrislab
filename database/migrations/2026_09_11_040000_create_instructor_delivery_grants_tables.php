<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'instructor_grants_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('instructor_grants_enabled')->default(false)->after('is_active');
            });
        }

        if (! Schema::hasTable('instructor_course_assignments')) {
            Schema::create('instructor_course_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('advanced_course_id')->constrained('advanced_courses')->cascadeOnDelete();
                $table->boolean('is_active')->default(true);
                $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'advanced_course_id'], 'instructor_course_unique');
            });
        }

        if (! Schema::hasTable('instructor_service_assignments')) {
            Schema::create('instructor_service_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('service_key', 64);
                $table->boolean('is_active')->default(true);
                $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'service_key'], 'instructor_service_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_service_assignments');
        Schema::dropIfExists('instructor_course_assignments');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'instructor_grants_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('instructor_grants_enabled');
            });
        }
    }
};
