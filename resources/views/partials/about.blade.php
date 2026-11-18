<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>About us</span></div>
    <h1>{{ $settings['about_heading'] ?? 'About '.($settings['site_name'] ?? 'solis') }}</h1>
    <p>{{ $settings['about_body'] ?? '' }}</p>
  </div>
</section>

@if(!empty($settings['about_image']))
  <section class="section" style="padding-top: 0">
    <div class="container">
      <img src="{{ $settings['about_image'] }}" alt="{{ $settings['site_name'] ?? 'solis' }}" style="width: 100%; max-height: 420px; object-fit: cover; border-radius: var(--r-lg)" />
    </div>
  </section>
@endif
