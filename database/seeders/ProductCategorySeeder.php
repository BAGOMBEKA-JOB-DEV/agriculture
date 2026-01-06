<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Grains & Cereals',
                'slug' => 'grains-cereals',
                'description' => 'Wheat, rice, corn, barley, and other grain products',
                'is_active' => true,
            ],
            [
                'name' => 'Flour',
                'slug' => 'flour',
                'description' => 'All types of flour products',
                'is_active' => true,
            ],
            [
                'name' => 'Vegetables',
                'slug' => 'vegetables',
                'description' => 'Fresh vegetables and produce',
                'is_active' => true,
            ],
            [
                'name' => 'Livestock',
                'slug' => 'livestock',
                'description' => 'Cattle, poultry, and other livestock',
                'is_active' => true,
            ],
            [
                'name' => 'Flowers',
                'slug' => 'flowers',
                'description' => 'Cut flowers and ornamental plants',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\ProductCategory::create($category);
        }
    }
}
