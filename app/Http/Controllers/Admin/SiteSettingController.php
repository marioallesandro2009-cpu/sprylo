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
        'hero_product_slug',
        'newsletter_prefix', 'newsletter_highlight', 'newsletter_suffix', 'newsletter_description',
        'utility_link1_label', 'utility_link1_url',
        'utility_link2_label', 'utility_link2_url',
        'utility_link3_label', 'utility_link3_url',
        'trust_shipping_title', 'trust_shipping_subtitle',
        'trust_returns_title', 'trust_returns_subtitle',
        'trust_warranty_title', 'trust_warranty_subtitle',
        'trust_authentic_title', 'trust_authentic_subtitle',
        'contact_trade_heading', 'contact_trade_description', 'contact_trade_cta_label',
        'contact_livechat_label', 'contact_livechat_status', 'contact_reply_sla_text',
        'about_heading', 'about_body', 'about_image', 'history_heading', 'history_body',
        'privacy_collect_heading', 'privacy_collect_body',
        'privacy_use_heading', 'privacy_use_body',
        'privacy_cookies_heading', 'privacy_cookies_body',
        'privacy_contact_heading', 'privacy_contact_body',
        'footer_credit_text',
        'payment_bank_name', 'payment_bank_account_number', 'payment_bank_account_holder',
        'payment_qris_image',
        'shop_head_title', 'shop_head_text',
        'contact_head_title', 'contact_head_text',
        'checkout_head_title', 'checkout_head_text',
        'help_head_title', 'help_head_text',
        'cart_empty_text', 'wishlist_empty_text',
        'newsletter_placeholder', 'newsletter_button_label', 'newsletter_success_text',
    ];

    private const NUMERIC_KEYS = [
        'shipping_free_threshold', 'shipping_fee', 'insurance_fee',
    ];

    private const SECTION_KEYS = [
        'section_hero_enabled', 'section_trending_enabled', 'section_discounts_enabled',
        'section_just_for_you_enabled', 'section_newsletter_enabled',
    ];

    public function index(): View
    {
        $settings = Setting::pluck('value', 'key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = collect(self::TEXT_KEYS)->mapWithKeys(fn ($key) => [$key => ['nullable', 'string']])
            ->merge(collect(self::NUMERIC_KEYS)->mapWithKeys(fn ($key) => [$key => ['nullable', 'numeric', 'min:0']]))
            ->all();

        $data = $request->validate($rules);

        foreach ([...self::TEXT_KEYS, ...self::NUMERIC_KEYS] as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $data[$key] ?? null]);
        }

        foreach (self::SECTION_KEYS as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $request->boolean($key) ? '1' : '0']);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated.');
    }
}
