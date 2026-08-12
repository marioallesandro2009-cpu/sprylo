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
    <button class="wishlist" aria-label="Wishlist">♡</button>
    <img src="{{ $image }}" alt="{{ $product->name }}" />
  </div>
  <div class="stock"><span class="dot"></span>In stock · {{ $product->stock }} items</div>
  <a href="{{ route('product.show', $product) }}" class="name">{{ $product->name }}</a>
  <div class="price">
    <span class="now">${{ number_format($displayPrice, 0) }}</span>
    @if($product->sale_price)
      <span class="was">${{ number_format($product->price, 0) }}</span>
    @endif
  </div>
  <div class="stars">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }} <span class="count">({{ $product->reviews_count }})</span></div>
  <form action="{{ route('cart.add', $product) }}" method="POST">
    @csrf
    <button type="submit" class="btn" @disabled($product->stock < 1)>{{ $product->stock < 1 ? 'Out of stock' : 'Order now →' }}</button>
  </form>
</article>
