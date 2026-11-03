@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Track your order — '.($settings['site_name'] ?? 'solis')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')
  <section class="page-head">
    <div class="container">
      <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Track order</span></div>
      <h1>{{ $settings['track_head_title'] ?? 'Track your order' }}</h1>
      <p>{{ $settings['track_head_text'] ?? 'Enter your order number and email to check its status' }}{{ $order && $order->payment_method === 'bank_transfer' ? ' or upload your transfer proof' : '' }}.</p>
    </div>
  </section>

  <section class="section">
    <div class="container" style="max-width: 640px">

      @isset($success)
        <p style="color: var(--emerald); font-weight: 600; margin-bottom: var(--s5)">{{ $success }}</p>
      @endisset

      <form action="{{ route('track-order.lookup') }}" method="POST" style="background: var(--paper); border: 1px solid var(--rule); border-radius: var(--r); padding: var(--s7); margin-bottom: var(--s6)">
        @csrf
        <div class="field-row">
          <div class="field">
            <label for="to-order-number">Order number</label>
            <input id="to-order-number" name="order_number" type="text" required placeholder="SPR-XXXXXX"
              value="{{ old('order_number', $prefillOrderNumber) }}" />
          </div>
          <div class="field">
            <label for="to-email">Email</label>
            <input id="to-email" name="email" type="email" required value="{{ old('email') }}" />
          </div>
        </div>
        @error('order_number')
          <p style="color: var(--rose); font-size: var(--text-sm); margin-top: var(--s2)">{{ $message }}</p>
        @enderror
        @error('email')
          <p style="color: var(--rose); font-size: var(--text-sm); margin-top: var(--s2)">{{ $message }}</p>
        @enderror
        <button type="submit" class="btn btn--indigo" style="margin-top: var(--s4)">Find my order →</button>
        <p style="color: var(--fg-soft); font-size: var(--text-sm); margin-top: var(--s4)">Have an invoice to verify? <a href="{{ route('invoice-check.index') }}">Check your invoice</a></p>
      </form>

      @if($order)
        <div style="background: var(--paper); border: 1px solid var(--rule); border-radius: var(--r); padding: var(--s7)">
          <div style="display:flex; justify-content:space-between; align-items:center; gap: var(--s3); margin-bottom: var(--s5)">
            <div>
              <div style="font-weight:700; font-size: var(--text-lg)">{{ $order->order_number }}</div>
              <div style="color: var(--fg-soft); font-size: var(--text-sm)">Placed {{ $order->created_at->format('d M Y, H:i') }}</div>
            </div>
            @php
              $statusColor = match($order->status) {
                'cancelled' => 'var(--rose)',
                'completed', 'paid' => 'var(--emerald)',
                default => 'var(--fg-mute)',
              };
            @endphp
            <span style="font-family: var(--ff-mono); font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.06em; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}; border-radius: 999px; padding: 4px 12px; white-space: nowrap">{{ str_replace('_', ' ', $order->status) }}</span>
          </div>

          @include('partials.order-summary-card')

          <form action="{{ route('track-order.reorder', $order) }}" method="POST" style="margin-top: var(--s5)">
            @csrf
            <input type="hidden" name="email" value="{{ $verifiedEmail }}" />
            <button type="submit" class="btn btn--indigo">Buy again →</button>
          </form>

          <div style="margin-top: var(--s6); padding-top: var(--s6); border-top: 1px solid var(--rule)">
            <div class="label" style="font-family: var(--ff-mono); font-size: var(--text-xs); letter-spacing: 0.06em; text-transform: uppercase; color: var(--fg-mute); margin-bottom: var(--s2)">Payment</div>

            @if($order->payment_method === 'qris')
              @if($settings['payment_qris_image'] ?? null)
                <img src="{{ $settings['payment_qris_image'] }}" alt="QRIS payment code" class="payment-qris-image" />
              @endif
              <p class="payment-qris-note">Scan the QRIS code above to complete your payment of {{ rupiah($order->total) }}.</p>
            @elseif($order->payment_proof_path)
              <p style="color: var(--fg-soft); font-size: var(--text-sm)">Proof received — awaiting verification by our team.</p>
            @elseif($order->status === 'awaiting_proof')
              <p style="color: var(--fg-soft); font-size: var(--text-sm); margin-bottom: var(--s4)">
                Please transfer {{ rupiah($order->total) }} to the account below, then upload your proof of payment.
              </p>
              @include('partials.bank-details')

              <form action="{{ route('track-order.payment-proof', $order) }}" method="POST" enctype="multipart/form-data" style="margin-top: var(--s5)">
                @csrf
                <input type="hidden" name="email" value="{{ $verifiedEmail }}" />
                <div class="field">
                  <label for="to-payment-proof">Upload transfer proof</label>
                  <input id="to-payment-proof" name="payment_proof" type="file" accept="image/*" required />
                </div>
                @error('payment_proof')
                  <p style="color: var(--rose); font-size: var(--text-sm); margin-top: var(--s2)">{{ $message }}</p>
                @enderror
                <button type="submit" class="btn btn--indigo" style="margin-top: var(--s4)">Submit proof →</button>
              </form>
            @else
              <p style="color: var(--fg-soft); font-size: var(--text-sm)">Bank transfer — no proof needed at this stage.</p>
            @endif
          </div>
        </div>
      @endif

    </div>
  </section>
@endsection
