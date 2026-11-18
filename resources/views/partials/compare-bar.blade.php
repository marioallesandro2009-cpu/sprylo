@php
  $barProducts = collect();
  if (! empty($compareIds) && ! request()->routeIs('compare.*')) {
    $barProducts = \App\Models\Product::with('images')->whereIn('id', $compareIds)->get()
      ->sortBy(fn ($p) => array_search($p->id, $compareIds))->values();
  }
@endphp
@if($barProducts->isNotEmpty())
  <div class="compare-bar" role="region" aria-label="Product comparison">
    <div class="compare-bar__inner">
      <div class="compare-bar__items">
        @foreach($barProducts as $item)
          <div class="compare-bar__item" title="{{ $item->name }}">
            <img src="{{ $item->images->first()->image ?? '' }}" alt="{{ $item->name }}" />
            <form action="{{ route('compare.remove', $item) }}" method="POST">
              @csrf @method('DELETE')
              <button type="submit" aria-label="Remove {{ $item->name }} from comparison">✕</button>
            </form>
          </div>
        @endforeach
        @for($i = $barProducts->count(); $i < \App\Http\Controllers\CompareController::MAX_ITEMS; $i++)
          <div class="compare-bar__slot" aria-hidden="true">+</div>
        @endfor
      </div>
      <div class="compare-bar__text">
        <strong>Compare {{ $barProducts->count() }}/{{ \App\Http\Controllers\CompareController::MAX_ITEMS }}</strong>
        <span>{{ $barProducts->count() < 2 ? 'Add at least one more product' : 'Ready to compare' }}</span>
      </div>
      <div class="compare-bar__actions">
        <form action="{{ route('compare.clear') }}" method="POST">
          @csrf @method('DELETE')
          <button type="submit" class="compare-bar__clear">Clear</button>
        </form>
        @if($barProducts->count() >= 2)
          <a href="{{ route('compare.index') }}" class="btn btn--indigo">Compare now →</a>
        @else
          <span class="btn btn--indigo is-disabled" aria-disabled="true">Compare now →</span>
        @endif
      </div>
    </div>
  </div>
@endif
