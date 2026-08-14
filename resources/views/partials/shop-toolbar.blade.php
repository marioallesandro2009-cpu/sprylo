<div class="shop-toolbar">
  <span class="count">Showing {{ count($products) }} products</span>
  <form method="GET" action="{{ route('shop.index') }}" style="display:flex; gap:var(--s3); align-items:center" id="sort-form">
    @foreach(request()->except('sort') as $key => $value)
      @foreach((array) $value as $v)
        <input type="hidden" name="{{ $key }}{{ is_array($value) ? '[]' : '' }}" value="{{ $v }}" />
      @endforeach
    @endforeach
    <span style="font-size:var(--text-xs); color:var(--fg-mute); font-family:var(--ff-mono)">SORT</span>
    <select name="sort" onchange="document.getElementById('sort-form').submit()">
      <option value="" @selected(request('sort') == '')>Most popular</option>
      <option value="price_asc" @selected(request('sort') == 'price_asc')>Price: low to high</option>
      <option value="price_desc" @selected(request('sort') == 'price_desc')>Price: high to low</option>
      <option value="newest" @selected(request('sort') == 'newest')>Newest first</option>
      <option value="rating" @selected(request('sort') == 'rating')>Highest rated</option>
    </select>
  </form>
</div>
