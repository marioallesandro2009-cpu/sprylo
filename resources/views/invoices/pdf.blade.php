<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Invoice {{ $order->invoice_number }}</title>
<style>
  /* dompdf renders CSS 2.1 only: tables for layout, no flex/grid. */
  @page { margin: 34px 38px 40px 38px; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #0F172A; line-height: 1.45; }
  table { border-collapse: collapse; width: 100%; }
  td, th { vertical-align: top; }
  .muted { color: #64748B; }
  .small { font-size: 8.5px; }
  .brand { font-size: 22px; font-weight: bold; color: #4F46E5; letter-spacing: -0.5px; }
  .title { font-size: 24px; font-weight: bold; color: #0F172A; letter-spacing: 1px; text-align: right; }
  .right { text-align: right; }
  .center { text-align: center; }
  .label { font-size: 7.5px; text-transform: uppercase; letter-spacing: 1px; color: #64748B; margin-bottom: 3px; }
  .badge { display: inline-block; padding: 3px 10px; font-size: 8.5px; font-weight: bold; letter-spacing: 1px; }
  .badge-paid { background: #DCFCE7; color: #15803D; }
  .badge-unpaid { background: #FEF3C7; color: #92400E; }
  .rule { border-top: 2px solid #4F46E5; margin: 14px 0 16px; }
  .box { background: #F8FAFC; border: 1px solid #E2E8F0; padding: 9px 11px; }
  .items th { background: #4F46E5; color: #FFFFFF; font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.6px; padding: 7px 8px; text-align: left; }
  .items td { padding: 8px; border-bottom: 1px solid #E2E8F0; }
  .items tr.alt td { background: #F8FAFC; }
  .totals td { padding: 4px 8px; }
  .totals .grand td { border-top: 2px solid #4F46E5; padding-top: 8px; font-size: 13px; font-weight: bold; color: #3730A3; }
  .pay { border: 1px solid #C7D2FE; background: #EEF2FF; padding: 10px 12px; }
  .footer { margin-top: 22px; border-top: 1px solid #E2E8F0; padding-top: 8px; text-align: center; color: #64748B; font-size: 8px; }
</style>
</head>
<body>

{{-- Header: seller on the left, invoice identity on the right --}}
<table>
  <tr>
    <td style="width: 55%">
      <div class="brand">{{ $seller['name'] }}</div>
      <div class="muted small" style="margin-top: 4px">
        @if($seller['address']){!! nl2br(e($seller['address'])) !!}<br>@endif
        @if($seller['email']){{ $seller['email'] }}@endif @if($seller['phone']) &nbsp;·&nbsp; {{ $seller['phone'] }}@endif
      </div>
    </td>
    <td style="width: 45%" class="right">
      <div class="title">INVOICE</div>
      <div style="margin-top: 6px"><strong>{{ $order->invoice_number }}</strong></div>
      <div class="muted small">Issued {{ $issuedAt->format('d M Y') }}</div>
      <div style="margin-top: 6px"><span class="badge {{ $paid ? 'badge-paid' : 'badge-unpaid' }}">{{ $statusLabel }}</span></div>
    </td>
  </tr>
</table>

<div class="rule"></div>

{{-- Bill to / delivery / order --}}
<table>
  <tr>
    <td style="width: 34%; padding-right: 12px">
      <div class="label">Billed to</div>
      <strong>{{ $order->name }}</strong><br>
      <span class="muted">{{ $order->email }}<br>{{ $order->phone }}</span>
    </td>
    <td style="width: 38%; padding-right: 12px">
      <div class="label">Ship to</div>
      {!! nl2br(e($order->address)) !!}<br>
      {{ $order->city }}, {{ $order->postal_code }}
      @if($order->delivery_schedule)
        <div class="muted small" style="margin-top: 3px">Requested delivery: {{ $order->delivery_schedule }}</div>
      @endif
    </td>
    <td style="width: 28%">
      <div class="label">Order details</div>
      Order no. <strong>{{ $order->order_number }}</strong><br>
      <span class="muted">Placed {{ $placedAt->format('d M Y, H:i') }} WIB</span><br>
      <span class="muted">Payment: {{ $paymentLabel }}</span>
      @if($order->courier_name)<br><span class="muted">Courier: {{ $order->courier_name }}</span>@endif
    </td>
  </tr>
</table>

{{-- Line items --}}
<table class="items" style="margin-top: 18px">
  <thead>
    <tr>
      <th style="width: 6%">#</th>
      <th style="width: 44%">Description</th>
      <th style="width: 8%" class="center">Qty</th>
      <th style="width: 20%; text-align: right">Unit price</th>
      <th style="width: 22%; text-align: right">Amount</th>
    </tr>
  </thead>
  <tbody>
    @foreach($order->items as $i => $item)
      <tr class="{{ $i % 2 ? 'alt' : '' }}">
        <td class="muted">{{ $i + 1 }}</td>
        <td>
          <strong>{{ $item->product_name }}</strong>
          @if($item->variant_label)<br><span class="muted small">{{ $item->variant_label }}</span>@endif
        </td>
        <td class="center">{{ $item->qty }}</td>
        <td class="right">{{ rupiah($item->unit_price) }}</td>
        <td class="right"><strong>{{ rupiah($item->subtotal) }}</strong></td>
      </tr>
    @endforeach
  </tbody>
</table>

{{-- Totals --}}
<table style="margin-top: 10px">
  <tr>
    <td style="width: 52%"></td>
    <td style="width: 48%">
      <table class="totals">
        <tr><td class="muted">Subtotal</td><td class="right">{{ rupiah($order->subtotal) }}</td></tr>
        @if((float) $order->promo_discount > 0)
          <tr><td class="muted">Discount{{ $order->promo_code ? ' ('.$order->promo_code.')' : '' }}</td><td class="right">− {{ rupiah($order->promo_discount) }}</td></tr>
        @endif
        <tr><td class="muted">Shipping</td><td class="right">{{ (float) $order->shipping > 0 ? rupiah($order->shipping) : 'Free' }}</td></tr>
        @if($order->has_insurance && (float) $order->insurance_fee > 0)
          <tr><td class="muted">Shipping insurance</td><td class="right">{{ rupiah($order->insurance_fee) }}</td></tr>
        @endif
        @php $taxRate = (float) $order->subtotal > 0 ? round((float) $order->tax / (float) $order->subtotal * 100, 1) : 0; @endphp
        <tr><td class="muted">Tax{{ $taxRate > 0 ? ' ('.rtrim(rtrim(number_format($taxRate, 1, '.', ''), '0'), '.').'%)' : '' }}</td><td class="right">{{ rupiah($order->tax) }}</td></tr>
        <tr class="grand"><td>Total</td><td class="right">{{ rupiah($order->total) }}</td></tr>
      </table>
    </td>
  </tr>
</table>

{{-- Payment --}}
<div style="margin-top: 18px">
  @if($paid)
    <div class="pay"><strong>Payment received — thank you!</strong><br><span class="muted">This invoice is settled in full. No further payment is due.</span></div>
  @elseif($order->payment_method === 'qris')
    <div class="pay"><strong>How to pay</strong><br><span class="muted">Scan the QRIS code shown on your order page to pay {{ rupiah($order->total) }}.</span></div>
  @else
    <div class="pay">
      <strong>How to pay — bank transfer of {{ rupiah($order->total) }}</strong><br>
      @if($bank['number'])
        <span class="muted">Bank</span> {{ $bank['name'] ?: '—' }} &nbsp;·&nbsp;
        <span class="muted">Account no.</span> <strong>{{ $bank['number'] }}</strong>
        @if($bank['holder'])&nbsp;·&nbsp; <span class="muted">Account holder</span> {{ $bank['holder'] }}@endif
        <br>
      @else
        <span class="muted">Bank details will be sent to {{ $order->email }} — or contact us at {{ $seller['email'] ?: 'our support email' }}.</span><br>
      @endif
      <span class="muted small">Please include order no. {{ $order->order_number }} in the transfer note, then upload your proof of payment on the order page.</span>
    </div>
  @endif
</div>

@if($order->notes)
  <div style="margin-top: 12px"><div class="label">Notes</div><span class="muted">{{ $order->notes }}</span></div>
@endif

<div class="footer">
  @if($footerNote){{ $footerNote }}<br>@endif
  This invoice was generated automatically by {{ $seller['name'] }} and is valid without a signature.
</div>

</body>
</html>
