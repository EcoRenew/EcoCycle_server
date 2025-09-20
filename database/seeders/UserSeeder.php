<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ecocycle.com',
            'password' => Hash::make('password'),
            // 'phone' => '1234567890',
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create collector users
        User::create([
            'name' => 'Collector One',
            'email' => 'collector1@ecocycle.com',
            'password' => Hash::make('password'),
            'phone' => '2345678901',
            'role' => 'collector',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Collector Two',
            'email' => 'collector2@ecocycle.com',
            'password' => Hash::make('password'),
            'phone' => '3456789012',
            'role' => 'collector',
            'status' => 'active',
        ]);

        // Create factory users
        User::create([
            'name' => 'Factory Manager',
            'email' => 'factory@ecocycle.com',
            'password' => Hash::make('password'),
            'phone' => '4567890123',
            'role' => 'factory',
            'status' => 'active',
        ]);

        // Create customer users
        User::create([
            'name' => 'Customer One',
            'email' => 'customer1@example.com',
            'password' => Hash::make('password'),
            'phone' => '5678901234',
            'role' => 'customer',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Customer Two',
            'email' => 'customer2@example.com',
            'password' => Hash::make('password'),
            'phone' => '6789012345',
            'role' => 'customer',
            'status' => 'active',
        ]);

        // Create more test users with factory
        User::factory()->count(20)->create();
    }
}