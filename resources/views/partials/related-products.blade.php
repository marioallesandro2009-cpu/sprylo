@if($relatedProducts->isNotEmpty())
  <section class="section">
    <div class="section-head">
      <h2>{{ $ui('ui_related_title') }}</h2>
      <a href="{{ route('shop.index') }}" class="view-all">{{ $ui('ui_related_view_all') }}</a>
    </div>
    <div class="products">
      @foreach($relatedProducts as $related)
        @include('partials.product-card', ['product' => $related])
      @endforeach
    </div>
  </section>
@endif
