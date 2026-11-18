@if($heroSlides->isNotEmpty())
<section class="hero">
  <div class="container">
    <div class="bento">
      @foreach($heroSlides as $slide)
        @php
          $link = $slide->product ? route('product.show', $slide->product) : route('shop.index');
        @endphp
        <article class="bento-card bento-card--size-{{ $slide->size }} theme-{{ $slide->theme }}">
          <div class="sparkle"></div>
          <div class="bento-card-body">
            <span class="eyebrow">{{ $slide->eyebrow }}</span>
            @if($slide->size === 'large')
              <h2>{{ $slide->heading }}</h2>
              @if($slide->description)
                <p>{{ $slide->description }}</p>
              @endif
              <a href="{{ $link }}" class="btn btn--paper">{{ $slide->cta_label ?: $ui('ui_hero_cta_default') }}
                <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true"><path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </a>
            @else
              <h3>{{ $slide->heading }}</h3>
              <a href="{{ $link }}" class="shop-now">{{ $slide->cta_label ?: $ui('ui_hero_cta_default') }}
                <svg width="12" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true"><path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </a>
            @endif
          </div>
          <img class="product" src="{{ $slide->image }}" alt="{{ $slide->heading }}" />
        </article>
      @endforeach
    </div>
  </div>
</section>
@endif
