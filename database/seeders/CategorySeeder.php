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
        $imageUrl = 'https://portal.bekia-egypt.com/storage/items/xVaMZGC47cbLREMB3HzpPy9nbo6rkCttgUJ1PaMq.png';

        $categories = ['Fabric','Accessories','Rubber','Bottles','Paper','Electronics'];


        $now = now();

        foreach ($categories as $cate) {
            DB::table('categories')->insert([
                'category_name' => $cate,
                'slug' => Str::slug($cate),
                'image_url' => $imageUrl,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
