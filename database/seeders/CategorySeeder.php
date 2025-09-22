<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $categories = [
            'Fabric'      => 'https://portal.bekia-egypt.com/storage/items/xVaMZGC47cbLREMB3HzpPy9nbo6rkCttgUJ1PaMq.png',
            'Accessories' => 'https://portal.bekia-egypt.com/storage/items/xVaMZGC47cbLREMB3HzpPy9nbo6rkCttgUJ1PaMq.png',
            'Rubber'      => 'https://portal.bekia-egypt.com/storage/items/xVaMZGC47cbLREMB3HzpPy9nbo6rkCttgUJ1PaMq.png',
            'Plastic'     => 'https://res.cloudinary.com/dc8sta0ea/image/upload/v1758486543/plastic_umcuv1.png',
            'Metals'      => 'https://res.cloudinary.com/dc8sta0ea/image/upload/v1758486558/metal_btiv5h.png',
            'Papers'      => 'https://res.cloudinary.com/dc8sta0ea/image/upload/v1758486565/carton_dlyszq.png',
            'Electronics' => 'https://res.cloudinary.com/dc8sta0ea/image/upload/v1758486572/electronics_lls5qb.png',
            'Oil'         => 'https://res.cloudinary.com/dc8sta0ea/image/upload/v1758486580/oil_bj54m3.png',
        ];

        foreach ($categories as $cate => $img) {
            DB::table('categories')->insert([
                'category_name' => $cate,
                'slug'          => Str::slug($cate),
                'image_url'     => $img,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }
}
