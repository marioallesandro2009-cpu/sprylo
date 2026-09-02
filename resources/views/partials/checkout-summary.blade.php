<aside class="cart-summary">
  <h3>Order summary</h3>

  <div style="display: grid; gap: var(--s3); margin-bottom: var(--s4)">
    @foreach($cartItems as $item)
      <div style="display:flex; justify-content:space-between; gap:var(--s3); font-size: var(--text-sm)">
        <span style="color: var(--fg-soft)">{{ $item['product']->name }} × {{ $item['qty'] }}</span>
        <span style="font-weight:600; white-space:nowrap">{{ rupiah($item['subtotal']) }}</span>
      </div>
    @endforeach
  </div>

  <div class="cart-line"><span>Subtotal</span><span
      style="font-family:var(--ff-display); font-weight:600; color:var(--ink)">{{ rupiah($totals['subtotal']) }}</span></div>
  <div class="cart-line"><span>Shipping</span><span id="summary-shipping" style="font-weight: 600">Select a courier below</span></div>
  <div class="cart-line"><span>Estimated tax</span><span
      style="font-family:var(--ff-display); font-weight:600; color:var(--ink)">{{ rupiah($totals['tax']) }}</span></div>
  @if($totals['promo_discount'] > 0)
    <div class="cart-line"><span>Promo · {{ $totals['promo_code'] }}</span><span
        style="color: var(--rose); font-family:var(--ff-display); font-weight:600">−{{ rupiah($totals['promo_discount']) }}</span></div>
  @endif

  <div class="cart-line is-total"><span>Total</span><span id="summary-total"
      data-base="{{ (float) $totals['subtotal'] + (float) $totals['tax'] - (float) $totals['promo_discount'] }}"
      >{{ rupiah($totals['subtotal'] + $totals['tax'] - $totals['promo_discount']) }}</span></div>

  <p style="margin-top: var(--s5); font-size: 11px; font-family: var(--ff-mono); color: var(--fg-mute); text-align: center; line-height: 1.6">
    Demo checkout · no payment is charged. Your order is recorded for our team to follow up.</p>
</aside>
