@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Check your invoice — '.($settings['site_name'] ?? 'solis')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')
  <section class="page-head">
    <div class="container">
      <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Check invoice</span></div>
      <h1>{{ $settings['invoice_check_head_title'] ?? 'Check your invoice' }}</h1>
      <p>{{ $settings['invoice_check_head_text'] ?? 'Enter your invoice number and email to compare your invoice with our records.' }}</p>
    </div>
  </section>

  <section class="section">
    <div class="container" style="max-width: 640px">

      <form action="{{ route('invoice-check.lookup') }}" method="POST" style="background: var(--paper); border: 1px solid var(--rule); border-radius: var(--r); padding: var(--s7); margin-bottom: var(--s6)">
        @csrf
        <div class="field-row">
          <div class="field">
            <label for="ic-invoice-number">Invoice number</label>
            <input id="ic-invoice-number" name="invoice_number" type="text" required maxlength="30" placeholder="INV-YYMM-0001"
              value="{{ old('invoice_number', $invoice['order']->invoice_number ?? '') }}" />
          </div>
          <div class="field">
            <label for="ic-email">Email</label>
            <input id="ic-email" name="email" type="email" required value="{{ old('email') }}" />
          </div>
        </div>
        @error('invoice_number')
          <p style="color: var(--rose); font-size: var(--text-sm); margin-top: var(--s2)">{{ $message }}</p>
        @enderror
        @error('email')
          <p style="color: var(--rose); font-size: var(--text-sm); margin-top: var(--s2)">{{ $message }}</p>
        @enderror
        <button type="submit" class="btn btn--indigo" style="margin-top: var(--s4)">Check invoice →</button>
      </form>

      @if($invoice)
        @php
          $order = $invoice['order'];
          $statusColor = $order->status === 'cancelled' ? 'var(--rose)' : ($invoice['paid'] ? 'var(--emerald)' : 'var(--fg-mute)');
        @endphp
        <div style="background: var(--paper); border: 1px solid var(--rule); border-radius: var(--r); padding: var(--s7)">
          <div style="display:flex; justify-content:space-between; align-items:center; gap: var(--s3); margin-bottom: var(--s5)">
            <div>
              <div style="font-weight:700; font-size: var(--text-lg)">{{ $order->invoice_number }}</div>
              <div style="color: var(--fg-soft); font-size: var(--text-sm)">Issued {{ $invoice['issuedAt']->format('d M Y') }} · Order <span style="white-space:nowrap">{{ $order->order_number }}</span></div>
              <div style="color: var(--fg-soft); font-size: var(--text-sm)">Payment: {{ $invoice['paymentLabel'] }}</div>
            </div>
            <span style="font-family: var(--ff-mono); font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.06em; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}; border-radius: 999px; padding: 4px 12px; text-align: center">{{ $invoice['statusLabel'] }}</span>
          </div>

          @include('partials.order-totals')

          @if($order->status === 'cancelled')
            <p style="color: var(--rose); font-size: var(--text-sm); margin-top: var(--s5)">This order was cancelled, so this invoice is no longer valid.</p>
          @else
            {{-- Same signed link as the order summary: shown only after the number and email have matched. --}}
            <div style="margin-top: var(--s5)">
              <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('invoices.show', now()->addDays(7), ['order' => $order]) }}" target="_blank" rel="noopener" class="btn btn--ghost">⬇ Invoice (PDF)</a>
            </div>
          @endif
        </div>
      @endif

    </div>
  </section>
@endsection
