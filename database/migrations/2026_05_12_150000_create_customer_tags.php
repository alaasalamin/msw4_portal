<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color', 16)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('customer_customer_tag', function (Blueprint $table) {
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_tag_id')->constrained('customer_tags')->cascadeOnDelete();
            $table->primary(['customer_id', 'customer_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_customer_tag');
        Schema::dropIfExists('customer_tags');
    }
};
