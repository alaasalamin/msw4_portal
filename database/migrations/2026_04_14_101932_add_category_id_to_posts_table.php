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
        Schema::table('posts', function (Blueprint $table) {
            // Column was already added in a partial run — only add the FK constraint
            if (!collect(\DB::select("SHOW COLUMNS FROM posts LIKE 'category_id'"))->isEmpty()) {
                $table->foreign('category_id')
                      ->references('id')
                      ->on('post_categories')
                      ->nullOnDelete();
            } else {
                $table->foreignId('category_id')
                      ->nullable()
                      ->after('author_id')
                      ->constrained('post_categories')
                      ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\PostCategory::class);
            $table->dropColumn('category_id');
        });
    }
};
