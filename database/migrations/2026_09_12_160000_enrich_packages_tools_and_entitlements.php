<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Packages Brief V3: tools/resources list, discount note, and user entitlements after activation.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('packages')) {
            Schema::table('packages', function (Blueprint $table) {
                if (! Schema::hasColumn('packages', 'tools_resources')) {
                    $table->json('tools_resources')->nullable()->after('includes_tools');
                }
                if (! Schema::hasColumn('packages', 'discount_note')) {
                    $table->string('discount_note', 500)->nullable()->after('original_price');
                }
            });
        }

        if (! Schema::hasTable('user_package_entitlements')) {
            Schema::create('user_package_entitlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->foreignId('activated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedInteger('consultation_sessions_total')->default(0);
                $table->unsignedInteger('consultation_sessions_remaining')->default(0);
                $table->unsignedInteger('participant_seats')->nullable();
                $table->boolean('includes_tools')->default(false);
                $table->json('tools_resources')->nullable();
                $table->string('status', 32)->default('active'); // active|expired|cancelled
                $table->timestamp('activated_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'package_id'], 'user_package_entitlement_unique');
                $table->index(['user_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_package_entitlements');

        if (Schema::hasTable('packages')) {
            Schema::table('packages', function (Blueprint $table) {
                foreach (['discount_note', 'tools_resources'] as $col) {
                    if (Schema::hasColumn('packages', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
