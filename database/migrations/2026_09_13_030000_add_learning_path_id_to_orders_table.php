<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'learning_path_id')) {
                $table->foreignId('learning_path_id')->nullable()->after('package_id')->constrained('learning_paths')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'learning_path_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('learning_path_id');
        });
    }
};
