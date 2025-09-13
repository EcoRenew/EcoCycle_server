<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            ['material_name' => 'cotton', 'price_per_unit' => 1, 'category_id' => 1, 'unit' => 'kg'],
            ['material_name' => 'Polyester', 'price_per_unit' => 1, 'category_id' => 1, 'unit' => 'kg'],
            ['material_name' => 'Nylon Straps', 'price_per_unit' => 1, 'category_id' => 2, 'unit' => 'kg'],
            ['material_name' => 'Rubber', 'price_per_unit' => 1, 'category_id' => 3, 'unit' => 'kg'],
            ['material_name' => 'Thread', 'price_per_unit' => 1, 'category_id' => 3, 'unit' => 'kg'],
            ['material_name' => 'Plastic/Metal (Zipper/Buttons)', 'category_id' => 3, 'price_per_unit' => 1, 'unit' => 'kg'],
        ];

        foreach ($materials as $mat) {
            Material::create($mat);
        }
    }
}
