<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => 'admin@msw.local'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('admin1234'),
            ]
        );
    }
}
