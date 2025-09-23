<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Address;
use App\Models\Category;
use App\Models\Material;
use Illuminate\Support\Facades\DB;
use App\Models\Phone;
use Illuminate\Support\Str;

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
                // 'phone' => '01000000000',
                'role' => 'user',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
        Phone::create([
            'user_id' => $user->user_id,
            'phone' => '1234567890',
        ]);
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

        $imageUrl = 'https://portal.bekia-egypt.com/storage/items/xVaMZGC47cbLREMB3HzpPy9nbo6rkCttgUJ1PaMq.png';

        // Ensure material with ID 1 exists and is Cotton
        $material = Material::find(1);
        if (!$material) {
            // Create with explicit ID = 1 using DB to set material_id
            DB::table('materials')->insert([
                'material_id'    => 1,
                'material_name'  => 'Cotton',
                'price_per_unit' => 10.00,
                'default_unit'   => 'kg',
                'units'          => json_encode(['kg']),
                'image_url'      => $imageUrl,
                'category_id'    => $category->category_id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
            $material = Material::find(1);
        } else {
            $material->update([
                'material_name'  => 'Cotton',
                'price_per_unit' => 10.00,
                'default_unit'   => 'kg',
                'units'          => ['kg'],
                'image_url'      => $imageUrl,
                'category_id'    => $category->category_id,
            ]);
        }

        //
        // Add test requests/items for dashboard testing:
        // - 2 Donation requests (with request_items)
        // - 2 Recycling requests (one Completed, one Pending for upcoming pickups)
        //
        // Donation 1 (completed)
        $donation1Id = DB::table('requests')->insertGetId([
            'customer_id' => $user->user_id,
            'pickup_address_id' => $address->address_id,
            'request_type' => 'Donation',
            'status' => 'Completed',
            'pickup_date' => now()->subDays(12),
            'created_at' => now()->subDays(15),
            'updated_at' => now()->subDays(12),
        ]);
        DB::table('request_items')->insert([
            'request_id' => $donation1Id,
            'material_id' => $material->material_id,
            'quantity' => 5,
            'calculated_price' => 5 * ($material->price_per_unit ?? 0),
            'created_at' => now()->subDays(15),
            'updated_at' => now()->subDays(15),
        ]);

        // Donation 2 (completed)
        $donation2Id = DB::table('requests')->insertGetId([
            'customer_id' => $user->user_id,
            'pickup_address_id' => $address->address_id,
            'request_type' => 'Donation',
            'status' => 'Completed',
            'pickup_date' => now()->subDays(6),
            'created_at' => now()->subDays(8),
            'updated_at' => now()->subDays(6),
        ]);
        DB::table('request_items')->insert([
            'request_id' => $donation2Id,
            'material_id' => $material->material_id,
            'quantity' => 3,
            'calculated_price' => 3 * ($material->price_per_unit ?? 0),
            'created_at' => now()->subDays(8),
            'updated_at' => now()->subDays(8),
        ]);

        // Recycling 1 (Completed) - counts toward total_recycled_items
        $recycle1Id = DB::table('requests')->insertGetId([
            'customer_id' => $user->user_id,
            'pickup_address_id' => $address->address_id,
            'request_type' => 'Recycling',
            'status' => 'Completed',
            'pickup_date' => now()->subDays(20),
            'created_at' => now()->subDays(22),
            'updated_at' => now()->subDays(20),
        ]);
        DB::table('request_items')->insert([
            'request_id' => $recycle1Id,
            'material_id' => $material->material_id,
            'quantity' => 12,
            'calculated_price' => 12 * ($material->price_per_unit ?? 0),
            'created_at' => now()->subDays(22),
            'updated_at' => now()->subDays(22),
        ]);

        // Recycling 2 (Pending) - should appear in upcoming pickups
        $recycle2Id = DB::table('requests')->insertGetId([
            'customer_id' => $user->user_id,
            'pickup_address_id' => $address->address_id,
            'request_type' => 'Recycling',
            'status' => 'Pending',
            'pickup_date' => now()->addDays(5),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('request_items')->insert([
            'request_id' => $recycle2Id,
            'material_id' => $material->material_id,
            'quantity' => 8,
            'calculated_price' => 8 * ($material->price_per_unit ?? 0),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Output for reference in logs
        $this->command?->info('Seeded test user: request.user@example.com / password');
        $this->command?->info('Pickup address_id: ' . $address->address_id);
        $this->command?->info('Cotton material_id: ' . $material->material_id);
    }
}
