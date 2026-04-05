<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tracking_number')->nullable()->unique();
            $table->string('type')->default('domestic'); // domestic | international
            $table->string('status')->default('created');

            // Sender
            $table->string('sender_name');
            $table->string('sender_company')->nullable();
            $table->string('sender_street');
            $table->string('sender_house_number');
            $table->string('sender_postal_code');
            $table->string('sender_city');
            $table->string('sender_country')->default('DEU');
            $table->string('sender_email')->nullable();
            $table->string('sender_phone')->nullable();

            // Recipient
            $table->string('recipient_name');
            $table->string('recipient_company')->nullable();
            $table->string('recipient_street');
            $table->string('recipient_house_number');
            $table->string('recipient_postal_code');
            $table->string('recipient_city');
            $table->string('recipient_country')->default('DEU');
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();

            // Parcel
            $table->float('weight_kg');
            $table->string('reference')->nullable();

            // DHL response
            $table->string('label_url')->nullable();
            $table->binary('label_pdf')->nullable();
            $table->json('dhl_response')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
