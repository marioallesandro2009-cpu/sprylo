<aside class="cart-summary co-summary" id="checkout-summary" data-variant-url="{{ route('checkout.variant') }}">
  <h3>Order summary</h3>

  <p id="checkout-variant-message" class="checkout-variant-message" role="status" hidden></p>

  <ul class="co-items">
    @foreach($cartItems as $item)
      @php $image = $item['product']->images->first()->image ?? null; @endphp
      <li class="co-item">
        <span class="co-item__pic @unless($image) is-empty @endunless" data-initial="{{ mb_strtoupper(mb_substr($item['product']->name, 0, 1)) }}">
          @if($image)
            <img src="{{ $image }}" alt="" loading="lazy" onerror="this.parentElement.classList.add('is-empty'); this.remove();" />
          @endif
          <span class="co-item__qty">{{ $item['qty'] }}</span>
        </span>
        <div class="co-item__body">
          <span class="co-item__name">{{ $item['product']->name }}</span>
          @if($item['variant'] && $item['has_warranty'])
            <span class="cart-tag cart-tag--ok">Includes warranty</span>
          @endif
          @if($item['variant'])
            <label class="checkout-variant">
              <span>Option</span>
              <select class="checkout-variant-select" data-cart-key="{{ $item['cart_key'] }}" aria-label="Option for {{ $item['product']->name }}">
                @foreach($item['product']->variants as $variant)
                  <option value="{{ $variant->id }}" @selected($variant->id === $item['variant']->id) @disabled($variant->stock < 1 && $variant->id !== $item['variant']->id)>
                    {{ $variant->label }} — {{ rupiah($variant->sale_price ?? $variant->price) }}@if($variant->stock < 1) (sold out)@endif
                  </option>
                @endforeach
              </select>
            </label>
          @endif
        </div>
        <span class="co-item__price">{{ rupiah($item['subtotal']) }}</span>
      </li>
    @endforeach
  </ul>

  <div class="cart-line"><span>Subtotal</span><span class="v">{{ rupiah($totals['subtotal']) }}</span></div>
  <div class="cart-line"><span>Shipping</span><span id="summary-shipping" class="v">Select a courier below</span></div>
  <div class="cart-line" id="summary-insurance-row" style="display:none"><span>Shipping insurance</span><span id="summary-insurance" class="v"></span></div>
  @if($totals['promo_discount'] > 0)
    <div class="cart-line"><span>Promo · {{ $totals['promo_code'] }}</span><span class="v is-discount">−{{ rupiah($totals['promo_discount']) }}</span></div>
  @endif
  <div class="cart-line"><span>PPN {{ \App\Support\Cart::PPN_PERCENT }}%</span><span class="v">{{ rupiah($totals['tax']) }}</span></div>

  <div class="cart-line is-total"><span>Total</span><span id="summary-total"
      data-base="{{ (float) $totals['subtotal'] + (float) $totals['tax'] - (float) $totals['promo_discount'] }}"
      >{{ rupiah($totals['subtotal'] + $totals['tax'] - $totals['promo_discount']) }}</span></div>

  <p class="cart-summary__note">Demo checkout · no payment is charged. Your order is recorded for our team to follow up.</p>
</aside>
