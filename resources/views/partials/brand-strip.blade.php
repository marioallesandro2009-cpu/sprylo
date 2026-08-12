@if($brands->isNotEmpty())
  <section class="brands">
    <div class="container">
      <div class="brand-row">
        @foreach($brands as $brand)
          <a href="{{ route('shop.index', ['brands' => [$brand->id]]) }}" class="brand-logo">{{ $brand->name }}</a>
        @endforeach
      </div>
    </div>
  </section>
@endif
