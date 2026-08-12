<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Smartphones', 'slug' => 'smartphones', 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&q=80&auto=format&fit=crop'],
            ['name' => 'Laptops & Desktops', 'slug' => 'laptops-desktops', 'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=300&q=80&auto=format&fit=crop'],
            ['name' => 'Tablets', 'slug' => 'tablets', 'image' => 'https://images.unsplash.com/photo-1561154464-82e9adf32764?w=300&q=80&auto=format&fit=crop'],
            ['name' => 'Smart Watches', 'slug' => 'smart-watches', 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300&q=80&auto=format&fit=crop'],
            ['name' => 'Cameras', 'slug' => 'cameras', 'image' => 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=300&q=80&auto=format&fit=crop'],
            ['name' => 'Headphones & Buds', 'slug' => 'headphones-buds', 'image' => 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=300&q=80&auto=format&fit=crop'],
            ['name' => 'Gaming', 'slug' => 'gaming', 'image' => 'https://images.unsplash.com/photo-1592840496694-26d035b52b48?w=300&q=80&auto=format&fit=crop'],
            ['name' => 'Accessories', 'slug' => 'accessories', 'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&q=80&auto=format&fit=crop'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
