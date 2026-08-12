@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Cart — '.($settings['site_name'] ?? 'Sprylo')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')

  <section class="page-head">
    <div class="container">
      <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Shopping cart</span></div>
      <h1>Your cart</h1>
      @if($cartItems->isNotEmpty())
        <p>{{ $cartItems->sum('qty') }} items · ready to ship. {{ $totals['shipping'] === 0.0 ? 'Free delivery on this order.' : 'Add '.'$'.number_format(50 - $totals['subtotal'], 2).' more for free shipping.' }}</p>
      @else
        <p>Your cart is currently empty.</p>
      @endif
    </div>
  </section>

  @if(session('success'))
    <div class="container"><p style="color: var(--emerald); font-weight: 600; margin-top: var(--s4)">{{ session('success') }}</p></div>
  @endif
  @if(session('error'))
    <div class="container"><p style="color: var(--rose); font-weight: 600; margin-top: var(--s4)">{{ session('error') }}</p></div>
  @endif

  @if($cartItems->isEmpty())

    <section class="section">
      <div class="container" style="text-align:center; padding: var(--s8) 0">
        <p style="color: var(--fg-soft); margin-bottom: var(--s5)">Looks like you haven't added anything to your cart yet.</p>
        <a href="{{ route('shop.index') }}" class="btn btn--indigo">Browse the shop →</a>
      </div>
    </section>

  @else

    <section class="section">
      <div class="container">
        <div class="cart-layout">

          <div>
            <form action="{{ route('cart.update') }}" method="POST">
              @csrf @method('PATCH')
              <div class="cart-list">
                @foreach($cartItems as $item)
                  @php $product = $item['product']; @endphp
                  <article class="cart-row">
                    <div class="pic"><img src="{{ $product->images->first()->image ?? '' }}" alt="{{ $product->name }}" /></div>
                    <div class="info">
                      <a href="{{ route('product.show', $product) }}" class="name">{{ $product->name }}</a>
                      <div class="variant">{{ $product->category->name }}</div>
                    </div>
                    <div class="qty">
                      <button type="button" data-act="-" aria-label="Decrease">−</button>
                      <input type="text" name="qty[{{ $product->id }}]" value="{{ $item['qty'] }}" inputmode="numeric" aria-label="Quantity" />
                      <button type="button" data-act="+" aria-label="Increase">+</button>
                    </div>
                    <span class="subtotal">${{ number_format($item['subtotal'], 2) }}</span>
                  </article>
                @endforeach
              </div>

              <div style="margin-top: var(--s5); display: flex; gap: var(--s3); flex-wrap: wrap">
                <a href="{{ route('shop.index') }}" class="btn btn--ghost">← Continue shopping</a>
                <button type="submit" class="btn btn--ghost">Update cart</button>
              </div>
            </form>

            <div style="margin-top: var(--s4); display: flex; flex-direction: column; gap: 6px">
              @foreach($cartItems as $item)
                <form action="{{ route('cart.remove', $item['product']) }}" method="POST" style="display:inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn--ghost" style="font-size: 11px; padding: 4px 10px">Remove {{ $item['product']->name }} ✕</button>
                </form>
              @endforeach
            </div>

            <!-- Trust strip -->
            <div style="margin-top: var(--s7); display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--s4); padding: var(--s5); background: var(--bg); border-radius: var(--r)">
              <div style="display:flex; align-items:center; gap:var(--s3)">
                <div style="width:40px; height:40px; background:var(--indigo-soft); color:var(--indigo); border-radius:999px; display:grid; place-items:center; font-size:18px">⚡</div>
                <div>
                  <div style="font-family:var(--ff-display); font-weight:700; font-size:var(--text-sm)">{{ $settings['trust_shipping_title'] ?? '' }}</div>
                  <div style="font-family:var(--ff-mono); font-size:11px; color:var(--fg-mute)">{{ $settings['trust_shipping_subtitle'] ?? '' }}</div>
                </div>
              </div>
              <div style="display:flex; align-items:center; gap:var(--s3)">
                <div style="width:40px; height:40px; background:var(--indigo-soft); color:var(--indigo); border-radius:999px; display:grid; place-items:center; font-size:18px">↺</div>
                <div>
                  <div style="font-family:var(--ff-display); font-weight:700; font-size:var(--text-sm)">{{ $settings['trust_returns_title'] ?? '' }}</div>
                  <div style="font-family:var(--ff-mono); font-size:11px; color:var(--fg-mute)">{{ $settings['trust_returns_subtitle'] ?? '' }}</div>
                </div>
              </div>
              <div style="display:flex; align-items:center; gap:var(--s3)">
                <div style="width:40px; height:40px; background:var(--indigo-soft); color:var(--indigo); border-radius:999px; display:grid; place-items:center; font-size:18px">★</div>
                <div>
                  <div style="font-family:var(--ff-display); font-weight:700; font-size:var(--text-sm)">{{ $settings['trust_warranty_title'] ?? '' }}</div>
                  <div style="font-family:var(--ff-mono); font-size:11px; color:var(--fg-mute)">{{ $settings['trust_warranty_subtitle'] ?? '' }}</div>
                </div>
              </div>
            </div>
          </div>

          <aside class="cart-summary">
            <h3>Order summary</h3>

            <div class="promo-input">
              <input type="text" placeholder="Promo code" />
              <button type="button">Apply</button>
            </div>

            <div class="cart-line"><span>Subtotal · {{ $cartItems->sum('qty') }} items</span><span
                style="font-family:var(--ff-display); font-weight:600; color:var(--ink)">${{ number_format($totals['subtotal'], 2) }}</span></div>
            <div class="cart-line"><span>Shipping</span><span
                style="color: var(--emerald); font-weight: 600">{{ $totals['shipping'] == 0.0 ? 'Free' : '$'.number_format($totals['shipping'], 2) }}</span></div>
            <div class="cart-line"><span>Estimated tax</span><span
                style="font-family:var(--ff-display); font-weight:600; color:var(--ink)">${{ number_format($totals['tax'], 2) }}</span></div>
            @if($totals['promo_discount'] > 0)
              <div class="cart-line"><span>Promo · {{ $totals['promo_code'] }}</span><span
                  style="color: var(--rose); font-family:var(--ff-display); font-weight:600">−${{ number_format($totals['promo_discount'], 2) }}</span></div>
            @endif

            <div class="cart-line is-total"><span>Total</span><span>${{ number_format($totals['total'], 2) }}</span></div>

            <a href="#" class="btn btn--indigo btn--block">Proceed to checkout →</a>

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

        </div>
      </div>
    </section>

  @endif

@endsection
