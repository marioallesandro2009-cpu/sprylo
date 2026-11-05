<aside class="cart-summary">
  <h3>Order summary</h3>

  @if($totals['promo_code'])
    <form action="{{ route('cart.promo.remove') }}" method="POST" class="promo-input is-applied">
      @csrf @method('DELETE')
      <input type="text" value="{{ $totals['promo_code'] }}" disabled aria-label="Applied promo code" />
      <button type="submit">Remove</button>
    </form>
  @else
    <form action="{{ route('cart.promo.apply') }}" method="POST" class="promo-input">
      @csrf
      <input type="text" name="code" placeholder="Promo code" aria-label="Promo code" />
      <button type="submit">Apply</button>
    </form>
  @endif

  <div class="cart-line"><span>Subtotal · {{ $cartItems->sum('qty') }} {{ $cartItems->sum('qty') === 1 ? 'item' : 'items' }}</span><span class="v">{{ rupiah($totals['subtotal']) }}</span></div>
  <div class="cart-line"><span>Shipping (estimate)</span><span class="v @if($totals['shipping'] == 0.0) is-free @endif">{{ $totals['shipping'] == 0.0 ? 'Free' : rupiah($totals['shipping']) }}</span></div>
  @if($totals['promo_discount'] > 0)
    <div class="cart-line"><span>Promo · {{ $totals['promo_code'] }}</span><span class="v is-discount">−{{ rupiah($totals['promo_discount']) }}</span></div>
  @endif
  <div class="cart-line"><span>PPN {{ \App\Support\Cart::PPN_PERCENT }}%</span><span class="v">{{ rupiah($totals['tax']) }}</span></div>

  <div class="cart-line is-total"><span>Total</span><span>{{ rupiah($totals['total']) }}</span></div>
  <p class="cart-summary__hint">Final shipping is calculated from your delivery address at checkout.</p>

  <a href="{{ route('checkout.index') }}" class="btn btn--indigo btn--block">Proceed to checkout →</a>

  <div class="pay-chips" aria-label="Accepted payment methods">
    <span>Bank transfer</span>
    <span>QRIS</span>
  </div>

  <p class="cart-summary__note">You'll pay by bank transfer or QRIS after placing your order.</p>
</aside>
