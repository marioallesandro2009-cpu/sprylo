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
            [
                'page_slug' => 'about',
                'title' => 'About Us — Sprylo',
                'meta_description' => 'Learn about Sprylo — our story, our team, and how to reach our flagship store.',
            ],
            [
                'page_slug' => 'categories',
                'title' => 'Categories — Sprylo',
                'meta_description' => 'Browse the Sprylo catalogue by category — smartphones, laptops, cameras, audio, wearables, and gaming.',
            ],
            [
                'page_slug' => 'brands',
                'title' => 'Brands — Sprylo',
                'meta_description' => 'Shop the Sprylo catalogue by brand.',
            ],
            [
                'page_slug' => 'wishlist',
                'title' => 'Wishlist — Sprylo',
                'meta_description' => 'Products you have saved for later at Sprylo.',
            ],
            [
                'page_slug' => 'help',
                'title' => 'Help Centre — Sprylo',
                'meta_description' => 'Answers to common questions about orders, shipping, returns, and warranty at Sprylo.',
            ],
            [
                'page_slug' => 'privacy-policy',
                'title' => 'Privacy Policy — Sprylo',
                'meta_description' => 'How Sprylo collects, uses, and protects your personal information.',
            ],
            [
                'page_slug' => 'checkout',
                'title' => 'Checkout — Sprylo',
                'meta_description' => 'Complete your order at Sprylo.',
            ],
        ];

        foreach ($pages as $page) {
            PageMeta::updateOrCreate(['page_slug' => $page['page_slug']], $page);
        }
    }
}
