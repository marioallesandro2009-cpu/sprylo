@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Compare products — '.($settings['site_name'] ?? 'solis')))
@section('meta_description', $pageMeta->meta_description ?? 'Compare prices and specifications side by side.')

@section('content')
<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Compare</span></div>
    <h1>Compare products</h1>
    <p>
      @if($products->count() >= 2)
        Comparing {{ $products->count() }} products side by side. Prices include any active discount.
      @else
        Pick 2 to {{ $max }} products to see their prices and specifications side by side.
      @endif
    </p>
  </div>
</section>

<section class="section">
  <div class="container">
    @if($products->count() < 2)
      <div class="compare-empty">
        <div class="compare-empty__icon">⇄</div>
        <h2>{{ $products->isEmpty() ? 'Nothing to compare yet' : 'Add one more product' }}</h2>
        <p>Use the “Compare” button on any product card or product page to add it here.</p>
        @if($products->isNotEmpty())
          <div class="compare-empty__picked">
            @foreach($products as $p)<span>{{ $p->name }}</span>@endforeach
          </div>
        @endif
        <a href="{{ route('shop.index') }}" class="btn btn--indigo">Browse products →</a>
      </div>
    @else
      <div class="compare-toolbar">
        <label class="compare-toggle">
          <input type="checkbox" id="only-diff" />
          <span>Show differences only</span>
        </label>
        <form action="{{ route('compare.clear') }}" method="POST">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn--ghost">Clear all</button>
        </form>
      </div>

      <div class="compare-scroll">
        <table class="compare-table" style="--cols: {{ $products->count() }}">
          <thead>
            <tr>
              <th class="compare-corner" scope="col"><span>Product</span></th>
              @foreach($products as $product)
                @php
                  $price = $product->sale_price ?? $product->price;
                  $image = $product->images->first()->image ?? '';
                @endphp
                <th scope="col" class="compare-product">
                  <form action="{{ route('compare.remove', $product) }}" method="POST" class="compare-product__remove">
                    @csrf @method('DELETE')
                    <button type="submit" aria-label="Remove {{ $product->name }} from comparison">✕</button>
                  </form>
                  <a href="{{ route('product.show', $product) }}" class="compare-product__img"><img src="{{ $image }}" alt="{{ $product->name }}" /></a>
                  <a href="{{ route('product.show', $product) }}" class="compare-product__name">{{ $product->name }}</a>
                  <div class="compare-product__flags">
                    @if($product->id === $cheapestId)<span class="flag flag--price">Lowest price</span>@endif
                    @if($product->id === $topRatedId)<span class="flag flag--rating">Top rated</span>@endif
                    @if($product->promo?->is_flash)<span class="flag flag--flash">⚡ Flash sale</span>@endif
                  </div>
                </th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            <tr class="compare-row compare-row--price" data-same="0">
              <th scope="row">Price</th>
              @foreach($products as $product)
                @php $price = $product->sale_price ?? $product->price; @endphp
                <td class="{{ $product->id === $cheapestId ? 'is-best' : '' }}">
                  <div class="compare-price">{{ $product->has_variants ? 'From ' : '' }}{{ rupiah($price) }}</div>
                  @if($product->sale_price)
                    <div class="compare-was">{{ rupiah($product->price) }} <span class="save">−{{ round((1 - $price / $product->price) * 100) }}%</span></div>
                  @endif
                </td>
              @endforeach
            </tr>

            @foreach($overview as $row)
              <tr class="compare-row" data-same="{{ $row['same'] ? 1 : 0 }}">
                <th scope="row">{{ $row['label'] }}</th>
                @foreach($row['values'] as $i => $value)
                  <td>{{ $value }}</td>
                @endforeach
              </tr>
            @endforeach

            @if($specs->isNotEmpty())
              <tr class="compare-section"><th colspan="{{ $products->count() + 1 }}">Specifications</th></tr>
              @foreach($specs as $row)
                <tr class="compare-row" data-same="{{ $row['same'] ? 1 : 0 }}">
                  <th scope="row">{{ $row['label'] }}</th>
                  @foreach($row['values'] as $value)
                    <td class="{{ $value === '—' ? 'is-empty' : '' }}">{{ $value }}</td>
                  @endforeach
                </tr>
              @endforeach
            @endif
          </tbody>
          <tfoot>
            <tr>
              <th scope="row"></th>
              @foreach($products as $product)
                <td>
                  <form action="{{ route('cart.add', $product) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn--indigo" style="width:100%" @disabled($product->stock < 1)>{{ $product->stock < 1 ? 'Sold out' : 'Add to cart' }}</button>
                  </form>
                </td>
              @endforeach
            </tr>
          </tfoot>
        </table>
      </div>
    @endif
  </div>
</section>
@endsection

@push('scripts')
<script>
  (function () {
    var box = document.getElementById('only-diff');
    if (!box) return;
    function apply() {
      document.querySelectorAll('.compare-row').forEach(function (row) {
        row.hidden = box.checked && row.dataset.same === '1';
      });
      try { localStorage.setItem('compareOnlyDiff', box.checked ? '1' : '0'); } catch (e) {}
    }
    try { box.checked = localStorage.getItem('compareOnlyDiff') === '1'; } catch (e) {}
    box.addEventListener('change', apply);
    apply();
  })();
</script>
@endpush
