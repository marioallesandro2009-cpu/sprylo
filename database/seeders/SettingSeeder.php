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
            'utility_text' => 'Free shipping on orders over Rp 750.000 · 30-day returns',
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

            'hero_product_slug' => 'apple-homepod-2nd-gen',

            'newsletter_prefix' => 'Get',
            'newsletter_highlight' => '20% Off',
            'newsletter_suffix' => 'your first order — straight to your inbox.',
            'newsletter_description' => "Drop your email and we'll send a one-time discount, plus first-look offers on the gear we just got in. Unsubscribe anytime.",

            'utility_link1_label' => 'Track order',
            'utility_link1_url' => '#',
            'utility_link2_label' => 'Help',
            'utility_link2_url' => '#',
            'utility_link3_label' => 'ID · IDR',
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

            'about_heading' => 'About Sprylo',
            'about_body' => "We're a small team obsessed with getting good tech gear into your hands without the markup or the runaround. Every product we stock, we've tested ourselves.",
            'about_image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1200&q=80&auto=format&fit=crop',
            'history_heading' => 'Our story',
            'history_body' => "Sprylo started in 2018 as a two-person operation shipping headphones out of a garage. Since then we've moved over 400,000 boxes, opened a flagship in San Francisco, and kept the same rule: if we wouldn't buy it ourselves, we don't sell it.",

            'section_hero_enabled' => '1',
            'section_trending_enabled' => '1',
            'section_discounts_enabled' => '1',
            'section_just_for_you_enabled' => '1',
            'section_newsletter_enabled' => '1',

            'shipping_free_threshold' => '750000',
            'shipping_fee' => '25000',

            'contact_reply_sla_text' => 'Message sent — we will reply within 4 hours.',

            'privacy_collect_heading' => 'Information we collect',
            'privacy_collect_body' => "When you place an order, contact support, or sign up for our newsletter, we collect the details you provide directly — name, email, phone, and shipping address. We also collect basic usage data (pages visited, cart contents) to keep the store working correctly.",
            'privacy_use_heading' => 'How we use it',
            'privacy_use_body' => 'Order details are used to fulfil and ship your order and to respond to support requests. We never sell your personal data to third parties.',
            'privacy_cookies_heading' => 'Cookies',
            'privacy_cookies_body' => "We use a session cookie to remember what's in your cart and wishlist while you shop. It's cleared automatically and isn't used for tracking or advertising.",
            'privacy_contact_heading' => 'Contact us',
            'privacy_contact_body' => 'Questions about this policy or your data? Email us or use our contact form.',

            'footer_credit_text' => 'Free HTML template · No framework · No build step.',
        ];

        Setting::where('key', 'nav_cta_text')->delete();

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
