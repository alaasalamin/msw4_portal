<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('device_id')->constrained()->cascadeOnDelete();

            // Who wrote it — stored at write-time so history survives account changes
            $table->string('author_name');
            $table->enum('author_role', ['admin', 'employee', 'customer', 'partner']);

            // Polymorphic: Admin (admins table) or User (users table)
            $table->nullableMorphs('authorable');

            // Content
            $table->text('content');
            $table->enum('type', ['text', 'image'])->default('text');

            // Visibility: false = internal (employees + admins only), true = public (customers + partners see it too)
            $table->boolean('is_public')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_notes');
    }
};
