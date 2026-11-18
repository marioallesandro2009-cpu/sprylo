<section class="page-head">
  <div class="container" style="text-align:center">
    <div style="width:56px; height:56px; margin: 0 auto var(--s4); background: var(--indigo-soft); color: var(--indigo); border-radius: 999px; display:grid; place-items:center; font-size: 24px">✓</div>
    <h1 style="margin-left:auto; margin-right:auto">Thanks — your order is in!</h1>
    <p style="margin-left:auto; margin-right:auto">Order <strong>{{ $order->order_number }}</strong> is confirmed. A copy of this summary was recorded for our team; we'll reach out at {{ $order->email }} with shipping updates.</p>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width: 640px">
    @foreach($vouchers ?? [] as $voucher)
      <div class="voucher-card">
        <div class="voucher-card__kicker">🎁 A voucher for your next order</div>
        <div class="voucher-card__code">{{ $voucher->code }}</div>
        <div class="voucher-card__meta">
          {{ $voucher->type === 'percent' ? (float) $voucher->value.'% off' : rupiah($voucher->value).' off' }}
          @if($voucher->min_subtotal) · min. order {{ rupiah($voucher->min_subtotal) }}@endif
          · valid until {{ $voucher->expires_at->format('d M Y') }} · one use · also sent to {{ $order->email }}
        </div>
      </div>
    @endforeach
    <div style="background: var(--paper); border: 1px solid var(--rule); border-radius: var(--r); padding: var(--s7)">

      @include('partials.order-summary-card')

      <div style="margin-top: var(--s6); padding-top: var(--s6); border-top: 1px solid var(--rule)">
        <div class="label" style="font-family: var(--ff-mono); font-size: var(--text-xs); letter-spacing: 0.06em; text-transform: uppercase; color: var(--fg-mute); margin-bottom: var(--s2)">Payment</div>
        @if($order->payment_method === 'qris')
          @if($settings['payment_qris_image'] ?? null)
            <img src="{{ $settings['payment_qris_image'] }}" alt="QRIS payment code" class="payment-qris-image" />
          @endif
          <p class="payment-qris-note">Scan the QRIS code above to complete your payment of {{ rupiah($order->total) }}.</p>
        @elseif($order->payment_proof_path)
          <p style="color: var(--fg-soft); font-size: var(--text-sm)">Proof received — awaiting verification by our team.</p>
        @else
          <p style="color: var(--fg-soft); font-size: var(--text-sm); margin-bottom: var(--s4)">
            Please transfer {{ rupiah($order->total) }} to the account below, then upload your proof of payment
            so we can confirm your order.
          </p>
          @include('partials.bank-details')
          @if($canUploadProof && $order->status === 'awaiting_proof')
            <form action="{{ route('checkout.payment-proof', $order) }}" method="POST" enctype="multipart/form-data" style="margin-top: var(--s5)">
              @csrf
              <div class="field">
                <label for="co-payment-proof">Upload transfer proof</label>
                <input id="co-payment-proof" name="payment_proof" type="file" accept="image/*" required />
              </div>
              @error('payment_proof')
                <p style="color: var(--rose); font-size: var(--text-sm); margin-top: var(--s2)">{{ $message }}</p>
              @enderror
              <button type="submit" class="btn btn--indigo" style="margin-top: var(--s4)">Submit proof →</button>
            </form>
          @else
            <a href="{{ route('track-order.index', ['order_number' => $order->order_number]) }}" class="btn btn--indigo" style="margin-top: var(--s4)">Upload transfer proof →</a>
          @endif
        @endif
      </div>

    </div>

    <div style="text-align:center; margin-top: var(--s6)">
      <a href="{{ route('shop.index') }}" class="btn btn--indigo">Continue shopping →</a>
    </div>
  </div>
</section>
