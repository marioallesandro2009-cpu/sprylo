<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'slug' => 'galaxy-note-20-ultra-5g', 'name' => 'Galaxy Note 20 Ultra 5G', 'category' => 'smartphones', 'brand' => 'samsung',
                'price' => 41700000, 'sale_price' => null, 'stock' => 52,
                'description' => 'Flagship Samsung phone with a 6.9" Dynamic AMOLED display, S Pen support, and a pro-grade triple camera system.',
                'images' => ['https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&q=80&auto=format&fit=crop'],
            ],
            [
                'slug' => 'ipad-10th-generation', 'name' => 'iPad 10th Generation', 'category' => 'tablets', 'brand' => 'apple',
                'price' => 29700000, 'sale_price' => 26700000, 'stock' => 32,
                'description' => 'A big, beautiful 10.9" Liquid Retina display, A14 Bionic chip, and support for Apple Pencil and Magic Keyboard Folio.',
                'images' => ['https://images.unsplash.com/photo-1561154464-82e9adf32764?w=500&q=80&auto=format&fit=crop'],
            ],
            [
                'slug' => 'apple-macbook-pro-m3', 'name' => 'Apple MacBook Pro M3', 'category' => 'laptops-desktops', 'brand' => 'apple',
                'price' => 37200000, 'sale_price' => null, 'stock' => 18,
                'description' => 'The M3 chip delivers a huge leap in performance and battery life, with a stunning Liquid Retina XDR display.',
                'images' => ['https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=500&q=80&auto=format&fit=crop'],
            ],
            [
                'slug' => 'canon-eos-r7-dslr', 'name' => 'Canon EOS R7 DSLR', 'category' => 'cameras', 'brand' => 'canon',
                'price' => 28800000, 'sale_price' => null, 'stock' => 24,
                'description' => 'A high-speed APS-C mirrorless camera built for wildlife and sports, with in-body image stabilization and 4K video.',
                'images' => ['https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=500&q=80&auto=format&fit=crop'],
            ],
            [
                'slug' => 'apple-watch-series-9', 'name' => 'Apple Watch Series 9', 'category' => 'smart-watches', 'brand' => 'apple',
                'price' => 10200000, 'sale_price' => null, 'stock' => 41,
                'description' => 'The most advanced Apple Watch yet, with a brighter display, the new S9 chip, and double tap gesture control.',
                'images' => ['https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&q=80&auto=format&fit=crop'],
            ],
            [
                'slug' => 'beats-studio-buds-pro', 'name' => 'Beats Studio Buds Pro', 'category' => 'headphones-buds', 'brand' => 'apple',
                'price' => 4200000, 'sale_price' => null, 'stock' => 67,
                'description' => 'Personalized spatial audio, active noise cancelling, and up to 24 hours of combined battery life.',
                'images' => ['https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=500&q=80&auto=format&fit=crop'],
            ],
            [
                'slug' => 'apple-homepod-2nd-gen', 'name' => 'Apple HomePod 2nd Gen Speaker', 'category' => 'accessories', 'brand' => 'apple',
                'price' => 4800000, 'sale_price' => 4200000, 'stock' => 22,
                'description' => 'Spatial audio, room-sensing tech, and the centrepiece of your Apple ecosystem. Built-in S7 chip, dynamic computational audio, and seamless handoff from iPhone. The new HomePod is designed to disappear into your room and fill it with sound.',
                'images' => [
                    'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=500&q=80&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=500&q=80&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1589003077984-894e133dabab?w=500&q=80&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1518770660439-4636190af475?w=500&q=80&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1574920162043-b872873f19c8?w=500&q=80&auto=format&fit=crop',
                ],
            ],
            [
                'slug' => 'power-wired-controller', 'name' => 'Power Wired Controller', 'category' => 'gaming', 'brand' => 'sony',
                'price' => 2850000, 'sale_price' => null, 'stock' => 34,
                'description' => 'A precision wired game controller with textured grips, mappable back buttons, and a braided 3m cable.',
                'images' => ['https://images.unsplash.com/photo-1592840496694-26d035b52b48?w=500&q=80&auto=format&fit=crop'],
            ],
            [
                'slug' => 'galaxy-s24-ultra-mint', 'name' => 'Galaxy S24 Ultra Mint', 'category' => 'smartphones', 'brand' => 'samsung',
                'price' => 21300000, 'sale_price' => null, 'stock' => 19,
                'description' => 'Titanium frame, a 200MP main camera, and Galaxy AI features baked right into the camera and keyboard.',
                'images' => ['https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=500&q=80&auto=format&fit=crop'],
            ],
            [
                'slug' => 'apple-airpods-v57', 'name' => 'Apple AirPods V57', 'category' => 'headphones-buds', 'brand' => 'apple',
                'price' => 10200000, 'sale_price' => null, 'stock' => 12,
                'description' => 'Adaptive audio, active noise cancellation, and a case that doubles as a Find My tracker.',
                'images' => ['https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=500&q=80&auto=format&fit=crop'],
            ],
            [
                'slug' => 'samsung-s21-ultra', 'name' => 'Samsung S21 Ultra', 'category' => 'smartphones', 'brand' => 'samsung',
                'price' => 41700000, 'sale_price' => 38700000, 'stock' => 24,
                'description' => 'A pro-grade 108MP camera system with 100x Space Zoom, wrapped in a Gorilla Glass Victus body.',
                'images' => ['https://images.unsplash.com/photo-1565849904461-04a58ad377e0?w=500&q=80&auto=format&fit=crop'],
            ],
            [
                'slug' => 'samsung-galaxy-note-20', 'name' => 'Samsung Galaxy Note 20', 'category' => 'smartphones', 'brand' => 'samsung',
                'price' => 41700000, 'sale_price' => null, 'stock' => 67,
                'description' => 'The classic Note experience — a built-in S Pen, a huge display, and all-day battery life.',
                'images' => ['https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=500&q=80&auto=format&fit=crop'],
            ],
            [
                'slug' => 'samsung-gear-camera', 'name' => 'Samsung Gear Camera', 'category' => 'cameras', 'brand' => 'samsung',
                'price' => 7800000, 'sale_price' => null, 'stock' => 30,
                'description' => 'A compact point-and-shoot with a fast lens and 4K video, built for everyday carry.',
                'images' => ['https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?w=500&q=80&auto=format&fit=crop'],
            ],
            [
                'slug' => 'gaming-mouse-pro', 'name' => 'Gaming Mouse Pro', 'category' => 'gaming', 'brand' => 'hp',
                'price' => 2850000, 'sale_price' => null, 'stock' => 36,
                'description' => 'A lightweight 19,000 DPI gaming mouse with on-board profile switching and RGB lighting.',
                'images' => ['https://images.unsplash.com/photo-1527814050087-3793815479db?w=500&q=80&auto=format&fit=crop'],
            ],
        ];

        foreach ($products as $data) {
            $category = Category::where('slug', $data['category'])->first();
            $brand = Brand::where('slug', $data['brand'])->first();

            $product = Product::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category_id' => $category->id,
                    'brand_id' => $brand?->id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'sale_price' => $data['sale_price'],
                    'stock' => $data['stock'],
                    'is_active' => true,
                ]
            );

            $product->images()->delete();
            foreach ($data['images'] as $sortOrder => $image) {
                $product->images()->create(['image' => $image, 'sort_order' => $sortOrder]);
            }
        }
    }
}
