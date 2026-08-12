<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    private const TEXT_KEYS = [
        'site_name', 'site_tagline', 'site_description', 'logo', 'favicon',
        'utility_promo_tag', 'utility_text',
        'nav_cta_prefix', 'nav_cta_highlight', 'nav_cta_suffix',
        'contact_email', 'contact_phone', 'contact_address',
        'contact_hours_weekday', 'contact_hours_weekend',
        'social_twitter', 'social_linkedin', 'social_instagram', 'social_youtube',
        'footer_about_text', 'footer_copyright',
        'hero_eyebrow', 'hero_heading', 'hero_description', 'hero_cta_label',
        'hero_image', 'hero_product_slug',
        'newsletter_prefix', 'newsletter_highlight', 'newsletter_suffix', 'newsletter_description',
        'utility_link1_label', 'utility_link1_url',
        'utility_link2_label', 'utility_link2_url',
        'utility_link3_label', 'utility_link3_url',
        'trust_shipping_title', 'trust_shipping_subtitle',
        'trust_returns_title', 'trust_returns_subtitle',
        'trust_warranty_title', 'trust_warranty_subtitle',
        'trust_authentic_title', 'trust_authentic_subtitle',
        'contact_trade_heading', 'contact_trade_description', 'contact_trade_cta_label',
        'contact_livechat_label', 'contact_livechat_status',
    ];

    private const SECTION_KEYS = [
        'section_hero_enabled', 'section_trending_enabled', 'section_discounts_enabled',
        'section_categories_enabled', 'section_just_for_you_enabled',
        'section_brands_enabled', 'section_newsletter_enabled',
    ];

    public function index(): View
    {
        $settings = Setting::pluck('value', 'key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(
            collect(self::TEXT_KEYS)->mapWithKeys(fn ($key) => [$key => ['nullable', 'string']])->all()
        );

        foreach (self::TEXT_KEYS as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $data[$key] ?? null]);
        }

        foreach (self::SECTION_KEYS as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $request->boolean($key) ? '1' : '0']);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated.');
    }
}
