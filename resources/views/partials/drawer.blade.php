@php
  $siteName = $settings['site_name'] ?? 'Sprylo';
@endphp
<div class="drawer" id="drawer" aria-hidden="true">
  <div class="drawer-head">
    <a href="{{ route('home') }}" class="brand">
      @if(!empty($settings['logo']))
        <img class="brand-mark" src="{{ $settings['logo'] }}" alt="{{ $siteName }}" style="object-fit:cover" />
      @else
        <span class="brand-mark">{{ strtoupper(substr($siteName, 0, 1)) }}</span>
      @endif
      {{ $siteName }}
    </a>
    <button class="drawer-close" aria-label="Close menu">Close ✕</button>
  </div>
  <a href="{{ route('home') }}">Home</a>
  <a href="{{ route('shop.index') }}">Shop</a>
  <a href="{{ route('categories.index') }}">Categories</a>
  <a href="{{ route('brands.index') }}">Brands</a>
  <a href="{{ route('shop.index') }}">Products</a>
  <a href="{{ route('about.index') }}">About</a>
  <a href="{{ route('wishlist.index') }}">Wishlist</a>
  <a href="{{ route('cart.index') }}">Cart</a>
  <a href="{{ route('contact.index') }}">Contact</a>
  <a href="{{ route('cart.index') }}" class="btn btn--indigo" style="margin-top:var(--s5); justify-content:center">View cart →</a>
</div>
