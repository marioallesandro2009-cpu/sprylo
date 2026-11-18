<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">{{ $ui('ui_crumb_home') }}</a> <span class="sep">›</span> <span>{{ $ui('ui_wishlist_crumb') }}</span></div>
    <h1>{{ $settings['wishlist_head_title'] ?? 'Your wishlist' }}</h1>
    <p>{{ $products->isNotEmpty() ? $products->count().' item(s) saved for later.' : ($settings['wishlist_head_empty_text'] ?? 'Save products you like to find them here.') }}</p>
  </div>
</section>

