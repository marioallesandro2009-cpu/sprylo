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
                'title' => 'solis — Gadgets, gizmos & good deals',
                'meta_description' => 'solis is a modern online store for tech and electronics: smartphones, laptops, cameras, audio, and wearables.',
            ],
            [
                'page_slug' => 'shop',
                'title' => 'Shop — solis',
                'meta_description' => 'Browse the full solis catalogue — smartphones, laptops, cameras, audio, wearables, and gaming. Filter by brand, price, and rating.',
            ],
            [
                'page_slug' => 'cart',
                'title' => 'Cart — solis',
                'meta_description' => 'Your solis shopping cart — review items, apply a promo code, and proceed to secure checkout.',
            ],
            [
                'page_slug' => 'contact',
                'title' => 'Contact — solis',
                'meta_description' => 'Get in touch with solis customer support — order help, returns, warranty claims, and partnership inquiries.',
            ],
            [
                'page_slug' => 'about',
                'title' => 'About Us — solis',
                'meta_description' => 'Learn about solis — our story, our team, and how to reach our flagship store.',
            ],
            [
                'page_slug' => 'categories',
                'title' => 'Categories — solis',
                'meta_description' => 'Browse the solis catalogue by category — smartphones, laptops, cameras, audio, wearables, and gaming.',
            ],
            [
                'page_slug' => 'brands',
                'title' => 'Brands — solis',
                'meta_description' => 'Shop the solis catalogue by brand.',
            ],
            [
                'page_slug' => 'wishlist',
                'title' => 'Wishlist — solis',
                'meta_description' => 'Products you have saved for later at solis.',
            ],
            [
                'page_slug' => 'help',
                'title' => 'Help Centre — solis',
                'meta_description' => 'Answers to common questions about orders, shipping, returns, and warranty at solis.',
            ],
            [
                'page_slug' => 'privacy-policy',
                'title' => 'Privacy Policy — solis',
                'meta_description' => 'How solis collects, uses, and protects your personal information.',
            ],
            [
                'page_slug' => 'checkout',
                'title' => 'Checkout — solis',
                'meta_description' => 'Complete your order at solis.',
            ],
        ];

        foreach ($pages as $page) {
            PageMeta::updateOrCreate(['page_slug' => $page['page_slug']], $page);
        }
    }
}
