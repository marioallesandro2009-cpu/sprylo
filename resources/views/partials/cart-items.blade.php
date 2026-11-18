@php
  $freeThreshold = (float) ($settings['shipping_free_threshold'] ?? 0);
  $cartSubtotal = (float) $totals['subtotal'];
  $shipPct = $freeThreshold > 0 ? min(100, (int) floor($cartSubtotal / $freeThreshold * 100)) : 100;
  $shipRemaining = max(0, $freeThreshold - $cartSubtotal);
@endphp
<div>
  @if($freeThreshold > 0)
    <div class="ship-progress">
      <p class="ship-progress__text">
        @if($shipRemaining > 0)
          Add <strong>{{ rupiah($shipRemaining) }}</strong> more to unlock free shipping
        @else
          You've unlocked <strong>free shipping</strong> on this order
        @endif
      </p>
      <div class="ship-progress__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $shipPct }}">
        <span style="width: {{ $shipPct }}%"></span>
      </div>
    </div>
  @endif

  <form id="cart-update-form" action="{{ route('cart.update') }}" method="POST">
    @csrf @method('PATCH')
    <div class="cart-list">
      @foreach($cartItems as $item)
        @php
          $product = $item['product'];
          $variant = $item['variant'];
          $image = $product->images->first()->image ?? null;
        @endphp
        <article class="cart-row">
          <a class="pic @unless($image) is-empty @endunless" href="{{ route('product.show', $product) }}" data-initial="{{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}" tabindex="-1" aria-hidden="true">
            @if($image)
              <img src="{{ $image }}" alt="" loading="lazy" onerror="this.parentElement.classList.add('is-empty'); this.remove();" />
            @endif
          </a>

          <div class="info">
            <a href="{{ route('product.show', $product) }}" class="name">{{ $product->name }}</a>
            <div class="variant">{{ $variant->label ?? $product->category->name }}</div>
            <div class="unit">{{ rupiah($item['unit_price']) }} each</div>
            @if($variant)
              @if($item['has_warranty'])
                <span class="cart-tag cart-tag--ok">Includes travel warranty</span>
              @else
                <span class="cart-tag">No travel warranty</span>
              @endif
            @endif
          </div>

          <div class="qty">
            <button type="button" data-act="-" aria-label="Decrease quantity of {{ $product->name }}">−</button>
            <input type="text" name="qty[{{ $item['cart_key'] }}]" value="{{ $item['qty'] }}" inputmode="numeric" aria-label="Quantity of {{ $product->name }}" />
            <button type="button" data-act="+" aria-label="Increase quantity of {{ $product->name }}">+</button>
          </div>

          <span class="subtotal">{{ rupiah($item['subtotal']) }}</span>

          <button type="submit" form="cart-remove-{{ $loop->index }}" class="remove" aria-label="Remove {{ $product->name }}@if($variant) ({{ $variant->label }})@endif from cart" title="Remove">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
          </button>
        </article>
      @endforeach
    </div>

    <div class="cart-actions">
      <a href="{{ route('shop.index') }}" class="btn btn--ghost">← Continue shopping</a>
      <noscript><button type="submit" class="btn btn--ghost">Update cart</button></noscript>
    </div>
  </form>

  @foreach($cartItems as $item)
    <form id="cart-remove-{{ $loop->index }}" class="cart-remove-form" action="{{ route('cart.remove', $item['product']) }}" method="POST" hidden>
      @csrf @method('DELETE')
      @if($item['variant'])
        <input type="hidden" name="variant_id" value="{{ $item['variant']->id }}" />
      @endif
    </form>
  @endforeach

  @include('partials.trust-strip')
</div>
