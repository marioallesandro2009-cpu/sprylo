<div class="shop-grid">
  @forelse($products as $product)
    @include('partials.product-card', ['product' => $product])
  @empty
    <p style="color: var(--fg-soft); padding: var(--s6) 0">No products match these filters. <a href="{{ route('shop.index') }}" style="color: var(--indigo)">Clear filters</a></p>
  @endforelse
</div>
