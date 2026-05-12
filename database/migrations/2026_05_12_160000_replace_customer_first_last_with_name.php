<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('name')->after('id')->nullable();
        });

        DB::table('customers')->orderBy('id')->each(function ($row) {
            DB::table('customers')->where('id', $row->id)->update([
                'name' => trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')),
            ]);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->dropColumn(['first_name', 'last_name']);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('first_name')->after('id')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
        });

        DB::table('customers')->orderBy('id')->each(function ($row) {
            $parts = preg_split('/\s+/', trim($row->name ?? ''), 2);
            DB::table('customers')->where('id', $row->id)->update([
                'first_name' => $parts[0] ?? '',
                'last_name'  => $parts[1] ?? '',
            ]);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            $table->dropColumn('name');
        });
    }
};
