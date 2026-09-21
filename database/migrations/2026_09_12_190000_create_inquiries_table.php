<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Inquiries (Brief V3 §11) — internal follow-up even when chat starts on WhatsApp.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inquiries')) {
            return;
        }

        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 32)->nullable()->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('inquiry_type', 40)->default('general');
            // learning_path|package|consultation|payment|technical|school_institution|general
            $table->string('status', 24)->default('new'); // new|in_progress|resolved
            $table->string('source', 32)->default('contact_form');
            // contact_form|whatsapp|admin|institution_form|other
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('consultation_request_id')->nullable()->constrained('consultation_requests')->nullOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->foreignId('contact_message_id')->nullable()->constrained('contact_messages')->nullOnDelete();
            $table->string('subject', 255)->nullable();
            $table->text('message')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('inquired_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'inquiry_type']);
            $table->index(['inquired_at']);
            $table->index(['source']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
