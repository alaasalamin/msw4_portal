<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('trigger_type'); // step_changed | customer_approved | customer_declined | payment_received
            $table->json('trigger_config')->nullable(); // {"step_id": 5} or {}
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('automation_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('automation_rules')->cascadeOnDelete();
            $table->string('action_type'); // send_allowance | notify_employee | send_email | change_step | generate_invoice
            $table->json('action_config')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rule_id')->nullable();
            $table->string('rule_name');
            $table->string('trigger_type');
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action_type');
            $table->string('status'); // success | failed | skipped
            $table->json('payload')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });

        Schema::create('device_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('rule_id')->nullable();
            $table->uuid('token')->unique();
            $table->string('status')->default('pending'); // pending | approved | declined
            $table->string('customer_email');
            $table->string('customer_name')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_allowances');
        Schema::dropIfExists('automation_logs');
        Schema::dropIfExists('automation_actions');
        Schema::dropIfExists('automation_rules');
    }
};
