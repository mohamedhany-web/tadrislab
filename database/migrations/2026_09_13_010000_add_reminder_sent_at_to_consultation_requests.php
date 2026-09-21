<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('consultation_requests')) {
            return;
        }

        Schema::table('consultation_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('consultation_requests', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable()->after('scheduled_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('consultation_requests')) {
            return;
        }

        Schema::table('consultation_requests', function (Blueprint $table) {
            if (Schema::hasColumn('consultation_requests', 'reminder_sent_at')) {
                $table->dropColumn('reminder_sent_at');
            }
        });
    }
};
