@php
  $fallbackLink = fn ($slide) => $slide?->product ? route('product.show', $slide->product) : route('shop.index');
@endphp
<section class="hero">
  <div class="container">
    <div class="bento">

      <article class="bento-card bento-card--lg">
        <div class="sparkle"></div>
        <div>
          <span class="eyebrow">{{ $settings['hero_eyebrow'] ?? '' }}</span>
          <h2>{{ $settings['hero_heading'] ?? '' }}</h2>
          <p>{{ $settings['hero_description'] ?? '' }}</p>
          <a href="{{ route('product.show', $settings['hero_product_slug'] ?? 'apple-homepod-2nd-gen') }}" class="btn btn--paper">{{ $settings['hero_cta_label'] ?? 'Shop Now' }}
            <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true"><path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
          <div class="dots"><span class="active"></span><span></span><span></span></div>
        </div>
        <img class="product" src="{{ $settings['hero_image'] ?? '' }}" alt="{{ $settings['hero_heading'] ?? '' }}" />
      </article>

      @foreach(['purple', 'teal'] as $position)
        @php $slide = $heroHighlights->get($position); @endphp
        @if($slide)
          <article class="bento-card bento-card--{{ $position }}">
            <div class="sparkle"></div>
            <span class="eyebrow">{{ $slide->eyebrow }}</span>
            <h3 style="font-size:var(--text-xl); line-height:1.15">{{ $slide->heading }}</h3>
            <a href="{{ $fallbackLink($slide) }}" class="shop-now">Shop Now
              <svg width="12" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true"><path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <img class="product" src="{{ $slide->image }}" alt="{{ $slide->heading }}" />
          </article>
        @endif
      @endforeach

      <div class="bento-row">
        @foreach(['orange', 'green', 'black'] as $position)
          @php $slide = $heroHighlights->get($position); @endphp
          @if($slide)
            <article class="bento-card bento-card--{{ $position }}">
              <div class="sparkle"></div>
              <span class="eyebrow">{{ $slide->eyebrow }}</span>
              <h3 style="font-size:var(--text-lg); line-height:1.2">{{ $slide->heading }}</h3>
              <a href="{{ $fallbackLink($slide) }}" class="shop-now">Shop Now
                <svg width="12" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true"><path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </a>
              <img class="product" src="{{ $slide->image }}" alt="{{ $slide->heading }}" />
            </article>
          @endif
        @endforeach
      </div>

    </div>
  </div>
</section>
