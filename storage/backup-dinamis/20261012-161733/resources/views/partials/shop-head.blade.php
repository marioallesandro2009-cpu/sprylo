<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Shop · All products</span></div>
    <h1>{{ cms('shop.head.title', 'All Products') }}</h1>
    <p>{{ cms('shop.head.description', ':count products across smartphones, laptops, audio, cameras, wearables, and gaming. Use the filters on the left to narrow down by brand, price, rating, or availability.', ['count' => count($products)]) }}</p>
  </div>
</section>
