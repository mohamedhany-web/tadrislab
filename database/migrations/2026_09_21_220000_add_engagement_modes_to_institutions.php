<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * مساران للتعاقد: direct_delivery | platform_access
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('institutions')) {
            Schema::table('institutions', function (Blueprint $table) {
                if (! Schema::hasColumn('institutions', 'default_engagement_mode')) {
                    $table->string('default_engagement_mode', 32)
                        ->default('platform_access')
                        ->after('org_type');
                }
                if (! Schema::hasColumn('institutions', 'seat_limit')) {
                    $table->unsignedInteger('seat_limit')->nullable()->after('default_engagement_mode');
                }
            });
        }

        if (Schema::hasTable('institution_programs')) {
            Schema::table('institution_programs', function (Blueprint $table) {
                if (! Schema::hasColumn('institution_programs', 'engagement_mode')) {
                    $table->string('engagement_mode', 32)->nullable()->after('program_kind');
                }
                if (! Schema::hasColumn('institution_programs', 'seat_limit')) {
                    $table->unsignedInteger('seat_limit')->nullable()->after('planned_participants');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('institutions')) {
            Schema::table('institutions', function (Blueprint $table) {
                if (Schema::hasColumn('institutions', 'seat_limit')) {
                    $table->dropColumn('seat_limit');
                }
                if (Schema::hasColumn('institutions', 'default_engagement_mode')) {
                    $table->dropColumn('default_engagement_mode');
                }
            });
        }

        if (Schema::hasTable('institution_programs')) {
            Schema::table('institution_programs', function (Blueprint $table) {
                if (Schema::hasColumn('institution_programs', 'seat_limit')) {
                    $table->dropColumn('seat_limit');
                }
                if (Schema::hasColumn('institution_programs', 'engagement_mode')) {
                    $table->dropColumn('engagement_mode');
                }
            });
        }
    }
};
