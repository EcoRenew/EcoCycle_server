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

        $materials = [
            ['material_name' => 'cotton', 'price_per_unit' => 1.00, 'category_id' => 1, 'default_unit' => 'kg', 'units' => ['kg', 'pieces']],
            ['material_name' => 'Polyester', 'price_per_unit' => 1.00, 'category_id' => 1, 'default_unit' => 'kg', 'units' => ['kg', 'pieces']],
            ['material_name' => 'Nylon Straps', 'price_per_unit' => 1.00, 'category_id' => 2, 'default_unit' => 'kg', 'units' => ['kg']],
            ['material_name' => 'Rubber', 'price_per_unit' => 1.00, 'category_id' => 3, 'default_unit' => 'kg', 'units' => ['kg']],
            ['material_name' => 'Thread', 'price_per_unit' => 1.00, 'category_id' => 3, 'default_unit' => 'kg', 'units' => ['kg']],
            ['material_name' => 'Plastic/Metal (Zipper/Buttons)', 'price_per_unit' => 1.00, 'category_id' => 3, 'default_unit' => 'kg', 'units' => ['kg']],
        ];

        $now = now();

        foreach ($materials as $mat) {
            DB::table('materials')->insert([
                'material_name'   => $mat['material_name'],
                'description'     => $mat['description'] ?? null,
                'price_per_unit'  => $mat['price_per_unit'],
                'default_unit'    => $mat['default_unit'] ?? null,
                'units'           => json_encode($mat['units'] ?? []), // JSON column
                'image_url'       => $imageUrl,
                'category_id'     => $mat['category_id'],
                'points_per_kg'   => $mat['points_per_kg'] ?? null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }
    }
}
