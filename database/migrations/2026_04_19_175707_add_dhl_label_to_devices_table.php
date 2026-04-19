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
        Schema::table('devices', function (Blueprint $table) {
            $table->string('dhl_tracking_number')->nullable()->after('completed_at');
            $table->string('dhl_label_url')->nullable()->after('dhl_tracking_number');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['dhl_tracking_number', 'dhl_label_url']);
        });
    }
};
