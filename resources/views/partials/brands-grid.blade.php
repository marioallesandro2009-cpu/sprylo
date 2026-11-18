<section class="section">
  <div class="container">
    <div class="cats-grid">
      @foreach($allBrands as $brand)
        <a href="{{ route('shop.index', ['brands' => [$brand->id]]) }}" class="cat-tile">
          @if($brand->logo)
            <div class="pic"><img src="{{ $brand->logo }}" alt="{{ $brand->name }}" /></div>
          @endif
          <div class="name">{{ $brand->name }}</div>
          <div class="count">{{ $ui('ui_products_count', ['count' => $brand->products_count]) }}</div>
        </a>
      @endforeach
    </div>
  </div>
</section>
