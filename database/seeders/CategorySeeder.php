<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Fabric', 'Accessories', 'Rubber', ];

        foreach ($categories as $cate) {
            DB::table('categories')->insert([
                'category_name' => $cate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
