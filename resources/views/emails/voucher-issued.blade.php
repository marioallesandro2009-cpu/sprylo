<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Your voucher</title>
</head>
<body style="margin:0; padding:0; background:#f4f4f5; font-family: Arial, Helvetica, sans-serif; color:#18181b;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #e4e4e7;">
          <tr>
            <td style="padding:28px 32px; background:#4338ca; color:#ffffff;">
              <div style="font-size:13px; text-transform:uppercase; letter-spacing:0.08em; opacity:0.8;">{{ config('app.name') }}</div>
              <div style="font-size:20px; font-weight:700; margin-top:4px;">A voucher just for you 🎁</div>
            </td>
          </tr>
          <tr>
            <td style="padding:28px 32px;">
              <p style="margin:0 0 16px; font-size:14px; line-height:1.5;">Hi {{ $customerName }}, thank you for shopping with us. Here's a personal voucher for your next order:</p>
              <div style="border:2px dashed #a5b4fc; background:#eef2ff; border-radius:12px; text-align:center; padding:20px; margin-bottom:16px;">
                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#4338ca;">{{ $voucher->type === 'percent' ? (float) $voucher->value.'% off' : rupiah($voucher->value).' off' }}</div>
                <div style="font-size:26px; font-weight:800; letter-spacing:0.06em; margin:6px 0; font-family:Consolas, monospace;">{{ $voucher->code }}</div>
                <div style="font-size:12px; color:#52525b;">
                  @if($voucher->min_subtotal)Minimum order {{ rupiah($voucher->min_subtotal) }} · @endif
                  Valid until {{ $voucher->expires_at->format('d M Y') }} · one use only
                </div>
              </div>
              <p style="margin:0 0 16px; font-size:14px; line-height:1.5;">Enter the code in your cart under “Promo code” to use it.</p>
              <p style="margin:0;"><a href="{{ route('cart.index') }}" style="display:inline-block; background:#4338ca; color:#ffffff; text-decoration:none; padding:10px 20px; border-radius:8px; font-weight:600; font-size:14px;">Go to my cart</a></p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
