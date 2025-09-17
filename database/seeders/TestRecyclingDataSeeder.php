<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Address;
use App\Models\Category;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class TestRecyclingDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create or get a test user with role 'user' (so they can access user routes)
        $user = User::firstOrCreate(
            ['email' => 'request.user@example.com'],
            [
                'name' => 'Request User',
                'password' => Hash::make('password'),
                'phone' => '01000000000',
                'role' => 'user',
            ]
        );

        // Create a pickup address for the user
        $address = Address::firstOrCreate(
            ['user_id' => $user->user_id, 'street' => '123 Testing St', 'city' => 'Cairo']
        );

        // Ensure there is at least one category
        $category = Category::first();
        if (!$category) {
            $category = Category::create([
                'category_name' => 'Fabric',
                'parent_category_id' => null,
            ]);
        }

        // Ensure material with ID 1 exists and is Cotton
        $material = Material::find(1);
        if (!$material) {
            // Create with explicit ID = 1
            DB::table('materials')->insert([
                'material_id' => 1,
                'material_name' => 'Cotton',
                'price_per_unit' => 10.00,
                'unit' => 'kg',
                'category_id' => $category->category_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $material = Material::find(1);
        } else {
            $material->update([
                'material_name' => 'Cotton',
                'price_per_unit' => 10.00,
                'unit' => 'kg',
                'category_id' => $category->category_id,
            ]);
        }

        // Output for reference in logs
        $this->command?->info('Seeded test user: request.user@example.com / password');
        $this->command?->info('Pickup address_id: ' . $address->address_id);
        $this->command?->info('Cotton material_id: ' . $material->material_id);
    }
}



