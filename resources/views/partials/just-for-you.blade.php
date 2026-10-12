@if($justForYou->isNotEmpty())
  <section class="section" style="padding-top:0">
    <div class="container">
      <div class="section-head">
        <h2>{{ cms('home.picks.title', 'Just for you') }}</h2>
        <a href="{{ route('shop.index') }}" class="view-all">{{ cms('home.picks.view_all', 'More picks') }}
          <svg width="14" height="10" viewBox="0 0 14 10" fill="none"><path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>
      <div class="compact-row">
        @foreach($justForYou as $item)
          <article class="compact-card">
            <div class="pic"><img src="{{ $item->images->first()->image ?? '' }}" alt="{{ $item->name }}" /></div>
            <div>
              <div class="stock">IN STOCK · {{ $item->stock }}</div>
              <div class="name">{{ $item->name }}</div>
              <div class="price">{{ $item->has_variants ? 'From ' : '' }}{{ rupiah($item->sale_price ?? $item->price) }}</div>
              <form action="{{ route('cart.add', $item) }}" method="POST">
                @csrf
                <button type="submit" class="btn" @disabled($item->stock < 1)>{{ $item->stock < 1 ? cms('shop.card.out_of_stock', 'Out of stock') : cms('home.picks.order_now', 'Order Now') }}</button>
              </form>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>
@endif
