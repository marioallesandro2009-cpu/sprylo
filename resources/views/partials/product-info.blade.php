<div class="pdp-info">
  <span class="pdp-cat">{{ $product->category->name }}</span>
  <h1>{{ $product->name }}</h1>
  <div class="rating-row">
    <span style="color: var(--amber); font-size: 16px">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }}</span>
    <span>{{ $product->rating_avg }} / 5 · {{ $product->reviews_count }} reviews</span>
    <span style="color: var(--rule-strong)">|</span>
    <span style="color: var(--emerald); display:inline-flex; align-items:center; gap:6px"><span
        style="width:6px;height:6px;background:var(--emerald);border-radius:999px;display:inline-block"></span>
      In stock · {{ $product->stock }} items</span>
  </div>
  <p class="desc">{{ $product->description }}</p>

  <div class="price-row">
    <span class="now">{{ rupiah($displayPrice) }}</span>
    @if($product->sale_price)
      <span class="was">{{ rupiah($product->price) }}</span>
      <span class="save">Save {{ round((1 - $product->sale_price / $product->price) * 100) }}%</span>
    @endif
  </div>

  <form class="pdp-cta" action="{{ route('cart.add', $product) }}" method="POST">
    @csrf
    <div class="qty">
      <button type="button" data-act="-" aria-label="Decrease">−</button>
      <input type="text" name="qty" value="1" inputmode="numeric" aria-label="Quantity" />
      <button type="button" data-act="+" aria-label="Increase">+</button>
    </div>
    @if($product->stock < 1)
      <button type="submit" class="btn btn--indigo" style="flex:1; min-width:160px" disabled>Out of stock</button>
    @else
      <button type="submit" class="btn btn--indigo" style="flex:1; min-width:160px">Add to cart →</button>
      <button type="submit" class="btn btn--ink">Buy now</button>
    @endif
  </form>

  @if(in_array($product->id, $wishlistIds ?? []))
    <form action="{{ route('wishlist.remove', $product) }}" method="POST" style="margin-top: var(--s3)">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn--ghost" style="color: var(--rose); border-color: var(--rose)">♥ Remove from wishlist</button>
    </form>
  @else
    <form action="{{ route('wishlist.add', $product) }}" method="POST" style="margin-top: var(--s3)">
      @csrf
      <button type="submit" class="btn btn--ghost">♡ Add to wishlist</button>
    </form>
  @endif

  <div class="pdp-features">
    <div class="pf"><span class="ic">⚡</span><span>{{ $settings['trust_shipping_title'] ?? '' }}</span></div>
    <div class="pf"><span class="ic">↺</span><span>{{ $settings['trust_returns_title'] ?? '' }}</span></div>
    <div class="pf"><span class="ic">★</span><span>{{ $settings['trust_warranty_title'] ?? '' }}</span></div>
    <div class="pf"><span class="ic">✓</span><span>{{ $settings['trust_authentic_title'] ?? '' }}</span></div>
  </div>
</div>
