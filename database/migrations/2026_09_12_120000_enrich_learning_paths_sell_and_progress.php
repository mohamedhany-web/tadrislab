<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Standalone sell fields + per-item progress/completion for Learning Paths.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('learning_paths')) {
            Schema::table('learning_paths', function (Blueprint $table) {
                if (! Schema::hasColumn('learning_paths', 'price')) {
                    $table->decimal('price', 10, 2)->nullable()->after('estimated_minutes');
                }
                if (! Schema::hasColumn('learning_paths', 'currency')) {
                    $table->string('currency', 10)->default('QAR')->after('price');
                }
                if (! Schema::hasColumn('learning_paths', 'is_sellable_standalone')) {
                    $table->boolean('is_sellable_standalone')->default(false)->after('currency');
                }
                if (! Schema::hasColumn('learning_paths', 'access_days')) {
                    $table->unsignedInteger('access_days')->nullable()->after('is_sellable_standalone');
                }
            });
        }

        if (! Schema::hasTable('teacher_path_progress_items')) {
            Schema::create('teacher_path_progress_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('learning_path_id')->constrained('learning_paths')->cascadeOnDelete();
                $table->string('item_type', 32); // lesson|practice
                $table->unsignedBigInteger('item_id');
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'item_type', 'item_id'], 'teacher_path_progress_unique');
                $table->index(['user_id', 'learning_path_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_path_progress_items');

        if (Schema::hasTable('learning_paths')) {
            Schema::table('learning_paths', function (Blueprint $table) {
                foreach (['access_days', 'is_sellable_standalone', 'currency', 'price'] as $col) {
                    if (Schema::hasColumn('learning_paths', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
