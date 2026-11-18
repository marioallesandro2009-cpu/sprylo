@php $trendingTabs = $trendingTabs ?? collect(); @endphp
@if($trendingProducts->isNotEmpty())
  <section class="section" style="padding-top: var(--s5)">
    <div class="container">
      <div class="section-head">
        <h2>{{ $settings['home_trending_title'] ?? 'Trending Products' }}</h2>
        <a href="{{ route('shop.index') }}" class="view-all">{{ $settings['home_trending_link_label'] ?? 'View all' }}
          <svg width="14" height="10" viewBox="0 0 14 10" fill="none"><path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>

      @if($trendingTabs->isNotEmpty())
        <div class="tabs" role="tablist" data-trending-tabs>
          <button type="button" class="tab is-active" role="tab" aria-selected="true" data-panel="all">{{ $settings['home_trending_all_label'] ?? 'All' }}</button>
          @foreach($trendingTabs as $tab)
            <button type="button" class="tab" role="tab" aria-selected="false" data-panel="cat-{{ $tab->id }}">{{ $tab->name }}</button>
          @endforeach
        </div>
      @endif

      <div class="products" data-trending-panel="all">
        @foreach($trendingProducts as $product)
          @include('partials.product-card', ['product' => $product])
        @endforeach
      </div>

      @foreach($trendingTabs as $tab)
        <div class="products" data-trending-panel="cat-{{ $tab->id }}" style="display:none">
          @foreach($tab->trending as $product)
            @include('partials.product-card', ['product' => $product])
          @endforeach
        </div>
      @endforeach
    </div>
  </section>
@endif

@if($trendingTabs->isNotEmpty())
  @push('scripts')
    <script src="{{ asset('assets/js/trending-tabs.js') }}" defer></script>
  @endpush
@endif