@extends('layouts.app')

@section('title', $product->name.' — '.($settings['site_name'] ?? 'Sprylo'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description), 160))

@section('content')
  @php
    $displayPrice = $product->sale_price ?? $product->price;
    $rating = $product->rating;
    $mainImage = $product->images->first()->image ?? '';
  @endphp
  <div class="container">
    <div class="crumbs" style="padding-top: var(--s5)"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <a href="{{ route('shop.index') }}">Shop</a> <span class="sep">›</span> <span>{{ $product->category->name }}</span></div>

    <section class="product-detail">

      <div class="gallery">
        <div class="gallery-thumbs">
          @foreach($product->images as $image)
            <button class="@if($loop->first) is-active @endif"><img src="{{ $image->image }}" alt="" /></button>
          @endforeach
        </div>
        <figure class="gallery-main">
          <img src="{{ str_replace('w=500', 'w=900', $mainImage) }}" alt="{{ $product->name }}" />
        </figure>
      </div>

      <div class="pdp-info">
        <span class="pdp-cat">{{ $product->category->name }}</span>
        <h1>{{ $product->name }}</h1>
        <div class="rating-row">
          <span style="color: var(--amber); font-size: 16px">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }}</span>
          <span>{{ $product->rating_avg }} / 5 · {{ $product->reviews_count }} reviews</span>
          <span style="color: var(--rule-strong)">|</span>
          <span style="color: var(--emerald); display:inline-flex; align-items:center; gap:6px"><span
              style="width:6px;height:6px;background:var(--emerald);border-radius:999px;display:inline-block"></span>
            In stock · {{ $product->stock }} items</span>
        </div>
        <p class="desc">{{ $product->description }}</p>

        <div class="price-row">
          <span class="now">${{ number_format($displayPrice, 0) }}</span>
          @if($product->sale_price)
            <span class="was">${{ number_format($product->price, 0) }}</span>
            <span class="save">Save {{ round((1 - $product->sale_price / $product->price) * 100) }}%</span>
          @endif
        </div>

        <form class="pdp-cta" action="{{ route('cart.add', $product) }}" method="POST">
          @csrf
          <div class="qty">
            <button type="button" data-act="-" aria-label="Decrease">−</button>
            <input type="text" name="qty" value="1" inputmode="numeric" aria-label="Quantity" />
            <button type="button" data-act="+" aria-label="Increase">+</button>
          </div>
          @if($product->stock < 1)
            <button type="submit" class="btn btn--indigo" style="flex:1; min-width:160px" disabled>Out of stock</button>
          @else
            <button type="submit" class="btn btn--indigo" style="flex:1; min-width:160px">Add to cart →</button>
            <button type="submit" class="btn btn--ink">Buy now</button>
          @endif
          <button type="button" class="icon-btn" aria-label="Add to wishlist" style="background: var(--bg)">♡</button>
        </form>

        <div class="pdp-features">
          <div class="pf"><span class="ic">⚡</span><span>{{ $settings['trust_shipping_title'] ?? '' }}</span></div>
          <div class="pf"><span class="ic">↺</span><span>{{ $settings['trust_returns_title'] ?? '' }}</span></div>
          <div class="pf"><span class="ic">★</span><span>{{ $settings['trust_warranty_title'] ?? '' }}</span></div>
          <div class="pf"><span class="ic">✓</span><span>{{ $settings['trust_authentic_title'] ?? '' }}</span></div>
        </div>
      </div>

    </section>

    <!-- Specs + reviews split -->
    <section class="section" style="padding-top: 0;">
      <div style="display:grid; grid-template-columns: 1fr 1fr; gap: var(--s8)">

        <div>
          <h2 style="font-size: var(--text-2xl); margin-bottom: var(--s5)">Specifications</h2>
          <table style="width:100%; border-collapse: collapse; font-size: var(--text-sm)">
            <tbody>
              @foreach($specs as $spec)
                <tr @if(!$loop->last) style="border-bottom: 1px solid var(--rule)" @endif>
                  <td style="padding: var(--s4) 0; color: var(--fg-mute); font-family: var(--ff-mono); font-size: var(--text-xs); letter-spacing: 0.06em; text-transform: uppercase; width: 40%">{{ $spec['label'] }}</td>
                  <td style="padding: var(--s4) 0; font-weight: 600">{{ $spec['value'] }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div>
          <h2 style="font-size: var(--text-2xl); margin-bottom: var(--s5)">Reviews</h2>
          <div style="display:grid; grid-template-columns: auto 1fr; gap: var(--s5); align-items:center; padding: var(--s5); background: var(--bg); border-radius: var(--r); margin-bottom: var(--s5)">
            <div style="text-align:center">
              <div style="font-family: var(--ff-display); font-size: 48px; font-weight: 800; color: var(--ink); line-height: 1">{{ $reviewSummary['average'] }}</div>
              <div style="color: var(--amber); font-size: 18px">{{ str_repeat('★', $product->rating) }}{{ str_repeat('☆', 5 - $product->rating) }}</div>
              <div style="font-family: var(--ff-mono); font-size: 11px; color: var(--fg-mute); margin-top: 4px">{{ $reviewSummary['count'] }} reviews</div>
            </div>
            <div style="display:grid; gap:6px; font-family: var(--ff-mono); font-size: var(--text-xs); color: var(--fg-soft)">
              @foreach($reviewSummary['breakdown'] as $stars => $pct)
                <div style="display:flex; align-items:center; gap:8px"><span style="width: 16px">{{ $stars }}</span><span
                    style="flex:1; height:6px; background:var(--rule); border-radius:999px; overflow:hidden"><span
                      style="display:block; width:{{ $pct }}%; height:100%; background: var(--amber)"></span></span><span
                    style="width: 28px; text-align:right">{{ $pct }}%</span></div>
              @endforeach
            </div>
          </div>

          @forelse($reviews as $review)
            <article style="border-bottom: 1px solid var(--rule); padding-bottom: var(--s5); margin-bottom: var(--s5)">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px">
                <strong style="font-family: var(--ff-display); font-weight: 700">{{ $review->name }}</strong>
                <span style="font-family: var(--ff-mono); font-size: 11px; color: var(--fg-mute)">{{ $review->created_at->diffForHumans() }}</span>
              </div>
              <div style="color: var(--amber); font-size: 14px; margin-bottom: 6px">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
              <p style="font-size: var(--text-sm); color: var(--fg-soft); line-height: 1.6">{{ $review->comment }}</p>
            </article>
          @empty
            <p style="color: var(--fg-soft); font-size: var(--text-sm)">No reviews yet — be the first to share your experience.</p>
          @endforelse
        </div>

      </div>
    </section>

    <!-- Related products -->
    @if($relatedProducts->isNotEmpty())
      <section class="section">
        <div class="section-head">
          <h2>You may also like</h2>
          <a href="{{ route('shop.index') }}" class="view-all">All products →</a>
        </div>
        <div class="products">
          @foreach($relatedProducts as $related)
            @include('partials.product-card', ['product' => $related])
          @endforeach
        </div>
      </section>
    @endif

  </div>
@endsection
