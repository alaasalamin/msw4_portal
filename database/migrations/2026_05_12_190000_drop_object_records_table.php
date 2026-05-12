<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('object_records');
    }

    public function down(): void
    {
        // Recreating with the same shape so this migration is reversible.
        Schema::create('object_records', function ($table) {
            $table->id();
            $table->foreignId('object_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->json('data')->nullable();
            $table->timestamps();
            $table->index('object_type_id');
            $table->index('customer_id');
        });
    }
};
