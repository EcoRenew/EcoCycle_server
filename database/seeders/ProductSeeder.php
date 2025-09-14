<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Recycled Shoes',
                'description' => 'Eco-friendly shoes crafted from recycled plastics and sustainably sourced materials. Designed for comfort, durability, and a smaller environmental footprint, these recycled shoes give waste a second life while keeping your style modern and versatile.',
                'img' => 'img',
                'hover_img' => 'hover img',
                'price' => 500,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [

                'name' => 'Recycled Dress',
                'description' => 'A sustainable dress made from recycled fabrics, combining style with responsibility. Lightweight, comfortable, and eco-friendly, this dress is perfect for everyday wear while reducing textile waste and supporting a greener future',
                'img' => 'img',
                'hover_img' => 'hover img',
                'price' => 800,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [

                'name' => 'Recycled Bag',
                'description' => '"A durable and stylish bag crafted from recycled materials. Designed for everyday use, it combines functionality with sustainability, offering strength, practicality, and an eco-friendly choice for reducing waste.',
                'img' => 'img',
                'hover_img' => 'hover img',
                'price' => 300,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];
        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'description' => $product['description'],
                'img' => $product['img'],
                'hover_img' => $product['hover_img'],
                'price' => $product['price'],
                'created_at' => $product['created_at'],
                'updated_at' => $product['updated_at']
            ]);
        }
    }
}
