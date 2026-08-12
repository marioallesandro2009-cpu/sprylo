@if($trendingProducts->isNotEmpty())
  <section class="section" style="padding-top: var(--s5)">
    <div class="container">
      <div class="section-head">
        <h2>Trending Products</h2>
        <a href="{{ route('shop.index') }}" class="view-all">View all
          <svg width="14" height="10" viewBox="0 0 14 10" fill="none"><path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>

      <div class="tabs" role="tablist">
        <button class="tab is-active" role="tab" aria-selected="true">Mobile</button>
        <button class="tab" role="tab">Watch</button>
        <button class="tab" role="tab">Camera</button>
        <button class="tab" role="tab">Accessories</button>
        <button class="tab" role="tab">Speaker</button>
      </div>

      <div class="products">
        @foreach($trendingProducts as $product)
          @include('partials.product-card', ['product' => $product])
        @endforeach
      </div>
    </div>
  </section>
@endif
