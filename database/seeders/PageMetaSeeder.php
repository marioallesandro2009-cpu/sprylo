<?php

namespace Database\Seeders;

use App\Models\PageMeta;
use Illuminate\Database\Seeder;

class PageMetaSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'page_slug' => 'home',
                'title' => 'Sprylo — Tech & Gadgets Ecommerce HTML Template',
                'meta_description' => 'Sprylo is a free modern HTML template for tech and electronics stores — multi-color bento hero, indigo primary palette, 5 fully responsive pages, no framework, no build step.',
            ],
            [
                'page_slug' => 'shop',
                'title' => 'Shop — Sprylo',
                'meta_description' => 'Browse the full Sprylo catalogue — smartphones, laptops, cameras, audio, wearables, and gaming. Filter by brand, price, and rating.',
            ],
            [
                'page_slug' => 'cart',
                'title' => 'Cart — Sprylo',
                'meta_description' => 'Your Sprylo shopping cart — review items, apply a promo code, and proceed to secure checkout.',
            ],
            [
                'page_slug' => 'contact',
                'title' => 'Contact — Sprylo',
                'meta_description' => 'Get in touch with Sprylo customer support — order help, returns, warranty claims, and partnership inquiries.',
            ],
        ];

        foreach ($pages as $page) {
            PageMeta::updateOrCreate(['page_slug' => $page['page_slug']], $page);
        }
    }
}
