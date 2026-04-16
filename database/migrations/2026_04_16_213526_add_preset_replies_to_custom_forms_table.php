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
        Schema::table('custom_forms', function (Blueprint $table) {
            $table->json('preset_replies')->nullable()->after('redirect_url');
        });
    }

    public function down(): void
    {
        Schema::table('custom_forms', function (Blueprint $table) {
            $table->dropColumn('preset_replies');
        });
    }
};
