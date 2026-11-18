<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class LabelSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'categories_head_title' => 'Shop by category',
            'categories_head_text' => 'categories to browse — pick one to jump straight into filtered results.',
            'brands_head_title' => 'Shop by brand',
            'brands_head_text' => 'brands we carry — pick one to jump straight into filtered results.',
            'wishlist_head_title' => 'Your wishlist',
            'wishlist_head_empty_text' => 'Save products you like to find them here.',
            'cart_head_title' => 'Your cart',
            'track_head_title' => 'Track your order',
            'track_head_text' => 'Enter your order number and email to check its status',
            'home_trending_title' => 'Trending Products',
            'home_trending_link_label' => 'View all',
            'home_trending_all_label' => 'All',
            'home_just_for_you_title' => 'Just for you',
            'home_just_for_you_link_label' => 'More picks',
            'footer_categories_title' => 'Popular Categories',
            'contact_label_email' => 'EMAIL US',
            'contact_label_phone' => 'CALL US · 24/7',
            'contact_label_address' => 'VISIT OUR FLAGSHIP',
            'contact_label_hours' => 'SUPPORT HOURS',
            'contact_form_note' => 'No bots, no templated replies — a real person on our team will pick this up.',
            'privacy_last_updated' => now()->format('F Y'),
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}