<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $imageUrl = 'https://portal.bekia-egypt.com/storage/items/xVaMZGC47cbLREMB3HzpPy9nbo6rkCttgUJ1PaMq.png';
        $stock_num = 100;
        $materials = [
            [
                'material_name' => 'Cotton',
                'price_per_unit' => 50.00, 
                'category_id' => 1,
                'default_unit' => 'kg',
                'units' => ['kg'],
                'points' => 10,
            ],
            [
                'material_name' => 'Polyester',
                'price_per_unit' => 40.00,
                'category_id' => 1,
                'default_unit' => 'kg',
                'units' => ['kg'],
                'points' => 8,
            ],
            [
                'material_name' => 'Nylon Straps',
                'price_per_unit' => 30.00,
                'category_id' => 2,
                'default_unit' => 'kg',
                'units' => ['kg', 'piece'],
                'points' => 6,
            ],
            [
                'material_name' => 'Rubber',
                'price_per_unit' => 60.00,
                'category_id' => 3,
                'default_unit' => 'kg',
                'units' => ['kg'],
                'points' => 12,
            ],
            [
                'material_name' => 'Thread',
                'price_per_unit' => 25.00,
                'category_id' => 3,
                'default_unit' => 'kg',
                'units' => ['kg', 'roll'],
                'points' => 5,
            ],
            [
                'material_name' => 'Plastic/Metal (Zipper/Buttons)',
                'price_per_unit' => 80.00,
                'category_id' => 3,
                'default_unit' => 'kg',
                'units' => ['kg', 'piece'],
                'points' => 15,
            ],
            [
                'material_name' => 'Glass Bottles',
                'price_per_unit' => 2.00, 
                'category_id' => 4,
                'default_unit' => 'piece',
                'units' => ['piece', 'kg'],
                'points' => 2,
            ],
            [
                'material_name' => 'Aluminum Cans',
                'price_per_unit' => 100.00, 
                'category_id' => 4,
                'default_unit' => 'kg',
                'units' => ['kg', 'piece'],
                'points' => 20,
            ],
            [
                'material_name' => 'Paper/Cardboard',
                'price_per_unit' => 15.00,
                'category_id' => 5,
                'default_unit' => 'kg',
                'units' => ['kg'],
                'points' => 3,
            ],
            [
                'material_name' => 'Electronics',
                'price_per_unit' => 500.00,
                'category_id' => 6,
                'default_unit' => 'piece',
                'units' => ['piece', 'kg'],
                'points' => 100,
            ],
        ];

        $now = now();

        foreach ($materials as $mat) {
            DB::table('materials')->insert([
                'material_name'   => $mat['material_name'],
                'description'     => $mat['description'] ?? null,
                'price_per_unit'  => $mat['price_per_unit'],
                'default_unit'    => $mat['default_unit'] ?? null,
                'units'           => json_encode($mat['units'] ?? []),
                'stock'           => $stock_num,
                'image_url'       => $imageUrl,
                'category_id'     => $mat['category_id'],
                'points'   => $mat['points'] ?? null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }
    }
}
