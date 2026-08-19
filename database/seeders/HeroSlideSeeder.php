<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use App\Models\Product;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            [
                'size' => 'large', 'theme' => 'indigo', 'sort_order' => 0,
                'eyebrow' => '⚡ Audio · Featured', 'heading' => 'Apple HomePod 2nd Gen Speaker',
                'description' => 'Apple ecosystem with high-quality audio playback while serving as a hub for controlling smart home devices. Spatial audio, room-sensing tech.',
                'cta_label' => 'Shop Now',
                'product' => 'apple-homepod-2nd-gen',
                'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=900&q=80&auto=format&fit=crop',
            ],
            [
                'size' => 'small', 'theme' => 'violet', 'sort_order' => 1,
                'eyebrow' => 'Wearables', 'heading' => 'Explore Apple Watch',
                'product' => 'apple-watch-series-9',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&q=80&auto=format&fit=crop',
            ],
            [
                'size' => 'small', 'theme' => 'teal', 'sort_order' => 2,
                'eyebrow' => 'Latest Phones', 'heading' => 'Galaxy S24 Ultra · 5G',
                'product' => 'galaxy-s24-ultra-mint',
                'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600&q=80&auto=format&fit=crop',
            ],
            [
                'size' => 'small', 'theme' => 'orange', 'sort_order' => 3,
                'eyebrow' => 'Cameras', 'heading' => 'Samsung Gear Camera',
                'product' => 'samsung-gear-camera',
                'image' => 'https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?w=500&q=80&auto=format&fit=crop',
            ],
            [
                'size' => 'small', 'theme' => 'green', 'sort_order' => 4,
                'eyebrow' => 'Audio', 'heading' => 'Beats Studio Buds',
                'product' => 'beats-studio-buds-pro',
                'image' => 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=500&q=80&auto=format&fit=crop',
            ],
            [
                'size' => 'small', 'theme' => 'slate', 'sort_order' => 5,
                'eyebrow' => 'DSLR', 'heading' => 'Hero Camera X-Series',
                'product' => 'canon-eos-r7-dslr',
                'image' => 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=500&q=80&auto=format&fit=crop',
            ],
        ];

        foreach ($slides as $slide) {
            $product = Product::where('slug', $slide['product'])->first();

            HeroSlide::updateOrCreate(
                ['heading' => $slide['heading']],
                [
                    'size' => $slide['size'],
                    'theme' => $slide['theme'],
                    'sort_order' => $slide['sort_order'],
                    'eyebrow' => $slide['eyebrow'],
                    'description' => $slide['description'] ?? null,
                    'cta_label' => $slide['cta_label'] ?? null,
                    'image' => $slide['image'],
                    'product_id' => $product?->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
