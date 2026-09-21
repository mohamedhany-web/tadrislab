<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Brief V3 expandable data model — nullable FKs + notification delivery ledger.
 * Does not force MVP flows; columns unlock future Institution/Package/Booking↔Order wiring.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'institution_id')) {
                    $table->foreignId('institution_id')->nullable()->after('user_id')->constrained('institutions')->nullOnDelete();
                }
                if (! Schema::hasColumn('orders', 'package_id')) {
                    $table->foreignId('package_id')->nullable()->after('service_package_id')->constrained('packages')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('consultation_requests')) {
            Schema::table('consultation_requests', function (Blueprint $table) {
                if (! Schema::hasColumn('consultation_requests', 'order_id')) {
                    $table->foreignId('order_id')->nullable()->after('student_id')->constrained('orders')->nullOnDelete();
                }
                if (! Schema::hasColumn('consultation_requests', 'institution_id')) {
                    $table->foreignId('institution_id')->nullable()->after('order_id')->constrained('institutions')->nullOnDelete();
                }
            });
        }

        if (! Schema::hasTable('notification_deliveries')) {
            Schema::create('notification_deliveries', function (Blueprint $table) {
                $table->id();
                $table->string('event_key', 80)->index();
                $table->string('channel', 32)->index();
                $table->string('status', 32)->index(); // delivered|failed|skipped
                $table->nullableMorphs('notifiable');
                $table->string('recipient_email')->nullable();
                $table->string('recipient_phone', 40)->nullable();
                $table->string('recipient_name')->nullable();
                $table->string('subject')->nullable();
                $table->text('body')->nullable();
                $table->json('provider_response')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();

                $table->index(['event_key', 'channel', 'status'], 'notif_delivery_event_channel_status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_deliveries');

        if (Schema::hasTable('consultation_requests')) {
            Schema::table('consultation_requests', function (Blueprint $table) {
                if (Schema::hasColumn('consultation_requests', 'institution_id')) {
                    $table->dropConstrainedForeignId('institution_id');
                }
                if (Schema::hasColumn('consultation_requests', 'order_id')) {
                    $table->dropConstrainedForeignId('order_id');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'package_id')) {
                    $table->dropConstrainedForeignId('package_id');
                }
                if (Schema::hasColumn('orders', 'institution_id')) {
                    $table->dropConstrainedForeignId('institution_id');
                }
            });
        }
    }
};
