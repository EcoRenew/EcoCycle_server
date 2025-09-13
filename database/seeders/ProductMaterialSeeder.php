<?php

namespace Database\Seeders;

use App\Models\ProductMaterial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productMaterials = [
            // Recycled Shoes (product_id = 1)
            [
                'product_id' => 1,
                'material_id' => 2, // Polyester
                'quantity' => 0.8,  // 0.8 kg per shoe
            ],
            [
                'product_id' => 1,
                'material_id' => 4, // Rubber
                'quantity' => 0.5,
            ],
            [
                'product_id' => 1,
                'material_id' => 5, // Thread
                'quantity' => 0.05,
            ],

            // Recycled Dress (product_id = 2)
            [
                'product_id' => 2,
                'material_id' => 1, // Cotton
                'quantity' => 1.2,
            ],
            [
                'product_id' => 2,
                'material_id' => 2, // Polyester
                'quantity' => 0.6,
            ],
            [
                'product_id' => 2,
                'material_id' => 5, // Thread
                'quantity' => 0.1,
            ],

            // Recycled Bag (product_id = 3)
            [
                'product_id' => 3,
                'material_id' => 3, // Nylon Straps
                'quantity' => 0.3,
            ],
            [
                'product_id' => 3,
                'material_id' => 6, // Plastic/Metal (Zipper/Buttons)
                'quantity' => 0.1,
            ],
            [
                'product_id' => 3,
                'material_id' => 2, // Polyester
                'quantity' => 0.5,
            ],
        ];
        foreach ($productMaterials as $productMaterial) {
            ProductMaterial::create([
                'product_id' => $productMaterial['product_id'],
                'material_id' => $productMaterial['material_id'],
                'quantity' => $productMaterial['quantity']
            ]);
        }
    }
}
