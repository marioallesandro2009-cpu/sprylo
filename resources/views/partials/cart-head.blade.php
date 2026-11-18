<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">{{ $ui('ui_crumb_home') }}</a> <span class="sep">›</span> <span>{{ $ui('ui_cart_crumb') }}</span></div>
    @include('partials.checkout-steps', ['current' => 1])
    <h1>{{ cms('cart.head.title', 'Your cart') }}</h1>
    @if($cartItems->isNotEmpty())
      <p>{{ $ui('ui_cart_intro', ['count' => $cartItems->sum('qty'), 'shipping_note' => $totals['shipping'] === 0.0 ? $ui('ui_cart_free_shipping') : $ui('ui_cart_more_for_free', ['amount' => rupiah(($settings['shipping_free_threshold'] ?? 0) - $totals['subtotal'])])]) }}</p>
    @else
      <p>{{ cms('cart.head.empty', 'Your cart is currently empty.') }}</p>
    @endif
  </div>
</section>
