<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('employee', 'customer', 'partner', 'admin') NULL");
    }

    public function down(): void
    {
        // Move any admins to null before removing the value
        DB::statement("UPDATE users SET type = NULL WHERE type = 'admin'");
        DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('employee', 'customer', 'partner') NULL");
    }
};
