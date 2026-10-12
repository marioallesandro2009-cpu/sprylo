<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Order Confirmation</title>
</head>
<body style="margin:0; padding:0; background:#f4f4f5; font-family: Arial, Helvetica, sans-serif; color:#18181b;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding: 32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #e4e4e7;">
          <tr>
            <td style="padding:28px 32px; background:#4338ca; color:#ffffff;">
              <div style="font-size:13px; text-transform:uppercase; letter-spacing:0.08em; opacity:0.8;">Sprylo</div>
              <div style="font-size:20px; font-weight:700; margin-top:4px;">Thanks for your order!</div>
            </td>
          </tr>
          <tr>
            <td style="padding:28px 32px;">
              <p style="margin:0 0 16px; font-size:14px; line-height:1.5;">
                Hi {{ $order->name }}, we've received your order <strong>{{ $order->order_number }}</strong> and it's now <strong>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</strong>. We'll email you again once it ships.
              </p>

              @if($order->payment_method === 'bank_transfer' && $order->status === 'awaiting_proof')
                <p style="margin:0 0 16px; font-size:14px; line-height:1.5;">
                  Please transfer {{ rupiah($order->total) }} to our account and upload your proof of payment here:
                  <a href="{{ route('track-order.index', ['order_number' => $order->order_number]) }}">Upload transfer proof</a>.
                </p>
              @endif

              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; margin-bottom:16px;">
                @foreach($order->items as $item)
                  <tr>
                    <td style="padding:6px 0; color:#52525b;">{{ $item->product_name }}@if($item->variant_label) ({{ $item->variant_label }})@endif × {{ $item->qty }}</td>
                    <td style="padding:6px 0; text-align:right; font-weight:600;">{{ rupiah($item->subtotal) }}</td>
                  </tr>
                @endforeach
              </table>

              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px; color:#52525b; border-top:1px solid #e4e4e7; padding-top:12px;">
                <tr><td style="padding:4px 0;">Subtotal</td><td style="padding:4px 0; text-align:right;">{{ rupiah($order->subtotal) }}</td></tr>
                <tr><td style="padding:4px 0;">Shipping</td><td style="padding:4px 0; text-align:right;">{{ $order->shipping == 0.0 ? 'Free' : rupiah($order->shipping) }}</td></tr>
                <tr><td style="padding:4px 0;">Tax</td><td style="padding:4px 0; text-align:right;">{{ rupiah($order->tax) }}</td></tr>
                @if($order->has_insurance)
                  <tr><td style="padding:4px 0;">Shipping insurance</td><td style="padding:4px 0; text-align:right;">{{ rupiah($order->insurance_fee) }}</td></tr>
                @endif
                @if($order->promo_discount > 0)
                  <tr><td style="padding:4px 0;">Promo ({{ $order->promo_code }})</td><td style="padding:4px 0; text-align:right;">−{{ rupiah($order->promo_discount) }}</td></tr>
                @endif
                <tr><td style="padding:8px 0 0; font-weight:700; color:#18181b; font-size:15px;">Total</td><td style="padding:8px 0 0; text-align:right; font-weight:700; color:#18181b; font-size:15px;">{{ rupiah($order->total) }}</td></tr>
              </table>

              <div style="margin-top:24px; padding-top:16px; border-top:1px solid #e4e4e7;">
                <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:#a1a1aa; margin-bottom:6px;">Shipping to</div>
                <div style="font-size:14px; font-weight:600;">{{ $order->name }}</div>
                <div style="font-size:13px; color:#52525b; line-height:1.5;">{{ $order->address }}<br>{{ $order->city }}, {{ $order->postal_code }}</div>
                @if($order->requested_delivery_date)
                  <div style="font-size:13px; color:#52525b; margin-top:8px;">Requested delivery: {{ $order->requested_delivery_date->format('d M Y') }}</div>
                @endif
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
