@php
  $siteName = $settings['site_name'] ?? 'solis';
@endphp
<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Privacy policy</span></div>
    <h1>Privacy policy</h1>
    <p>Last updated {{ $settings['privacy_last_updated'] ?? now()->format('F Y') }}. This page explains what {{ $siteName }} collects and how it's used.</p>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width: 760px; display: grid; gap: var(--s6)">

    <div>
      <h2 style="font-size: var(--text-lg); margin-bottom: var(--s3)">{{ $settings['privacy_collect_heading'] ?? 'Information we collect' }}</h2>
      <p style="color: var(--fg-soft); line-height: 1.7">{{ $settings['privacy_collect_body'] ?? '' }}</p>
    </div>

    <div>
      <h2 style="font-size: var(--text-lg); margin-bottom: var(--s3)">{{ $settings['privacy_use_heading'] ?? 'How we use it' }}</h2>
      <p style="color: var(--fg-soft); line-height: 1.7">{{ $settings['privacy_use_body'] ?? '' }}</p>
    </div>

    <div>
      <h2 style="font-size: var(--text-lg); margin-bottom: var(--s3)">{{ $settings['privacy_cookies_heading'] ?? 'Cookies' }}</h2>
      <p style="color: var(--fg-soft); line-height: 1.7">{{ $settings['privacy_cookies_body'] ?? '' }}</p>
    </div>

    <div>
      <h2 style="font-size: var(--text-lg); margin-bottom: var(--s3)">{{ $settings['privacy_contact_heading'] ?? 'Contact us' }}</h2>
      <p style="color: var(--fg-soft); line-height: 1.7">{{ $settings['privacy_contact_body'] ?? '' }} Email <a href="mailto:{{ $settings['contact_email'] ?? '' }}" style="color: var(--indigo)">{{ $settings['contact_email'] ?? '' }}</a> or use our <a href="{{ route('contact.index') }}" style="color: var(--indigo)">contact form</a>.</p>
    </div>

  </div>
</section>
