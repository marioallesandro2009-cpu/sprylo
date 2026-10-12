<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Shopping cart</span></div>
    <h1>{{ cms('cart.head.title', 'Your cart') }}</h1>
    @if($cartItems->isNotEmpty())
      <p>{{ $cartItems->sum('qty') }} items · ready to ship. {{ $totals['shipping'] === 0.0 ? 'Free delivery on this order.' : 'Add '.rupiah(($settings['shipping_free_threshold'] ?? 750000) - $totals['subtotal']).' more for free shipping.' }}</p>
    @else
      <p>{{ cms('cart.head.empty', 'Your cart is currently empty.') }}</p>
    @endif
  </div>
</section>

@if(session('success'))
  <div class="container"><p style="color: var(--emerald); font-weight: 600; margin-top: var(--s4)">{{ session('success') }}</p></div>
@endif
@if(session('error'))
  <div class="container"><p style="color: var(--rose); font-weight: 600; margin-top: var(--s4)">{{ session('error') }}</p></div>
@endif
