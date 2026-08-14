<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Wishlist</span></div>
    <h1>Your wishlist</h1>
    <p>{{ $products->isNotEmpty() ? $products->count().' item(s) saved for later.' : 'Save products you like to find them here.' }}</p>
  </div>
</section>

@if(session('success'))
  <div class="container"><p style="color: var(--emerald); font-weight: 600; margin-top: var(--s4)">{{ session('success') }}</p></div>
@endif
