<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Phone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Collector One
        $collector1 = User::create([
            'name' => 'Collector One',
            'email' => 'collector1@ecocycle.com',
            'password' => Hash::make('password'),
            'role' => 'collector',
            // 'status' => 'active',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        Phone::create([
            'user_id' => $collector1->user_id,
            'phone' => '2345678901',
            'is_primary' => true,
        ]);

        // Collector Two
        $collector2 = User::create([
            'name' => 'Collector Two',
            'email' => 'collector2@ecocycle.com',
            'password' => Hash::make('password'),
            'role' => 'collector',
            // 'status' => 'active',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        Phone::create([
            'user_id' => $collector2->user_id,
            'phone' => '3456789012',
            'is_primary' => true,
        ]);

        // Factory Manager
        $factory = User::create([
            'name' => 'Factory Manager',
            'email' => 'factory@ecocycle.com',
            'password' => Hash::make('password'),
            'role' => 'factory',
            // 'status' => 'active',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        Phone::create([
            'user_id' => $factory->user_id,
            'phone' => '4567890123',
            'is_primary' => true,
        ]);
    }
}
