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
            // Category 1: Fabric
            ['material_name' => 'Cotton', 'price_per_unit' => 1.00, 'category_id' => 1, 'default_unit' => 'kg', 'units' => ['kg', 'pieces']],
            ['material_name' => 'Polyester', 'price_per_unit' => 1.00, 'category_id' => 1, 'default_unit' => 'kg', 'units' => ['kg', 'pieces']],
            ['material_name' => 'Nylon Straps', 'price_per_unit' => 1.00, 'category_id' => 2, 'default_unit' => 'kg', 'units' => ['kg']],

            // Category 2: Accessories
            ['material_name' => 'Rubber', 'price_per_unit' => 1.00, 'category_id' => 3, 'default_unit' => 'kg', 'units' => ['kg']],
            ['material_name' => 'Thread', 'price_per_unit' => 1.00, 'category_id' => 3, 'default_unit' => 'kg', 'units' => ['kg']],
            ['material_name' => 'Plastic/Metal (Zipper/Buttons)', 'price_per_unit' => 1.00, 'category_id' => 3, 'default_unit' => 'kg', 'units' => ['kg']],

            // Category 4: Plastic
            ['material_name' => 'PET Bottles', 'price_per_unit' => 1.50, 'category_id' => 4, 'default_unit' => 'kg', 'units' => ['kg', 'pieces']],
            ['material_name' => 'Plastic Bags', 'price_per_unit' => 0.80, 'category_id' => 4, 'default_unit' => 'kg', 'units' => ['kg']],
            ['material_name' => 'HDPE Containers', 'price_per_unit' => 2.00, 'category_id' => 4, 'default_unit' => 'kg', 'units' => ['kg', 'pieces']],

            // Category 5: Metals
            ['material_name' => 'Aluminum Cans', 'price_per_unit' => 2.50, 'category_id' => 5, 'default_unit' => 'kg', 'units' => ['kg', 'pieces']],
            ['material_name' => 'Steel Scrap', 'price_per_unit' => 1.80, 'category_id' => 5, 'default_unit' => 'kg', 'units' => ['kg']],
            ['material_name' => 'Copper Wires', 'price_per_unit' => 3.20, 'category_id' => 5, 'default_unit' => 'kg', 'units' => ['kg']],

            // Category 6: Papers
            ['material_name' => 'Cardboard', 'price_per_unit' => 0.50, 'category_id' => 6, 'default_unit' => 'kg', 'units' => ['kg']],
            ['material_name' => 'Office Paper', 'price_per_unit' => 0.70, 'category_id' => 6, 'default_unit' => 'kg', 'units' => ['kg']],
            ['material_name' => 'Newspapers', 'price_per_unit' => 0.60, 'category_id' => 6, 'default_unit' => 'kg', 'units' => ['kg']],

            // Category 7: Electronics
            ['material_name' => 'Circuit Boards', 'price_per_unit' => 4.50, 'category_id' => 7, 'default_unit' => 'kg', 'units' => ['kg']],
            ['material_name' => 'Mobile Phones', 'price_per_unit' => 6.00, 'category_id' => 7, 'default_unit' => 'pieces', 'units' => ['pieces']],
            ['material_name' => 'Computer Parts', 'price_per_unit' => 5.00, 'category_id' => 7, 'default_unit' => 'kg', 'units' => ['kg']],

            // Category 8: Oil
            ['material_name' => 'Used Cooking Oil', 'price_per_unit' => 1.20, 'category_id' => 8, 'default_unit' => 'liters', 'units' => ['liters']],
            ['material_name' => 'Lubricant Oil', 'price_per_unit' => 2.50, 'category_id' => 8, 'default_unit' => 'liters', 'units' => ['liters']],
            ['material_name' => 'Motor Oil', 'price_per_unit' => 2.80, 'category_id' => 8, 'default_unit' => 'liters', 'units' => ['liters']],
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
                'points_per_kg'   => $mat['points_per_kg'] ?? null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }
    }
}
