<section class="section">
  <div class="container">
    <div class="cats-grid">
      @foreach($allBrands as $brand)
        <a href="{{ route('shop.index', ['brands' => [$brand->id]]) }}" class="cat-tile">
          @if($brand->logo)
            <div class="pic"><img src="{{ $brand->logo }}" alt="{{ $brand->name }}" /></div>
          @endif
          <div class="name">{{ $brand->name }}</div>
          <div class="count">{{ $brand->products_count }} Products</div>
        </a>
      @endforeach
    </div>
  </div>
</section>
