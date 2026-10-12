@php
  $hasActiveFilters = request()->anyFilled(['categories', 'brands', 'price_min', 'price_max', 'min_rating', 'in_stock', 'on_sale', 'new_arrivals']);
@endphp
<button type="button" class="filters-toggle" aria-expanded="false" aria-controls="shop-filters">
  <span>Filters @if($hasActiveFilters)<span class="dot"></span>@endif</span>
  <svg class="chev" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
</button>
<form class="filters" id="shop-filters" aria-label="Filters" method="GET" action="{{ route('shop.index') }}">
  <div class="filter-block">
    <h3>{{ cms('shop.filter.category', 'Category') }}</h3>
    @foreach($categories as $category)
      <label><input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array($category->id, (array) request('categories', [])))> {{ $category->name }} <span class="ct">{{ $category->products_count }}</span></label>
    @endforeach
  </div>
  <div class="filter-block">
    <h3>{{ cms('shop.filter.price', 'Price range') }}</h3>
    <div style="display:flex; justify-content:space-between; font-family:var(--ff-mono); font-size:11px; color:var(--fg-mute)">
      <span>{{ rupiah($priceRange['min']) }}</span><span>{{ rupiah($priceRange['max']) }}</span></div>
    <div style="display:flex; gap:8px; margin-top:var(--s2)">
      <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="Min" style="width:50%; font-family:var(--ff-mono); font-size:var(--text-xs); padding:6px 8px; border:1px solid var(--rule); border-radius:6px" />
      <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Max" style="width:50%; font-family:var(--ff-mono); font-size:var(--text-xs); padding:6px 8px; border:1px solid var(--rule); border-radius:6px" />
    </div>
  </div>
  <div class="filter-block">
    <h3>{{ cms('shop.filter.brand', 'Brand') }}</h3>
    @foreach($brands as $brand)
      <label><input type="checkbox" name="brands[]" value="{{ $brand->id }}" @checked(in_array($brand->id, (array) request('brands', [])))> {{ $brand->name }} <span class="ct">{{ $brand->products_count }}</span></label>
    @endforeach
  </div>
  <div class="filter-block">
    <h3>{{ cms('shop.filter.rating', 'Rating') }}</h3>
    @foreach([5, 4, 3] as $stars)
      <label><input type="radio" name="min_rating" value="{{ $stars }}" @checked(request('min_rating') == $stars)> {{ str_repeat('★', $stars) }}{{ str_repeat('☆', 5 - $stars) }} &amp; up <span class="ct">{{ $ratingCounts[$stars] }}</span></label>
    @endforeach
  </div>
  <div class="filter-block">
    <h3>{{ cms('shop.filter.availability', 'Availability') }}</h3>
    <label><input type="checkbox" name="in_stock" value="1" @checked(request('in_stock'))> In stock <span class="ct">{{ $availabilityCounts['in_stock'] }}</span></label>
    <label><input type="checkbox" name="on_sale" value="1" @checked(request('on_sale'))> On sale <span class="ct">{{ $availabilityCounts['on_sale'] }}</span></label>
    <label><input type="checkbox" name="new_arrivals" value="1" @checked(request('new_arrivals'))> New arrivals <span class="ct">{{ $availabilityCounts['new_arrivals'] }}</span></label>
  </div>
  <input type="hidden" name="sort" value="{{ request('sort') }}" />
  <button type="submit" class="btn btn--indigo btn--block">{{ cms('shop.filter.apply', 'Apply filters') }}</button>
  @if(request()->anyFilled(['categories', 'brands', 'price_min', 'price_max', 'min_rating', 'in_stock', 'on_sale', 'new_arrivals', 'q']))
    <a href="{{ route('shop.index') }}" class="btn btn--ghost btn--block" style="margin-top:var(--s2)">{{ cms('shop.filter.clear', 'Clear filters') }}</a>
  @endif
</form>
