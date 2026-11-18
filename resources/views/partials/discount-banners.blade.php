@if($promoBanners->isNotEmpty())
  <section class="section" style="padding-top:0">
    <div class="container">
      <div class="discount-row">
        @foreach($promoBanners as $banner)
          <article class="discount-card theme-{{ $banner->theme }}">
            <div class="discount-card-body">
              <span class="meta">{{ $banner->label }}</span>
              <h3>{{ $banner->product->name }}
                @if($banner->discount_percent !== null)
                  <br/><span class="pct">{{ $ui('ui_discount_pct', ['percent' => $banner->discount_percent]) }}</span>
                @endif
              </h3>
              <a href="{{ route('product.show', $banner->product) }}" class="shop-now">{{ $ui('ui_discount_cta') }}
                <svg width="14" height="10" viewBox="0 0 14 10" fill="none"><path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </a>
            </div>
            <img class="product" src="{{ $banner->product->images->first()->image ?? '' }}" alt="{{ $banner->product->name }}" />
          </article>
        @endforeach
      </div>
    </div>
  </section>
@endif
