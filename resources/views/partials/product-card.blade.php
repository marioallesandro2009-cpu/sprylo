@php
  $displayPrice = $product->sale_price ?? $product->price;
  $rating = $product->rating;
  $image = $product->images->first()->image ?? '';
@endphp
<article class="product-card">
  <div class="img-wrap">
    @if($product->badge)
      <span class="badge @if($product->badge === 'Sale') badge--sale @endif">{{ $product->badge }}</span>
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
  <div class="stock"><span class="dot"></span>In stock · {{ $product->stock }} items</div>
  <a href="{{ route('product.show', $product) }}" class="name">{{ $product->name }}</a>
  <div class="price">
    <span class="now">{{ rupiah($displayPrice) }}</span>
    @if($product->sale_price)
      <span class="was">{{ rupiah($product->price) }}</span>
    @endif
  </div>
  <div class="stars">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }} <span class="count">({{ $product->reviews_count }})</span></div>
  <form action="{{ route('cart.add', $product) }}" method="POST">
    @csrf
    <button type="submit" class="btn" @disabled($product->stock < 1)>{{ $product->stock < 1 ? 'Out of stock' : 'Order now →' }}</button>
  </form>
</article>
