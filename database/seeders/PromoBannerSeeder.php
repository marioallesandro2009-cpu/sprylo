<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\PromoBanner;
use Illuminate\Database\Seeder;

class PromoBannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            ['product' => 'ipad-10th-generation', 'label' => 'THIS WEEK ONLY', 'theme' => 'slate', 'sort_order' => 0],
            ['product' => 'apple-homepod-2nd-gen', 'label' => 'LIMITED EDITION', 'theme' => 'violet', 'sort_order' => 1],
        ];

        foreach ($banners as $banner) {
            $product = Product::where('slug', $banner['product'])->first();

            if (! $product) {
                continue;
            }

            PromoBanner::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'label' => $banner['label'],
                    'theme' => $banner['theme'],
                    'sort_order' => $banner['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
