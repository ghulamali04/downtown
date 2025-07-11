<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'role' => 'superadmin',
            'email' => 'superadmin@downtown.com',
            'password' => Hash::make('password')
        ]);

        User::factory()->create([
            'first_name' => 'Admin',
            'role' => 'admin',
            'email' => 'admin@downtown.com',
            'password' => Hash::make('password')
        ]);

        User::factory()->create([
            'first_name' => 'Kitchen',
            'role' => 'kitchen',
            'email' => 'kitchen@downtown.com',
            'password' => Hash::make('password')
        ]);

        User::factory()->create([
            'first_name' => 'Reception',
            'role' => 'reception',
            'email' => 'reception@downtown.com',
            'password' => Hash::make('password')
        ]);

        User::factory()->create([
            'first_name' => 'Staff',
            'role' => 'staff',
            'email' => 'staff@downtown.com',
            'password' => Hash::make('password')
        ]);

        SystemSetting::factory()->create([
            'type' => 'ipconfig',
            'payload' => '127.0.0.1,::1'
        ]);

        SystemSetting::factory()->create([
            'type' => 'service_charges',
            'payload' => 5
        ]);

        SystemSetting::factory()->create([
            'type' => 'print_mode',
            'payload' => 'tunnel'
        ]);
    }
}
