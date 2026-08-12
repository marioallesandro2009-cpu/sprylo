<?php

namespace Database\Seeders;

use App\Models\HeroHighlight;
use App\Models\Product;
use Illuminate\Database\Seeder;

class HeroHighlightSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            [
                'position' => 'purple', 'eyebrow' => 'Wearables', 'heading' => 'Explore Apple Watch',
                'product' => 'apple-watch-series-9',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&q=80&auto=format&fit=crop',
            ],
            [
                'position' => 'teal', 'eyebrow' => 'Latest Phones', 'heading' => 'Galaxy S24 Ultra · 5G',
                'product' => 'galaxy-s24-ultra-mint',
                'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600&q=80&auto=format&fit=crop',
            ],
            [
                'position' => 'orange', 'eyebrow' => 'Cameras', 'heading' => 'Samsung Gear Camera',
                'product' => 'samsung-gear-camera',
                'image' => 'https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?w=500&q=80&auto=format&fit=crop',
            ],
            [
                'position' => 'green', 'eyebrow' => 'Audio', 'heading' => 'Beats Studio Buds',
                'product' => 'beats-studio-buds-pro',
                'image' => 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=500&q=80&auto=format&fit=crop',
            ],
            [
                'position' => 'black', 'eyebrow' => 'DSLR', 'heading' => 'Hero Camera X-Series',
                'product' => 'canon-eos-r7-dslr',
                'image' => 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=500&q=80&auto=format&fit=crop',
            ],
        ];

        foreach ($slides as $slide) {
            $product = Product::where('slug', $slide['product'])->first();

            HeroHighlight::updateOrCreate(
                ['position' => $slide['position']],
                [
                    'eyebrow' => $slide['eyebrow'],
                    'heading' => $slide['heading'],
                    'image' => $slide['image'],
                    'product_id' => $product?->id,
                ]
            );
        }
    }
}
