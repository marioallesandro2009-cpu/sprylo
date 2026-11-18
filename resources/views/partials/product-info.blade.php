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
    <span>{{ $ui('ui_product_rating', ['average' => $product->rating_avg, 'count' => $product->reviews_count]) }}</span>
    <span style="color: var(--rule-strong)">|</span>
    <span style="color: var(--{{ $selectedStock < 1 ? 'fg-mute' : 'emerald' }}); display:inline-flex; align-items:center; gap:6px" id="pdp-stock"><span
        style="width:6px;height:6px;background:var(--{{ $selectedStock < 1 ? 'fg-mute' : 'emerald' }});border-radius:999px;display:inline-block"></span>
      {{ $selectedStock < 1 ? $ui('ui_product_sold_out') : $ui('ui_product_in_stock', ['stock' => $selectedStock]) }}</span>
    <span style="color: var(--rule-strong)">|</span>
    <span style="color: var(--fg-mute)">{{ $ui('ui_product_weight') }} {{ weight_label($product->weight) }}</span>
  </div>
  <p class="desc">{{ $product->description }}</p>

  <div class="price-row" id="pdp-price-row">
    <span class="now">{{ $hasVariants ? $ui('ui_price_from').' ' : '' }}{{ rupiah($selectedPrice) }}</span>
    @if($selectedWasPrice)
      <span class="was">{{ rupiah($selectedWasPrice) }}</span>
      <span class="save">{{ $ui('ui_product_save', ['percent' => round((1 - $selectedPrice / $selectedWasPrice) * 100)]) }}</span>
    @endif
  </div>

  @php $promo = $product->promo; @endphp
  @if($promo && ($promo->is_flash || $promo->ends_at))
    <div class="promo-note {{ $promo->is_flash ? 'promo-note--flash' : '' }}">
      <strong>{{ $promo->is_flash ? '⚡ Flash sale' : 'Special price' }}</strong>
      @if($promo->remaining !== null)<span>· {{ $promo->remaining }} left at this price</span>@endif
      @if($promo->ends_at)<span>· ends {{ $promo->ends_at->copy()->timezone(\App\Models\Discount::DISPLAY_TZ)->format('d M, H:i') }} WIB</span>@endif
    </div>
  @endif

  @if($hasVariants)
    <div class="option-block">
      <div class="label">{{ $ui('ui_product_options') }}</div>
      <div class="option-pills option-pills--static">
        @foreach($variants as $variant)
          <span @class(['is-out' => $variant->stock < 1])>{{ $variant->label }}@if($variant->stock < 1) {{ $ui('ui_product_variant_sold_out') }}@endif</span>
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
    <button type="submit" class="btn btn--indigo" id="pdp-add-to-cart" style="flex:1; min-width:160px" @disabled($selectedStock < 1)>{{ $selectedStock < 1 ? $ui('ui_product_sold_out') : $ui('ui_product_add_to_cart') }}</button>
    <button type="submit" class="btn btn--ink" id="pdp-buy-now" @disabled($selectedStock < 1)>{{ $ui('ui_product_buy_now') }}</button>
  </form>

  @php $comparing = in_array($product->id, $compareIds ?? []); @endphp
  <form action="{{ route('compare.toggle', $product) }}" method="POST" style="margin-top: var(--s3)">
    @csrf
    <button type="submit" class="btn btn--ghost" aria-pressed="{{ $comparing ? 'true' : 'false' }}">{{ $comparing ? '✓ In comparison — remove' : '⇄ Compare with other products' }}</button>
  </form>

  @if(in_array($product->id, $wishlistIds ?? []))
    <form action="{{ route('wishlist.remove', $product) }}" method="POST" style="margin-top: var(--s3)">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn--ghost" style="color: var(--rose); border-color: var(--rose)">{{ $ui('ui_product_wishlist_remove') }}</button>
    </form>
  @else
    <form action="{{ route('wishlist.add', $product) }}" method="POST" style="margin-top: var(--s3)">
      @csrf
      <button type="submit" class="btn btn--ghost">{{ $ui('ui_product_wishlist_add') }}</button>
    </form>
  @endif

  <div class="pdp-features">
    <div class="pf"><span class="ic">⚡</span><span>{{ $settings['trust_shipping_title'] ?? '' }}</span></div>
    <div class="pf"><span class="ic">↺</span><span>{{ $settings['trust_returns_title'] ?? '' }}</span></div>
    <div class="pf"><span class="ic">★</span><span>{{ $settings['trust_warranty_title'] ?? '' }}</span></div>
    <div class="pf"><span class="ic">✓</span><span>{{ $settings['trust_authentic_title'] ?? '' }}</span></div>
  </div>
</div>
