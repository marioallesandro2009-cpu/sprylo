<section class="section">
  <div class="container">
    <div class="shop-grid">
      @foreach($products as $product)
        @include('partials.product-card', ['product' => $product])
      @endforeach
    </div>
  </div>
</section>
