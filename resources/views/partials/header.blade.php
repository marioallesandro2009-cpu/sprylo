@php
  $siteName = $settings['site_name'] ?? 'Sprylo';
@endphp
<header class="site-header">
  <div class="container">
    <a href="{{ route('home') }}" class="brand">
      @if(!empty($settings['logo']))
        <img class="brand-mark" src="{{ $settings['logo'] }}" alt="{{ $siteName }}" style="object-fit:cover" />
      @else
        <span class="brand-mark">{{ strtoupper(substr($siteName, 0, 1)) }}</span>
      @endif
      {{ $siteName }}
    </a>

    <form class="search" role="search" action="{{ route('shop.index') }}" method="GET" autocomplete="off">
      <input type="text" name="q" placeholder="Search for products, brands, categories…" aria-label="Search the store" value="{{ request('q') }}" />
      <button type="submit" aria-label="Search">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
      </button>
      <div class="search-suggest" hidden></div>
    </form>

    <div class="icon-row">
      <a href="{{ route('track-order.index') }}" class="icon-btn" aria-label="Track order" title="Track order">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
      </a>
      @auth
        <form action="{{ route('logout') }}" method="POST" style="display:contents" title="Log out as {{ auth()->user()->name }}">
          @csrf
          <button type="submit" class="icon-btn" aria-label="Log out" style="border:none; cursor:pointer; font:inherit">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/></svg>
          </button>
        </form>
      @else
        <a href="{{ route('login') }}" class="icon-btn" aria-label="Log in">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/></svg>
        </a>
      @endauth
      <a href="{{ route('wishlist.index') }}" class="icon-btn" aria-label="Wishlist">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        @if($wishlistCount > 0)
          <span class="count">{{ $wishlistCount }}</span>
        @endif
      </a>
      <a href="{{ route('cart.index') }}" class="icon-btn icon-btn--cart" aria-label="Cart">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2l-2 5v13a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7l-2-5z"/><path d="M4 7h16"/><path d="M16 11a4 4 0 0 1-8 0"/></svg>
        @if($cartCount > 0)
          <span class="count">{{ $cartCount }}</span>
        @endif
      </a>
      <button class="nav-toggle" aria-label="Open menu" aria-expanded="false">≡</button>
    </div>
  </div>
</header>
