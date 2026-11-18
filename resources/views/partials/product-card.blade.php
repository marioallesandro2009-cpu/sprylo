@php
  $displayPrice = $product->sale_price ?? $product->price;
  $rating = $product->rating;
  $image = $product->images->first()->image ?? '';
@endphp
<article class="product-card">
  <div class="img-wrap">
    @if($product->badge)
      <span class="badge @if(in_array($product->badge, ['Sale', 'Flash Sale'])) badge--sale @endif">{{ $product->badge }}</span>
    @endif
    @if(in_array($product->id, $wishlistIds ?? []))
      <form action="{{ route('wishlist.remove', $product) }}" method="POST" style="display:contents">
        @csrf @method('DELETE')
        <button type="submit" class="wishlist is-active" aria-label="Remove from wishlist">♥</button>
      </form>
    @else
      <form action="{{ route('wishlist.add', $product) }}" method="POST" style="display:contents">
        @csrf
        <button type="submit" class="wishlist" aria-label="Add to wishlist">♡</button>
      </form>
    @endif
    <img src="{{ $image }}" alt="{{ $product->name }}" />
  </div>
  <div class="stock">
    <span class="dot @if($product->stock < 1) is-out @endif"></span>
    {{ $product->stock < 1 ? $ui('ui_product_sold_out') : $ui('ui_product_in_stock', ['stock' => $product->stock]) }}
  </div>
  <a href="{{ route('product.show', $product) }}" class="name">{{ $product->name }}</a>
  <div class="price">
    @if($product->has_variants)
      <span class="now">{{ $ui('ui_price_from') }} {{ rupiah($displayPrice) }}</span>
    @else
      <span class="now">{{ rupiah($displayPrice) }}</span>
      @if($product->sale_price)
        <span class="was">{{ rupiah($product->price) }}</span>
      @endif
    @endif
  </div>
  @if($product->promo?->is_flash && $product->promo->remaining !== null)
    <div class="flash-left">⚡ {{ $product->promo->remaining }} left at this price</div>
  @endif
  <div class="stars">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }} <span class="count">({{ $product->reviews_count }})</span></div>
  @php $comparing = in_array($product->id, $compareIds ?? []); @endphp
  <form action="{{ route('compare.toggle', $product) }}" method="POST" class="compare-form">
    @csrf
    <button type="submit" class="compare-btn {{ $comparing ? 'is-active' : '' }}" aria-pressed="{{ $comparing ? 'true' : 'false' }}">{{ $comparing ? '✓ Comparing' : '⇄ Compare' }}</button>
  </form>
  <form action="{{ route('cart.add', $product) }}" method="POST">
    @csrf
    <button type="submit" class="btn" @disabled($product->stock < 1)>{{ $product->stock < 1 ? $ui('ui_product_sold_out') : $ui('ui_card_order_now') }}</button>
  </form>
</article>
