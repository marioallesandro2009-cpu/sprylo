<section class="page-head">
  <div class="container" style="text-align:center">
    <div style="width:56px; height:56px; margin: 0 auto var(--s4); background: var(--indigo-soft); color: var(--indigo); border-radius: 999px; display:grid; place-items:center; font-size: 24px">✓</div>
    <h1 style="margin-left:auto; margin-right:auto">Thanks — your order is in!</h1>
    <p style="margin-left:auto; margin-right:auto">Order <strong>{{ $order->order_number }}</strong> is confirmed. A copy of this summary was recorded for our team; we'll reach out at {{ $order->email }} with shipping updates.</p>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width: 640px">
    <div style="background: var(--paper); border: 1px solid var(--rule); border-radius: var(--r); padding: var(--s7)">

      <div style="display: grid; gap: var(--s3); margin-bottom: var(--s5)">
        @foreach($order->items as $item)
          <div style="display:flex; justify-content:space-between; gap:var(--s3); font-size: var(--text-sm)">
            <span style="color: var(--fg-soft)">{{ $item->product_name }} × {{ $item->qty }}</span>
            <span style="font-weight:600; white-space:nowrap">{{ rupiah($item->subtotal) }}</span>
          </div>
        @endforeach
      </div>

      <div class="cart-line"><span>Subtotal</span><span>{{ rupiah($order->subtotal) }}</span></div>
      <div class="cart-line">
        <span>Shipping @if($order->courier_name)({{ $order->courier_name }} — {{ $order->courier_service_name }})@endif</span>
        <span>{{ $order->shipping == 0.0 ? 'Free' : rupiah($order->shipping) }}</span>
      </div>
      <div class="cart-line"><span>Tax</span><span>{{ rupiah($order->tax) }}</span></div>
      @if($order->promo_discount > 0)
        <div class="cart-line"><span>Promo · {{ $order->promo_code }}</span><span>−{{ rupiah($order->promo_discount) }}</span></div>
      @endif
      <div class="cart-line is-total"><span>Total</span><span>{{ rupiah($order->total) }}</span></div>

      <div style="margin-top: var(--s6); padding-top: var(--s6); border-top: 1px solid var(--rule)">
        <div class="label" style="font-family: var(--ff-mono); font-size: var(--text-xs); letter-spacing: 0.06em; text-transform: uppercase; color: var(--fg-mute); margin-bottom: var(--s2)">Shipping to</div>
        <div style="font-weight:600">{{ $order->name }}</div>
        <div style="color: var(--fg-soft); font-size: var(--text-sm); white-space: pre-line">{{ $order->address }}
{{ $order->city }}, {{ $order->postal_code }}</div>
        <div style="color: var(--fg-soft); font-size: var(--text-sm); margin-top: var(--s2)">{{ $order->phone }} · {{ $order->email }}</div>
      </div>

      <div style="margin-top: var(--s6); padding-top: var(--s6); border-top: 1px solid var(--rule)">
        <div class="label" style="font-family: var(--ff-mono); font-size: var(--text-xs); letter-spacing: 0.06em; text-transform: uppercase; color: var(--fg-mute); margin-bottom: var(--s2)">Payment</div>
        @if($order->payment_method === 'qris')
          @if($settings['payment_qris_image'] ?? null)
            <img src="{{ $settings['payment_qris_image'] }}" alt="QRIS payment code" class="payment-qris-image" />
          @endif
          <p class="payment-qris-note">Scan the QRIS code above to complete your payment of {{ rupiah($order->total) }}.</p>
        @else
          <p style="color: var(--fg-soft); font-size: var(--text-sm)">Transfer proof received — we'll verify it and confirm your order shortly.</p>
        @endif
      </div>

    </div>

    <div style="text-align:center; margin-top: var(--s6)">
      <a href="{{ route('shop.index') }}" class="btn btn--indigo">Continue shopping →</a>
    </div>
  </div>
</section>
