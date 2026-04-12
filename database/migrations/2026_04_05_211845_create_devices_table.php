<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // e.g. REP-2024-0001

            // Assignment
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();

            // Customer
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();

            // Device
            $table->string('brand');
            $table->string('model');
            $table->string('serial_number')->nullable();
            $table->string('color')->nullable();
            $table->text('issue_description');
            $table->text('internal_notes')->nullable();

            // Workflow
            $table->enum('status', [
                'received',
                'diagnosing',
                'waiting_approval',
                'in_repair',
                'waiting_parts',
                'ready',
                'completed',
                'returned',
            ])->default('received');

            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            // Financials
            $table->decimal('estimated_cost', 8, 2)->nullable();
            $table->decimal('final_cost', 8, 2)->nullable();

            // Dates
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('estimated_completion')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
