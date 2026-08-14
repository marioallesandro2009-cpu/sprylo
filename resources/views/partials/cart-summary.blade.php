<aside class="cart-summary">
  <h3>Order summary</h3>

  @if($totals['promo_code'])
    <form action="{{ route('cart.promo.remove') }}" method="POST" class="promo-input">
      @csrf @method('DELETE')
      <input type="text" value="{{ $totals['promo_code'] }}" disabled />
      <button type="submit">Remove</button>
    </form>
  @else
    <form action="{{ route('cart.promo.apply') }}" method="POST" class="promo-input">
      @csrf
      <input type="text" name="code" placeholder="Promo code" />
      <button type="submit">Apply</button>
    </form>
  @endif

  <div class="cart-line"><span>Subtotal · {{ $cartItems->sum('qty') }} items</span><span
      style="font-family:var(--ff-display); font-weight:600; color:var(--ink)">{{ rupiah($totals['subtotal']) }}</span></div>
  <div class="cart-line"><span>Shipping</span><span
      style="color: var(--emerald); font-weight: 600">{{ $totals['shipping'] == 0.0 ? 'Free' : rupiah($totals['shipping']) }}</span></div>
  <div class="cart-line"><span>Estimated tax</span><span
      style="font-family:var(--ff-display); font-weight:600; color:var(--ink)">{{ rupiah($totals['tax']) }}</span></div>
  @if($totals['promo_discount'] > 0)
    <div class="cart-line"><span>Promo · {{ $totals['promo_code'] }}</span><span
        style="color: var(--rose); font-family:var(--ff-display); font-weight:600">−{{ rupiah($totals['promo_discount']) }}</span></div>
  @endif

  <div class="cart-line is-total"><span>Total</span><span>{{ rupiah($totals['total']) }}</span></div>

  <a href="{{ route('checkout.index') }}" class="btn btn--indigo btn--block">Proceed to checkout →</a>

  <div style="display: flex; justify-content: center; gap: var(--s3); margin-top: var(--s5); flex-wrap: wrap">
    <span style="font-family: var(--ff-mono); font-size: 11px; color: var(--fg-mute); padding: 6px 10px; background: var(--paper); border-radius: 4px">VISA</span>
    <span style="font-family: var(--ff-mono); font-size: 11px; color: var(--fg-mute); padding: 6px 10px; background: var(--paper); border-radius: 4px">MASTERCARD</span>
    <span style="font-family: var(--ff-mono); font-size: 11px; color: var(--fg-mute); padding: 6px 10px; background: var(--paper); border-radius: 4px">AMEX</span>
    <span style="font-family: var(--ff-mono); font-size: 11px; color: var(--fg-mute); padding: 6px 10px; background: var(--paper); border-radius: 4px">PAYPAL</span>
    <span style="font-family: var(--ff-mono); font-size: 11px; color: var(--fg-mute); padding: 6px 10px; background: var(--paper); border-radius: 4px">APPLE PAY</span>
  </div>

  <p style="margin-top: var(--s5); font-size: 11px; font-family: var(--ff-mono); color: var(--fg-mute); text-align: center; line-height: 1.6">
    Encrypted checkout · SSL secured. Your payment information is never stored on our servers.</p>
</aside>
