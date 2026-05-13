<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('object_types', function (Blueprint $table) {
            $table->longText('contract_template')->nullable()->after('attributes');
        });
    }

    public function down(): void
    {
        Schema::table('object_types', function (Blueprint $table) {
            $table->dropColumn('contract_template');
        });
    }
};
