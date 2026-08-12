@php
  $variants = ['watch', 'airpods'];
@endphp
@if($discountBanners->isNotEmpty())
  <section class="section" style="padding-top:0">
    <div class="container">
      <div class="discount-row">
        @foreach($discountBanners as $banner)
          @php
            $pct = round((1 - $banner->sale_price / $banner->price) * 100);
          @endphp
          <article class="discount-card discount-card--{{ $variants[$loop->index % 2] }}">
            <span class="meta">{{ $loop->first ? 'THIS WEEK ONLY' : 'LIMITED EDITION' }}</span>
            <h3>{{ $banner->name }}<br/><span class="pct">{{ $pct }}% Off</span></h3>
            <a href="{{ route('product.show', $banner) }}" class="shop-now">Shop Now
              <svg width="14" height="10" viewBox="0 0 14 10" fill="none"><path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <img class="product" src="{{ $banner->images->first()->image ?? '' }}" alt="{{ $banner->name }}" />
          </article>
        @endforeach
      </div>
    </div>
  </section>
@endif
