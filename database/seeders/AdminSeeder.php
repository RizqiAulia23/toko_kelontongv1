<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::firstOrCreate(
            ['username' => 'admin'],
            [
                'admin_id' => 1,
                'admin_name' => 'Vlonix Lead Admin',
                'password' => Hash::make('admin123'),
                'admin_telp' => '+62 812-8899-0011',
                'admin_email' => 'admin@vlonix.local',
                'admin_address' => 'VLONIX Operations Center, Floor 9, Jakarta',
                'level' => 'admin',
            ]
        );
    }
}
