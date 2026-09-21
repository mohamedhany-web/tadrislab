<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('inquiries')) {
            return;
        }

        Schema::table('inquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('inquiries', 'institution_program_id')) {
                $table->foreignId('institution_program_id')
                    ->nullable()
                    ->after('institution_id')
                    ->constrained('institution_programs')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('inquiries') || ! Schema::hasColumn('inquiries', 'institution_program_id')) {
            return;
        }

        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_program_id');
        });
    }
};
