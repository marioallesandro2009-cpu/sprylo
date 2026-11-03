<div style="display: grid; gap: var(--s3); margin-bottom: var(--s5)">
  @foreach($order->items as $item)
    <div style="display:flex; justify-content:space-between; gap:var(--s3); font-size: var(--text-sm)">
      <span style="color: var(--fg-soft)">{{ $item->product_name }}@if($item->variant_label) ({{ $item->variant_label }})@endif × {{ $item->qty }}</span>
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
@if($order->has_insurance)
  <div class="cart-line"><span>Shipping insurance</span><span>{{ rupiah($order->insurance_fee) }}</span></div>
@endif
@if($order->promo_discount > 0)
  <div class="cart-line"><span>Promo · {{ $order->promo_code }}</span><span>−{{ rupiah($order->promo_discount) }}</span></div>
@endif
<div class="cart-line is-total"><span>Total</span><span>{{ rupiah($order->total) }}</span></div>
