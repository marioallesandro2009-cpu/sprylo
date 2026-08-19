@php
  $banner = $banner ?? null;
@endphp

<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Product</label>
    <select name="product_id" class="form-select" required>
      <option value="">Select a product…</option>
      @foreach($products as $product)
        <option value="{{ $product->id }}" @selected(old('product_id', $banner->product_id ?? '') == $product->id)>
          {{ $product->name }} @if($product->sale_price) (sale price set) @endif
        </option>
      @endforeach
    </select>
    <div class="form-text">Only products with a sale price are listed — the % off badge is calculated from price vs. sale price. Set a product's sale price under Products first.</div>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Color theme</label>
    <select name="theme" class="form-select" required>
      @foreach(\App\Models\PromoBanner::THEMES as $value => $label)
        <option value="{{ $value }}" @selected(old('theme', $banner->theme ?? 'slate') === $value)>{{ $label }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-6">
    <label class="form-label small fw-semibold">Badge label</label>
    <input type="text" name="label" class="form-control" value="{{ old('label', $banner->label ?? '') }}" required placeholder="e.g. THIS WEEK ONLY" />
  </div>
  <div class="col-md-3">
    <label class="form-label small fw-semibold">Sort order</label>
    <input type="number" name="sort_order" min="0" class="form-control" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" />
    <div class="form-text">Lower numbers appear first.</div>
  </div>
  <div class="col-md-3">
    <label class="form-label small fw-semibold">Status</label>
    <div class="form-check form-switch mt-2">
      <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $banner->is_active ?? true)) />
      <label class="form-check-label small" for="is_active">Visible on homepage</label>
    </div>
  </div>
</div>
