@extends('layouts.admin')

@section('title', 'Site Settings')
@section('page-title', 'Site Settings')

@section('content')

  <form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf @method('PUT')

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">General</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Site name</label>
          <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings['site_name'] ?? '') }}" />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Tagline</label>
          <input type="text" name="site_tagline" class="form-control" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}" />
        </div>
        <div class="col-12">
          <label class="form-label small fw-semibold">Default meta description</label>
          <textarea name="site_description" rows="2" class="form-control">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Logo URL</label>
          <input type="text" name="logo" class="form-control" value="{{ old('logo', $settings['logo'] ?? '') }}" />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Favicon URL</label>
          <input type="text" name="favicon" class="form-control" value="{{ old('favicon', $settings['favicon'] ?? '') }}" />
        </div>
      </div>
    </div>

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">Utility Strip &amp; Nav Banner</h6>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Promo tag (e.g. SALE)</label>
          <input type="text" name="utility_promo_tag" class="form-control" value="{{ old('utility_promo_tag', $settings['utility_promo_tag'] ?? '') }}" />
        </div>
        <div class="col-md-8">
          <label class="form-label small fw-semibold">Utility strip text</label>
          <input type="text" name="utility_text" class="form-control" value="{{ old('utility_text', $settings['utility_text'] ?? '') }}" />
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Nav CTA prefix</label>
          <input type="text" name="nav_cta_prefix" class="form-control" value="{{ old('nav_cta_prefix', $settings['nav_cta_prefix'] ?? '') }}" />
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Nav CTA highlight</label>
          <input type="text" name="nav_cta_highlight" class="form-control" value="{{ old('nav_cta_highlight', $settings['nav_cta_highlight'] ?? '') }}" />
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Nav CTA suffix</label>
          <input type="text" name="nav_cta_suffix" class="form-control" value="{{ old('nav_cta_suffix', $settings['nav_cta_suffix'] ?? '') }}" />
        </div>
      </div>
      <hr class="my-3" />
      <div class="row g-3">
        @foreach([1, 2, 3] as $n)
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Utility link {{ $n }} label</label>
            <input type="text" name="utility_link{{ $n }}_label" class="form-control" value="{{ old("utility_link{$n}_label", $settings["utility_link{$n}_label"] ?? '') }}" />
          </div>
          <div class="col-md-8">
            <label class="form-label small fw-semibold">Utility link {{ $n }} URL</label>
            <input type="text" name="utility_link{{ $n }}_url" class="form-control" value="{{ old("utility_link{$n}_url", $settings["utility_link{$n}_url"] ?? '') }}" />
          </div>
        @endforeach
      </div>
    </div>

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">Contact Info</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Email</label>
          <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Phone</label>
          <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" />
        </div>
        <div class="col-12">
          <label class="form-label small fw-semibold">Address</label>
          <textarea name="contact_address" rows="2" class="form-control">{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Hours (weekday)</label>
          <input type="text" name="contact_hours_weekday" class="form-control" value="{{ old('contact_hours_weekday', $settings['contact_hours_weekday'] ?? '') }}" />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Hours (weekend)</label>
          <input type="text" name="contact_hours_weekend" class="form-control" value="{{ old('contact_hours_weekend', $settings['contact_hours_weekend'] ?? '') }}" />
        </div>
      </div>
    </div>

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">Social Links</h6>
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label small fw-semibold">Twitter</label>
          <input type="text" name="social_twitter" class="form-control" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}" />
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-semibold">LinkedIn</label>
          <input type="text" name="social_linkedin" class="form-control" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}" />
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-semibold">Instagram</label>
          <input type="text" name="social_instagram" class="form-control" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" />
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-semibold">YouTube</label>
          <input type="text" name="social_youtube" class="form-control" value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}" />
        </div>
      </div>
    </div>

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">Navigation</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Featured product slug</label>
          <input type="text" name="hero_product_slug" class="form-control" value="{{ old('hero_product_slug', $settings['hero_product_slug'] ?? '') }}" />
          <div class="form-text">Used by the "Products" nav link. The homepage hero banner cards themselves are managed under <a href="{{ route('admin.hero-slides.index') }}">Hero Slides</a>.</div>
        </div>
      </div>
    </div>

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">Newsletter Section</h6>
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label small fw-semibold">Prefix</label>
          <input type="text" name="newsletter_prefix" class="form-control" value="{{ old('newsletter_prefix', $settings['newsletter_prefix'] ?? '') }}" />
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Highlight (bold)</label>
          <input type="text" name="newsletter_highlight" class="form-control" value="{{ old('newsletter_highlight', $settings['newsletter_highlight'] ?? '') }}" />
        </div>
        <div class="col-md-5">
          <label class="form-label small fw-semibold">Suffix</label>
          <input type="text" name="newsletter_suffix" class="form-control" value="{{ old('newsletter_suffix', $settings['newsletter_suffix'] ?? '') }}" />
        </div>
        <div class="col-12">
          <label class="form-label small fw-semibold">Description</label>
          <textarea name="newsletter_description" rows="2" class="form-control">{{ old('newsletter_description', $settings['newsletter_description'] ?? '') }}</textarea>
        </div>
      </div>
    </div>

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">Trust Badges <span class="fw-normal text-muted small">(shown on cart &amp; product pages)</span></h6>
      <div class="row g-3">
        @foreach([
          'trust_shipping' => 'Shipping',
          'trust_returns' => 'Returns',
          'trust_warranty' => 'Warranty',
          'trust_authentic' => 'Authenticity',
        ] as $prefix => $label)
          <div class="col-md-3">
            <label class="form-label small fw-semibold">{{ $label }} title</label>
            <input type="text" name="{{ $prefix }}_title" class="form-control" value="{{ old("{$prefix}_title", $settings["{$prefix}_title"] ?? '') }}" />
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-semibold">{{ $label }} subtitle</label>
            <input type="text" name="{{ $prefix }}_subtitle" class="form-control" value="{{ old("{$prefix}_subtitle", $settings["{$prefix}_subtitle"] ?? '') }}" />
          </div>
        @endforeach
      </div>
    </div>

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">Shipping</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Free shipping threshold (Rp)</label>
          <input type="number" step="1" min="0" name="shipping_free_threshold" class="form-control" value="{{ old('shipping_free_threshold', $settings['shipping_free_threshold'] ?? '') }}" />
          <div class="form-text">Orders at or above this subtotal ship free. Drives both the actual shipping cost and the "add Rp X more" message in the cart.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Shipping fee below threshold (Rp)</label>
          <input type="number" step="1" min="0" name="shipping_fee" class="form-control" value="{{ old('shipping_fee', $settings['shipping_fee'] ?? '') }}" />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Shipping insurance fee (Rp)</label>
          <input type="number" step="1" min="0" name="insurance_fee" class="form-control" value="{{ old('insurance_fee', $settings['insurance_fee'] ?? '') }}" />
          <div class="form-text">Shown as an optional add-on at checkout — the shopper picks it there, not when choosing a product option.</div>
        </div>
      </div>
    </div>

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">Contact Page Blocks</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Live chat label</label>
          <input type="text" name="contact_livechat_label" class="form-control" value="{{ old('contact_livechat_label', $settings['contact_livechat_label'] ?? '') }}" />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Live chat status text</label>
          <input type="text" name="contact_livechat_status" class="form-control" value="{{ old('contact_livechat_status', $settings['contact_livechat_status'] ?? '') }}" />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Trade desk heading</label>
          <input type="text" name="contact_trade_heading" class="form-control" value="{{ old('contact_trade_heading', $settings['contact_trade_heading'] ?? '') }}" />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Trade desk CTA label</label>
          <input type="text" name="contact_trade_cta_label" class="form-control" value="{{ old('contact_trade_cta_label', $settings['contact_trade_cta_label'] ?? '') }}" />
        </div>
        <div class="col-12">
          <label class="form-label small fw-semibold">Trade desk description</label>
          <textarea name="contact_trade_description" rows="2" class="form-control">{{ old('contact_trade_description', $settings['contact_trade_description'] ?? '') }}</textarea>
        </div>
        <div class="col-12">
          <label class="form-label small fw-semibold">Reply confirmation message</label>
          <input type="text" name="contact_reply_sla_text" class="form-control" value="{{ old('contact_reply_sla_text', $settings['contact_reply_sla_text'] ?? '') }}" />
          <div class="form-text">Shown after a visitor submits the contact form.</div>
        </div>
      </div>
    </div>

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">Privacy Policy Page</h6>
      <div class="row g-3">
        @foreach([
          'privacy_collect' => 'Information we collect',
          'privacy_use' => 'How we use it',
          'privacy_cookies' => 'Cookies',
          'privacy_contact' => 'Contact us',
        ] as $prefix => $label)
          <div class="col-md-4">
            <label class="form-label small fw-semibold">{{ $label }} — heading</label>
            <input type="text" name="{{ $prefix }}_heading" class="form-control" value="{{ old("{$prefix}_heading", $settings["{$prefix}_heading"] ?? '') }}" />
          </div>
          <div class="col-md-8">
            <label class="form-label small fw-semibold">{{ $label }} — body</label>
            <textarea name="{{ $prefix }}_body" rows="2" class="form-control">{{ old("{$prefix}_body", $settings["{$prefix}_body"] ?? '') }}</textarea>
          </div>
        @endforeach
      </div>
    </div>

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">About Page</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-semibold">About heading</label>
          <input type="text" name="about_heading" class="form-control" value="{{ old('about_heading', $settings['about_heading'] ?? '') }}" />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">About image URL</label>
          <input type="text" name="about_image" class="form-control" value="{{ old('about_image', $settings['about_image'] ?? '') }}" />
        </div>
        <div class="col-12">
          <label class="form-label small fw-semibold">About body</label>
          <textarea name="about_body" rows="3" class="form-control">{{ old('about_body', $settings['about_body'] ?? '') }}</textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">History heading</label>
          <input type="text" name="history_heading" class="form-control" value="{{ old('history_heading', $settings['history_heading'] ?? '') }}" />
        </div>
        <div class="col-12">
          <label class="form-label small fw-semibold">History body</label>
          <textarea name="history_body" rows="3" class="form-control">{{ old('history_body', $settings['history_body'] ?? '') }}</textarea>
        </div>
        <div class="form-text">The Hours block on the About page reuses the "Hours (weekday/weekend)" fields from Contact Info above.</div>
      </div>
    </div>

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">Homepage Sections</h6>
      <div class="row g-2">
        @foreach([
          'section_hero_enabled' => 'Hero banner',
          'section_trending_enabled' => 'Trending products',
          'section_discounts_enabled' => 'Discount banners',
          'section_just_for_you_enabled' => 'Just for you',
          'section_newsletter_enabled' => 'Newsletter signup (footer)',
        ] as $key => $label)
          <div class="col-md-4">
            <div class="form-check form-switch">
              <input type="checkbox" name="{{ $key }}" value="1" class="form-check-input" id="{{ $key }}"
                     @checked(old($key, ($settings[$key] ?? '1')) != '0') />
              <label class="form-check-label small" for="{{ $key }}">{{ $label }}</label>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">Footer</h6>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label small fw-semibold">About text</label>
          <textarea name="footer_about_text" rows="2" class="form-control">{{ old('footer_about_text', $settings['footer_about_text'] ?? '') }}</textarea>
        </div>
        <div class="col-12">
          <label class="form-label small fw-semibold">Copyright text</label>
          <input type="text" name="footer_copyright" class="form-control" value="{{ old('footer_copyright', $settings['footer_copyright'] ?? '') }}" />
        </div>
        <div class="col-12">
          <label class="form-label small fw-semibold">Credit line</label>
          <input type="text" name="footer_credit_text" class="form-control" value="{{ old('footer_credit_text', $settings['footer_credit_text'] ?? '') }}" />
          <div class="form-text">Small text next to the copyright line. Leave blank to hide it.</div>
        </div>
      </div>
    </div>

    <div class="card-admin mb-3">
      <h6 class="fw-bold mb-3">Payment</h6>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Bank name</label>
          <input type="text" name="payment_bank_name" class="form-control" placeholder="BCA"
                 value="{{ old('payment_bank_name', $settings['payment_bank_name'] ?? '') }}" />
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Account number</label>
          <input type="text" name="payment_bank_account_number" class="form-control" placeholder="1234567890"
                 value="{{ old('payment_bank_account_number', $settings['payment_bank_account_number'] ?? '') }}" />
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Account holder</label>
          <input type="text" name="payment_bank_account_holder" class="form-control" placeholder="PT Sprylo Indonesia"
                 value="{{ old('payment_bank_account_holder', $settings['payment_bank_account_holder'] ?? '') }}" />
        </div>
        <div class="col-12">
          <label class="form-label small fw-semibold">QRIS image URL</label>
          <input type="text" name="payment_qris_image" class="form-control" placeholder="/storage/payment/qris.png"
                 value="{{ old('payment_qris_image', $settings['payment_qris_image'] ?? '') }}" />
          <div class="form-text">Static QRIS image shown at checkout. Upload the image file yourself and paste its URL here — no dynamic/per-transaction QRIS is generated.</div>
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-admin-primary">Save Settings</button>
  </form>

@endsection
