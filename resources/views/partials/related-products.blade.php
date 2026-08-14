@if($relatedProducts->isNotEmpty())
  <section class="section">
    <div class="section-head">
      <h2>You may also like</h2>
      <a href="{{ route('shop.index') }}" class="view-all">All products →</a>
    </div>
    <div class="products">
      @foreach($relatedProducts as $related)
        @include('partials.product-card', ['product' => $related])
      @endforeach
    </div>
  </section>
@endif
