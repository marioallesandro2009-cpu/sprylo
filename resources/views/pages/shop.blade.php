@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Shop — '.($settings['site_name'] ?? 'Sprylo')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')

  <section class="page-head">
    <div class="container">
      <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Shop · All products</span></div>
      <h1>All Products</h1>
      <p>{{ count($products) }} products across smartphones, laptops, audio, cameras, wearables, and gaming. Use the filters on the left to narrow down by brand, price, rating, or availability.</p>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="shop-layout">

        <form class="filters" aria-label="Filters" method="GET" action="{{ route('shop.index') }}">
          <div class="filter-block">
            <h3>Category</h3>
            @foreach($categories as $category)
              <label><input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array($category->id, (array) request('categories', [])))> {{ $category->name }} <span class="ct">{{ $category->products_count }}</span></label>
            @endforeach
          </div>
          <div class="filter-block">
            <h3>Price range</h3>
            <div style="display:flex; justify-content:space-between; font-family:var(--ff-mono); font-size:11px; color:var(--fg-mute)">
              <span>${{ number_format($priceRange['min']) }}</span><span>${{ number_format($priceRange['max']) }}</span></div>
            <div style="display:flex; gap:8px; margin-top:var(--s2)">
              <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="Min" style="width:50%; font-family:var(--ff-mono); font-size:var(--text-xs); padding:6px 8px; border:1px solid var(--rule); border-radius:6px" />
              <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Max" style="width:50%; font-family:var(--ff-mono); font-size:var(--text-xs); padding:6px 8px; border:1px solid var(--rule); border-radius:6px" />
            </div>
          </div>
          <div class="filter-block">
            <h3>Brand</h3>
            @foreach($brands as $brand)
              <label><input type="checkbox" name="brands[]" value="{{ $brand->id }}" @checked(in_array($brand->id, (array) request('brands', [])))> {{ $brand->name }} <span class="ct">{{ $brand->products_count }}</span></label>
            @endforeach
          </div>
          <div class="filter-block">
            <h3>Rating</h3>
            @foreach([5, 4, 3] as $stars)
              <label><input type="radio" name="min_rating" value="{{ $stars }}" @checked(request('min_rating') == $stars)> {{ str_repeat('★', $stars) }}{{ str_repeat('☆', 5 - $stars) }} &amp; up <span class="ct">{{ $ratingCounts[$stars] }}</span></label>
            @endforeach
          </div>
          <div class="filter-block">
            <h3>Availability</h3>
            <label><input type="checkbox" name="in_stock" value="1" @checked(request('in_stock'))> In stock <span class="ct">{{ $availabilityCounts['in_stock'] }}</span></label>
            <label><input type="checkbox" name="on_sale" value="1" @checked(request('on_sale'))> On sale <span class="ct">{{ $availabilityCounts['on_sale'] }}</span></label>
            <label><input type="checkbox" name="new_arrivals" value="1" @checked(request('new_arrivals'))> New arrivals <span class="ct">{{ $availabilityCounts['new_arrivals'] }}</span></label>
          </div>
          <input type="hidden" name="sort" value="{{ request('sort') }}" />
          <button type="submit" class="btn btn--indigo btn--block">Apply filters</button>
          @if(request()->anyFilled(['categories', 'brands', 'price_min', 'price_max', 'min_rating', 'in_stock', 'on_sale', 'new_arrivals', 'q']))
            <a href="{{ route('shop.index') }}" class="btn btn--ghost btn--block" style="margin-top:var(--s2)">Clear filters</a>
          @endif
        </form>

        <div>
          <div class="shop-toolbar">
            <span class="count">Showing {{ count($products) }} products</span>
            <form method="GET" action="{{ route('shop.index') }}" style="display:flex; gap:var(--s3); align-items:center" id="sort-form">
              @foreach(request()->except('sort') as $key => $value)
                @foreach((array) $value as $v)
                  <input type="hidden" name="{{ $key }}{{ is_array($value) ? '[]' : '' }}" value="{{ $v }}" />
                @endforeach
              @endforeach
              <span style="font-size:var(--text-xs); color:var(--fg-mute); font-family:var(--ff-mono)">SORT</span>
              <select name="sort" onchange="document.getElementById('sort-form').submit()">
                <option value="" @selected(request('sort') == '')>Most popular</option>
                <option value="price_asc" @selected(request('sort') == 'price_asc')>Price: low to high</option>
                <option value="price_desc" @selected(request('sort') == 'price_desc')>Price: high to low</option>
                <option value="newest" @selected(request('sort') == 'newest')>Newest first</option>
                <option value="rating" @selected(request('sort') == 'rating')>Highest rated</option>
              </select>
            </form>
          </div>

          <div class="shop-grid">
            @forelse($products as $product)
              @include('partials.product-card', ['product' => $product])
            @empty
              <p style="color: var(--fg-soft); padding: var(--s6) 0">No products match these filters. <a href="{{ route('shop.index') }}" style="color: var(--indigo)">Clear filters</a></p>
            @endforelse
          </div>

        </div>

      </div>
    </div>
  </section>

  @include('partials.brand-strip', ['brands' => $brands])

@endsection
