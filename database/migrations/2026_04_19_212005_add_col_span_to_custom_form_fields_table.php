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
        Schema::table('custom_form_fields', function (Blueprint $table) {
            $table->string('col_span')->default('full')->after('is_required'); // 'full' or 'half'
        });
    }

    public function down(): void
    {
        Schema::table('custom_form_fields', function (Blueprint $table) {
            $table->dropColumn('col_span');
        });
    }
};
