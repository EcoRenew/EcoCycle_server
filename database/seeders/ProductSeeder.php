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
                'img' => 'https://res.cloudinary.com/dooqrobue/image/upload/v1757857523/shoes-hove-img_zc0jcu.png',
                'hover_img' => 'https://res.cloudinary.com/dooqrobue/image/upload/v1757857067/shoes-img_wfyice.png',
                'price' => 500,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [

                'name' => 'Recycled Dress',
                'description' => 'A sustainable dress made from recycled fabrics, combining style with responsibility. Lightweight, comfortable, and eco-friendly, this dress is perfect for everyday wear while reducing textile waste and supporting a greener future',
                'img' => 'https://res.cloudinary.com/dooqrobue/image/upload/v1757857017/dress-hover-img_dreznu.jpg',
                'hover_img' => 'https://res.cloudinary.com/dooqrobue/image/upload/v1757856624/dress-img_xm9jsw.jpg',
                'price' => 800,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [

                'name' => 'Recycled Bag',
                'description' => '"A durable and stylish bag crafted from recycled materials. Designed for everyday use, it combines functionality with sustainability, offering strength, practicality, and an eco-friendly choice for reducing waste.',
                'img' => 'https://res.cloudinary.com/dooqrobue/image/upload/v1757855357/bag-img_zuabv6.jpg',
                'hover_img' => 'https://res.cloudinary.com/dooqrobue/image/upload/v1757855420/bag-hover-img_rxvake.jpg',
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
