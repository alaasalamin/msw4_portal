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
            $table->string('crm_key', 64)->nullable()->unique()->after('slug');
            $table->boolean('crm_sync')->default(false)->after('crm_key');
        });
    }

    public function down(): void
    {
        Schema::table('custom_forms', function (Blueprint $table) {
            $table->dropColumn(['crm_key', 'crm_sync']);
        });
    }
};
