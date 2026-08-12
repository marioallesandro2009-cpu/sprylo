<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'Sprylo',
            'site_tagline' => 'Tech & Gadgets Ecommerce HTML Template',
            'site_description' => 'Sprylo is a modern online store for tech and electronics — smartphones, laptops, cameras, audio, and wearables.',
            'logo' => null,
            'favicon' => null,

            'utility_promo_tag' => 'SALE',
            'utility_text' => 'Free shipping on orders over $50 · 30-day returns',
            'nav_cta_prefix' => 'UP TO',
            'nav_cta_highlight' => '60% OFF',
            'nav_cta_suffix' => 'ALL ITEMS',

            'contact_email' => 'support@sprylo.example',
            'contact_phone' => '+1 (800) 555-1234',
            'contact_address' => "214 Market Street\nSan Francisco, CA 94103",
            'contact_hours_weekday' => 'Mon — Fri · 06:00 — 22:00 PT',
            'contact_hours_weekend' => 'Sat — Sun · 08:00 — 18:00 PT',

            'social_twitter' => '#',
            'social_linkedin' => '#',
            'social_instagram' => '#',
            'social_youtube' => '#',

            'footer_about_text' => "Modern tech & gadgets — straight from the makers, shipped fast, supported well. Since 2018, we've moved over 400,000 boxes.",
            'footer_copyright' => '© '.date('Y').' Sprylo · All rights reserved',

            'hero_eyebrow' => '⚡ Audio · Featured',
            'hero_heading' => 'Apple HomePod 2nd Gen Speaker',
            'hero_description' => 'Apple ecosystem with high-quality audio playback while serving as a hub for controlling smart home devices. Spatial audio, room-sensing tech.',
            'hero_cta_label' => 'Shop Now',
            'hero_image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=900&q=80&auto=format&fit=crop',
            'hero_product_slug' => 'apple-homepod-2nd-gen',

            'newsletter_prefix' => 'Get',
            'newsletter_highlight' => '20% Off',
            'newsletter_suffix' => 'your first order — straight to your inbox.',
            'newsletter_description' => "Drop your email and we'll send a one-time discount, plus first-look offers on the gear we just got in. Unsubscribe anytime.",

            'utility_link1_label' => 'Track order',
            'utility_link1_url' => '#',
            'utility_link2_label' => 'Help',
            'utility_link2_url' => '#',
            'utility_link3_label' => 'EN · USD',
            'utility_link3_url' => '#',

            'trust_shipping_title' => 'Free fast shipping',
            'trust_shipping_subtitle' => '2 — 3 business days',
            'trust_returns_title' => '30-day free returns',
            'trust_returns_subtitle' => 'No questions asked',
            'trust_warranty_title' => '2-year warranty',
            'trust_warranty_subtitle' => 'On every order',
            'trust_authentic_title' => 'Authentic product',
            'trust_authentic_subtitle' => '100% genuine, sourced from the manufacturer',

            'contact_trade_heading' => 'Looking for trade or bulk pricing?',
            'contact_trade_description' => 'Companies ordering 10+ units qualify for tiered discounts, NET-30 terms, and a dedicated account manager.',
            'contact_trade_cta_label' => 'Email the trade desk',
            'contact_livechat_label' => 'LIVE CHAT',
            'contact_livechat_status' => 'Open chat — 4 agents online',

            'section_hero_enabled' => '1',
            'section_trending_enabled' => '1',
            'section_discounts_enabled' => '1',
            'section_categories_enabled' => '1',
            'section_just_for_you_enabled' => '1',
            'section_brands_enabled' => '1',
            'section_newsletter_enabled' => '1',
        ];

        Setting::where('key', 'nav_cta_text')->delete();

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
