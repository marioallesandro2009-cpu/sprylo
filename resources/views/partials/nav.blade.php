<nav class="nav-bar" aria-label="Primary">
  <div class="container">
    <a href="{{ route('shop.index') }}" class="all-cats">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      All Categories
    </a>
    <div class="main-nav">
      <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
      <a href="{{ route('shop.index') }}" @if(request()->routeIs('shop.index')) aria-current="page" @endif>Shop ▾</a>
      <a href="{{ route('categories.index') }}" @if(request()->routeIs('categories.index')) aria-current="page" @endif>Categories</a>
      <a href="{{ route('brands.index') }}" @if(request()->routeIs('brands.index')) aria-current="page" @endif>Brands</a>
      <a href="{{ route('product.show', $settings['hero_product_slug'] ?? 'apple-homepod-2nd-gen') }}" @if(request()->routeIs('product.show')) aria-current="page" @endif>Products</a>
      <a href="{{ route('about.index') }}" @if(request()->routeIs('about.index')) aria-current="page" @endif>About</a>
      <a href="{{ route('cart.index') }}" @if(request()->routeIs('cart.index')) aria-current="page" @endif>Cart</a>
      <a href="{{ route('contact.index') }}" @if(request()->routeIs('contact.index')) aria-current="page" @endif>Contact</a>
    </div>
    <span class="nav-cta">{{ $settings['nav_cta_prefix'] ?? 'UP TO' }} <strong>{{ $settings['nav_cta_highlight'] ?? '60% OFF' }}</strong> {{ $settings['nav_cta_suffix'] ?? 'ALL ITEMS' }}</span>
  </div>
</nav>
