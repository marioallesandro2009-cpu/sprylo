@php
  $variants = $product->variants;
  $hasVariants = $variants->isNotEmpty();
  // The option is chosen at checkout, so the page shows the product as a whole: its
  // cheapest price ("From …"), combined stock and heaviest weight (all synced from variants).
  $selectedStock = $product->stock;
  $selectedPrice = $displayPrice;
  $selectedWasPrice = $product->sale_price ? $product->price : null;
@endphp
<div class="pdp-info" id="pdp-info" data-has-variants="{{ $hasVariants ? '1' : '0' }}">
  <span class="pdp-cat">{{ $product->category->name }}</span>
  <h1>{{ $product->name }}</h1>
  <div class="rating-row">
    <span style="color: var(--amber); font-size: 16px">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }}</span>
    <span>{{ $product->rating_avg }} / 5 · {{ $product->reviews_count }} reviews</span>
    <span style="color: var(--rule-strong)">|</span>
    <span style="color: var(--{{ $selectedStock < 1 ? 'fg-mute' : 'emerald' }}); display:inline-flex; align-items:center; gap:6px" id="pdp-stock"><span
        style="width:6px;height:6px;background:var(--{{ $selectedStock < 1 ? 'fg-mute' : 'emerald' }});border-radius:999px;display:inline-block"></span>
      @if($selectedStock < 1) Sold out @else In stock · {{ $selectedStock }} items @endif</span>
    <span style="color: var(--rule-strong)">|</span>
    <span style="color: var(--fg-mute)">Weight: {{ weight_label($product->weight) }}</span>
  </div>
  <p class="desc">{{ $product->description }}</p>

  <div class="price-row" id="pdp-price-row">
    <span class="now">{{ $hasVariants ? 'From ' : '' }}{{ rupiah($selectedPrice) }}</span>
    @if($selectedWasPrice)
      <span class="was">{{ rupiah($selectedWasPrice) }}</span>
      <span class="save">Save {{ round((1 - $selectedPrice / $selectedWasPrice) * 100) }}%</span>
    @endif
  </div>

  @if($hasVariants)
    <div class="option-block">
      <div class="label">Available options · choose at checkout</div>
      <div class="option-pills option-pills--static">
        @foreach($variants as $variant)
          <span @class(['is-out' => $variant->stock < 1])>{{ $variant->label }}@if($variant->stock < 1) (sold out)@endif</span>
        @endforeach
      </div>
    </div>
  @endif

  <form class="pdp-cta" action="{{ route('cart.add', $product) }}" method="POST" id="pdp-cta-form">
    @csrf
    <div class="qty">
      <button type="button" data-act="-" aria-label="Decrease">−</button>
      <input type="text" name="qty" value="1" inputmode="numeric" aria-label="Quantity" />
      <button type="button" data-act="+" aria-label="Increase">+</button>
    </div>
    <button type="submit" class="btn btn--indigo" id="pdp-add-to-cart" style="flex:1; min-width:160px" @disabled($selectedStock < 1)>{{ $selectedStock < 1 ? 'Sold out' : 'Add to cart →' }}</button>
    <button type="submit" class="btn btn--ink" id="pdp-buy-now" @disabled($selectedStock < 1)>Buy now</button>
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
