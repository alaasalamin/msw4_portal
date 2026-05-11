<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color', 16)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('customer_groups')->insert([
            ['name' => 'B2C', 'color' => '#0ea5e9', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'B2B', 'color' => '#10b981', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('customers', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('last_name');
            $table->foreignId('customer_group_id')
                ->nullable()
                ->after('company_name')
                ->constrained('customer_groups')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_group_id');
            $table->dropColumn('company_name');
        });

        Schema::dropIfExists('customer_groups');
    }
};
