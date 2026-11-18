@php
  $discount = $discount ?? null;
  $tz = \App\Models\Discount::DISPLAY_TZ;
  $scope = old('scope', $discount->scope ?? 'product');
  $localTime = fn ($value) => $value ? $value->copy()->timezone($tz)->format('Y-m-d\TH:i') : '';
  $catalog = $products->map(fn ($p) => ['id' => $p->id, 'variants' => $p->variants->map(fn ($v) => ['id' => $v->id, 'label' => $v->label])->all()])->all();
@endphp

<div class="row g-3" id="discount-form" data-catalog="{{ json_encode($catalog) }}" data-variant="{{ old('product_variant_id', $discount->product_variant_id ?? '') }}">
  <div class="col-12">
    <label class="form-label small fw-semibold">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $discount->name ?? '') }}" placeholder="{{ $flash ? 'e.g. 12.12 Flash Sale — iPad' : 'e.g. Weekend Smartphone Sale' }}" required />
  </div>

  @if($flash)
    <input type="hidden" name="scope" value="product" />
  @else
    <div class="col-md-4">
      <label class="form-label small fw-semibold">Applies to</label>
      <select name="scope" class="form-select" id="scope" required>
        @foreach(\App\Models\Discount::SCOPES as $key => $label)
          <option value="{{ $key }}" @selected($scope === $key)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
  @endif

  <div class="col-md-{{ $flash ? 6 : 4 }}" data-scope-field="product">
    <label class="form-label small fw-semibold">Product</label>
    <select name="product_id" class="form-select" id="product_id">
      <option value="">— choose —</option>
      @foreach($products as $product)
        <option value="{{ $product->id }}" @selected((int) old('product_id', $discount->product_id ?? 0) === $product->id)>{{ $product->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-{{ $flash ? 6 : 4 }}" data-scope-field="product">
    <label class="form-label small fw-semibold">Variant <span class="text-muted fw-normal">(optional)</span></label>
    <select name="product_variant_id" class="form-select" id="product_variant_id"><option value="">All variants</option></select>
  </div>
  <div class="col-md-4" data-scope-field="category">
    <label class="form-label small fw-semibold">Category</label>
    <select name="category_id" class="form-select">
      <option value="">— choose —</option>
      @foreach($categories as $category)
        <option value="{{ $category->id }}" @selected((int) old('category_id', $discount->category_id ?? 0) === $category->id)>{{ $category->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4" data-scope-field="brand">
    <label class="form-label small fw-semibold">Brand</label>
    <select name="brand_id" class="form-select">
      <option value="">— choose —</option>
      @foreach($brands as $brand)
        <option value="{{ $brand->id }}" @selected((int) old('brand_id', $discount->brand_id ?? 0) === $brand->id)>{{ $brand->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-4">
    <label class="form-label small fw-semibold">Discount type</label>
    <select name="type" class="form-select" required>
      @foreach(\App\Models\Discount::TYPES as $key => $label)
        <option value="{{ $key }}" @selected(old('type', $discount->type ?? 'percent') === $key)>{{ $label }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Value</label>
    <input type="number" step="0.01" min="0.01" name="value" class="form-control" value="{{ old('value', isset($discount) ? (float) $discount->value : '') }}" required />
    <div class="form-text">Percent: 1–99. Amount: Rupiah off the list price.</div>
  </div>
  @if($flash)
    <div class="col-md-4">
      <label class="form-label small fw-semibold">Quota (units)</label>
      <input type="number" step="1" min="1" name="quota" class="form-control" value="{{ old('quota', $discount->quota ?? '') }}" required />
      <div class="form-text">Units sellable at the sale price.</div>
    </div>
  @endif

  <div class="col-md-6">
    <label class="form-label small fw-semibold">Starts <span class="text-muted fw-normal">(WIB)</span></label>
    <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at', $localTime($discount->starts_at ?? null)) }}" />
    <div class="form-text">Leave empty to start immediately.</div>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Ends <span class="text-muted fw-normal">(WIB)</span></label>
    <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at', $localTime($discount->ends_at ?? null)) }}" @required($flash) />
    <div class="form-text">{{ $flash ? 'Required for a flash sale.' : 'Leave empty for no end date.' }}</div>
  </div>

  @unless($flash)
    <div class="col-md-4">
      <label class="form-label small fw-semibold">Quota (units) <span class="text-muted fw-normal">(optional)</span></label>
      <input type="number" step="1" min="1" name="quota" class="form-control" value="{{ old('quota', $discount->quota ?? '') }}" />
      <div class="form-text">Stops the discount after this many units.</div>
    </div>
  @endunless

  <div class="col-12">
    <div class="form-check form-switch">
      <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $discount->is_active ?? true)) />
      <label class="form-check-label small" for="is_active">Active <span class="text-muted">(switch off to pause without deleting)</span></label>
    </div>
    <div class="form-text mt-2">The lowest price wins when several discounts overlap, and a manual sale price is used if it is lower. Discounts never stack.</div>
  </div>
</div>

<script>
  (function () {
    var root = document.getElementById('discount-form');
    var catalog = JSON.parse(root.dataset.catalog || '[]');
    var scope = document.getElementById('scope');
    var product = document.getElementById('product_id');
    var variant = document.getElementById('product_variant_id');
    var wantedVariant = root.dataset.variant;

    function showScope() {
      var current = scope ? scope.value : 'product';
      root.querySelectorAll('[data-scope-field]').forEach(function (el) {
        var on = el.dataset.scopeField === current;
        el.style.display = on ? '' : 'none';
        el.querySelectorAll('select').forEach(function (s) { s.disabled = !on; });
      });
    }

    function fillVariants() {
      var entry = catalog.find(function (p) { return String(p.id) === product.value; });
      variant.innerHTML = '<option value="">All variants</option>';
      (entry ? entry.variants : []).forEach(function (v) {
        variant.add(new Option(v.label, v.id, false, String(v.id) === String(wantedVariant)));
      });
      wantedVariant = '';
    }

    if (scope) scope.addEventListener('change', showScope);
    product.addEventListener('change', fillVariants);
    showScope();
    fillVariants();
  })();
</script>
