<div>
  <form id="cart-update-form" action="{{ route('cart.update') }}" method="POST">
    @csrf @method('PATCH')
    <div class="cart-list">
      @foreach($cartItems as $item)
        @php $product = $item['product']; $variant = $item['variant']; @endphp
        <article class="cart-row">
          <div class="pic"><img src="{{ $product->images->first()->image ?? '' }}" alt="{{ $product->name }}" /></div>
          <div class="info">
            <a href="{{ route('product.show', $product) }}" class="name">{{ $product->name }}</a>
            <div class="variant">{{ $variant->label ?? $product->category->name }}</div>
            @if($variant)
              @if($item['has_warranty'])
                <div style="color: var(--emerald); font-size: 11px; font-weight: 600">✓ Includes travel warranty</div>
              @else
                <div style="color: var(--fg-mute); font-size: 11px">No travel warranty — not returnable for courier faults</div>
              @endif
            @endif
          </div>
          <div class="qty">
            <button type="button" data-act="-" aria-label="Decrease">−</button>
            <input type="text" name="qty[{{ $item['cart_key'] }}]" value="{{ $item['qty'] }}" inputmode="numeric" aria-label="Quantity" />
            <button type="button" data-act="+" aria-label="Increase">+</button>
          </div>
          <span class="subtotal">{{ rupiah($item['subtotal']) }}</span>
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
      <form class="cart-remove-form" action="{{ route('cart.remove', $item['product']) }}" method="POST" style="display:inline">
        @csrf @method('DELETE')
        @if($item['variant'])
          <input type="hidden" name="variant_id" value="{{ $item['variant']->id }}" />
        @endif
        <button type="submit" class="btn btn--ghost" style="font-size: 11px; padding: 4px 10px">Remove {{ $item['product']->name }}@if($item['variant']) ({{ $item['variant']->label }})@endif ✕</button>
      </form>
    @endforeach
  </div>

  @include('partials.trust-strip')
</div>
