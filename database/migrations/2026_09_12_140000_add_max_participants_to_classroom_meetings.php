<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Ensure classroom_meetings.max_participants exists (used by consultations + classroom). */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('classroom_meetings')) {
            return;
        }

        Schema::table('classroom_meetings', function (Blueprint $table) {
            if (! Schema::hasColumn('classroom_meetings', 'max_participants')) {
                $table->unsignedInteger('max_participants')->nullable()->after('planned_duration_minutes');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('classroom_meetings') && Schema::hasColumn('classroom_meetings', 'max_participants')) {
            Schema::table('classroom_meetings', function (Blueprint $table) {
                $table->dropColumn('max_participants');
            });
        }
    }
};
