<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('packages')) {
            return;
        }

        Schema::table('packages', function (Blueprint $table) {
            if (! Schema::hasColumn('packages', 'package_type')) {
                $table->string('package_type', 40)->default('individual')->after('track')->index();
            }
            if (! Schema::hasColumn('packages', 'consultation_sessions')) {
                $table->unsignedSmallInteger('consultation_sessions')->nullable()->after('courses_count');
            }
            if (! Schema::hasColumn('packages', 'participant_seats')) {
                $table->unsignedSmallInteger('participant_seats')->nullable()->after('consultation_sessions');
            }
            if (! Schema::hasColumn('packages', 'includes_tools')) {
                $table->boolean('includes_tools')->default(true)->after('participant_seats');
            }
            if (! Schema::hasColumn('packages', 'cta_mode')) {
                $table->string('cta_mode', 20)->default('register')->after('includes_tools');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('packages')) {
            return;
        }

        Schema::table('packages', function (Blueprint $table) {
            foreach (['package_type', 'consultation_sessions', 'participant_seats', 'includes_tools', 'cta_mode'] as $col) {
                if (Schema::hasColumn('packages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
