<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_pages', function (Blueprint $table) {
            // Theme Builder section list — distinct from the legacy `sections`
            // column used by the older block editor at /admin/site-pages.
            $table->json('theme_sections')->nullable()->after('sections');
        });
    }

    public function down(): void
    {
        Schema::table('site_pages', function (Blueprint $table) {
            $table->dropColumn('theme_sections');
        });
    }
};
