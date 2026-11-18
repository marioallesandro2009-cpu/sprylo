<?php

namespace Database\Seeders;

use App\Models\PageMeta;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class PageHeadingSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'shop_head_title' => 'All Products',
            'shop_head_text' => 'Use the filters on the left to narrow down by brand, price, rating, or availability.',
            'contact_head_title' => 'Talk to us — we ship gear, but we answer messages too.',
            'contact_head_text' => 'Three ways to reach our team: live chat (fastest), email, or this contact form for anything that needs an attachment. We typically reply within 4 working hours and cover support in EN, ES, FR, and DE.',
            'checkout_head_title' => 'Checkout',
            'checkout_head_text' => 'Enter your shipping details to place your order. This is a demo checkout — no payment is collected, and your order is saved so our team can follow up.',
            'help_head_title' => 'Help centre',
            'help_head_text' => "Answers to the questions we get asked most. Can't find what you need?",
            'cart_empty_text' => "Looks like you haven't added anything to your cart yet.",
            'wishlist_empty_text' => 'Nothing here yet — tap the heart on any product to save it.',
            'newsletter_placeholder' => 'Enter your email',
            'newsletter_button_label' => 'Subscribe →',
            'newsletter_success_text' => 'Thanks for subscribing!',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        PageMeta::firstOrCreate(
            ['page_slug' => 'track-order'],
            ['title' => 'Track your order — solis', 'meta_description' => 'Check the status of your solis order.']
        );
    }
}