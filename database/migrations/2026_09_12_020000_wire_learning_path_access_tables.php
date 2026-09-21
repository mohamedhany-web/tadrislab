<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('instructor_learning_path_assignments')) {
            Schema::create('instructor_learning_path_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('learning_path_id')->constrained('learning_paths')->cascadeOnDelete();
                $table->boolean('is_active')->default(true);
                $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'learning_path_id'], 'instructor_path_unique');
            });
        }

        if (Schema::hasTable('teacher_path_enrollments')) {
            Schema::table('teacher_path_enrollments', function (Blueprint $table) {
                if (! Schema::hasColumn('teacher_path_enrollments', 'package_id')) {
                    $table->foreignId('package_id')->nullable()->after('learning_path_id')->constrained('packages')->nullOnDelete();
                }
                if (! Schema::hasColumn('teacher_path_enrollments', 'order_id')) {
                    $table->foreignId('order_id')->nullable()->after('package_id')->constrained('orders')->nullOnDelete();
                }
                if (! Schema::hasColumn('teacher_path_enrollments', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('completed_at');
                }
                if (! Schema::hasColumn('teacher_path_enrollments', 'activated_by')) {
                    $table->foreignId('activated_by')->nullable()->after('expires_at')->constrained('users')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_learning_path_assignments');

        if (Schema::hasTable('teacher_path_enrollments')) {
            Schema::table('teacher_path_enrollments', function (Blueprint $table) {
                foreach (['activated_by', 'expires_at', 'order_id', 'package_id'] as $col) {
                    if (Schema::hasColumn('teacher_path_enrollments', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
