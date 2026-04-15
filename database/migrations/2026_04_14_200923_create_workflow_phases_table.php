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
        Schema::create('workflow_phases', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed the two default phases from existing data
        $now = now();
        \DB::table('workflow_phases')->insert([
            ['label' => 'Phase 1: Initial Diagnosis & Preparation', 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Phase 2: Completion & Logistics',          'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_phases');
    }
};
