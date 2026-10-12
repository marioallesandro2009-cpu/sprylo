@if($trendingProducts->isNotEmpty())
  <section class="section" style="padding-top: var(--s5)">
    <div class="container">
      <div class="section-head">
        <h2>{{ cms('home.trending.title', 'Trending Products') }}</h2>
        <a href="{{ route('shop.index') }}" class="view-all">{{ cms('home.trending.view_all', 'View all') }}
          <svg width="14" height="10" viewBox="0 0 14 10" fill="none"><path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>

      <div class="tabs" role="tablist">
        <button class="tab is-active" role="tab" aria-selected="true">{{ cms('home.trending.tab_1', 'Mobile') }}</button>
        <button class="tab" role="tab">{{ cms('home.trending.tab_2', 'Watch') }}</button>
        <button class="tab" role="tab">{{ cms('home.trending.tab_3', 'Camera') }}</button>
        <button class="tab" role="tab">{{ cms('home.trending.tab_4', 'Accessories') }}</button>
        <button class="tab" role="tab">{{ cms('home.trending.tab_5', 'Speaker') }}</button>
      </div>

      <div class="products">
        @foreach($trendingProducts as $product)
          @include('partials.product-card', ['product' => $product])
        @endforeach
      </div>
    </div>
  </section>
@endif
