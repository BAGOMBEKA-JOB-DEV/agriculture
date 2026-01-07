<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::where('email', 'test@example.com')->first();
        if (!$seller) return;

        $grains = ProductCategory::where('slug', 'grains-cereals')->first();
        $vegetables = ProductCategory::where('slug', 'vegetables')->first();
        $livestock = ProductCategory::where('slug', 'livestock')->first();

        $products = [
            [
                'name' => 'Premium White Maize',
                'description' => 'High-quality grade A white maize, dried and cleaned.',
                'seller_id' => $seller->id,
                'product_category_id' => $grains->id,
                'unit' => 'MT',
                'min_order' => 10,
                'price' => 450.00,
                'location' => 'Nakuru, Kenya',
                'images' => [
                    'https://images.unsplash.com/photo-1551727041-5b347d65b633?auto=format&fit=crop&q=80&w=800',
                    'https://images.unsplash.com/photo-1590273466070-40ca466b4432?auto=format&fit=crop&q=80&w=800'
                ],
                'status' => 'available',
            ],
            [
                'name' => 'Yellow Corn',
                'description' => 'Organic yellow corn suitable for animal feed and human consumption.',
                'seller_id' => $seller->id,
                'product_category_id' => $grains->id,
                'unit' => 'MT',
                'min_order' => 50,
                'price' => 380.00,
                'location' => 'Eldoret, Kenya',
                'images' => ['https://images.unsplash.com/photo-1590273466070-40ca466b4432?auto=format&fit=crop&q=80&w=800'],
                'status' => 'available',
            ],
            [
                'name' => 'Fresh Garden Tomatoes',
                'description' => 'Red ripe garden tomatoes, carefully harvested.',
                'seller_id' => $seller->id,
                'product_category_id' => $vegetables->id,
                'unit' => 'Crate',
                'min_order' => 20,
                'price' => 15.00,
                'location' => 'Naivasha, Kenya',
                'images' => ['https://images.unsplash.com/photo-1591857177580-dc82b9ac4e1e?auto=format&fit=crop&q=80&w=800'],
                'status' => 'available',
            ],
            [
                'name' => 'Boran Beef Cattle',
                'description' => 'Healthy Boran bulls, vaccinated and well-fed.',
                'seller_id' => $seller->id,
                'product_category_id' => $livestock->id,
                'unit' => 'Head',
                'min_order' => 1,
                'price' => 800.00,
                'location' => 'Laikipia, Kenya',
                'images' => ['https://images.unsplash.com/photo-1558024284-18ca1ebbbad4?auto=format&fit=crop&q=80&w=800'],
                'status' => 'available',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
