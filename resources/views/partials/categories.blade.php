@if($homeCategories->isNotEmpty())
  <section class="section" style="padding-top: var(--s5)">
    <div class="container">
      <div class="section-head">
        <h2>{{ $ui('ui_home_categories_title') }}</h2>
        <a href="{{ route('shop.index') }}" class="view-all">{{ $ui('ui_home_categories_view_all') }}
          <svg width="14" height="10" viewBox="0 0 14 10" fill="none"><path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>
      <div class="cats-grid">
        @foreach($homeCategories as $category)
          <a href="{{ route('shop.index') }}" class="cat-tile">
            <div class="pic"><img src="{{ $category->image }}" alt="{{ $category->name }}" /></div>
            <div class="name">{{ $category->name }}</div>
            <div class="count">{{ $ui('ui_products_count', ['count' => $category->products_count]) }}</div>
          </a>
        @endforeach
      </div>
    </div>
  </section>
@endif
