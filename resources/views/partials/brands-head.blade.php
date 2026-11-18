<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">{{ $ui('ui_crumb_home') }}</a> <span class="sep">›</span> <span>{{ $ui('ui_brands_crumb') }}</span></div>
    <h1>{{ $settings['brands_head_title'] ?? 'Shop by brand' }}</h1>
    <p>{{ $allBrands->count() }} {{ $settings['brands_head_text'] ?? 'brands we carry — pick one to jump straight into filtered results.' }}</p>
  </div>
</section>