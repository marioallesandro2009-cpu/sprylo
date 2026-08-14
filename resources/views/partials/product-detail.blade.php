@php
  $displayPrice = $product->sale_price ?? $product->price;
  $rating = $product->rating;
  $mainImage = $product->images->first()->image ?? '';
@endphp
<section class="product-detail">
  @include('partials.product-gallery')
  @include('partials.product-info')
</section>
