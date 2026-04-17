<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_page_employee', function (Blueprint $table) {
            $table->foreignId('custom_page_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->references('id')->on('users')->cascadeOnDelete();
            $table->primary(['custom_page_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_page_employee');
    }
};
