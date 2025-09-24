<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminsUsers = [
            [
                "name" => "karim",
                "email" => "karim.nasser@echocycle.com",
                "role" => "admin",
                "password" => Hash::make('password')
            ],
            [
                "name" => "abdulrahman",
                "email" => "abdulrahman.khawaga@echocycle.com",
                "role" => "admin",
                "password" => Hash::make('password')
            ],
            [
                "name" => "nariman",
                "email" => "nariman.awany@echocycle.com",
                "role" => "admin",
                "password" => Hash::make('password')
            ],
            [
                "name" => "mariam",
                "email" => "mariam.alsaeed@echocycle.com",
                "role" => "admin",
                "password" => Hash::make('password')
            ]
        ];
        foreach ($adminsUsers as $adminUser) {
            User::factory()->create([
                "name" => $adminUser['name'],
                "email" => $adminUser['email'],
                "role" => $adminUser['role'],
                "password" => $adminUser['password']
            ]);
        }

        $this->call(AdminSeeder::class);
        User::factory(200)->create();
        $this->call(CategorySeeder::class);
        $this->call(MaterialSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ProductMaterialSeeder::class);
        $this->call(TestRecyclingDataSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CommunityPostSeeder::class);
    }
}
