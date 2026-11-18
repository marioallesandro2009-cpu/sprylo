@php
  $product = $product ?? null;
  $specRows = old('specs') ?? ($product->specs ?? []);
  $variantRows = old('variants') ?? ($product->variants ?? collect())->map(fn ($v) => [
      'id' => $v->id,
      'label' => $v->label,
      'sku' => $v->sku,
      'price' => (string) $v->price,
      'sale_price' => $v->getRawOriginal('sale_price') !== null ? (string) $v->getRawOriginal('sale_price') : '',
      'has_warranty' => $v->has_warranty,
      'stock' => $v->stock,
      'weight' => $v->weight,
  ])->all();
@endphp

<div class="row g-3">
  <div class="col-md-8">
    <label class="form-label small fw-semibold">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required />
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Category</label>
    <select name="category_id" class="form-select" required>
      <option value="">Select…</option>
      @foreach($categories as $category)
        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Brand</label>
    <select name="brand_id" class="form-select">
      <option value="">None</option>
      @foreach($brands as $brand)
        <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>{{ $brand->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-12">
    <label class="form-label small fw-semibold">Description</label>
    <textarea name="description" rows="4" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
  </div>

  <div class="col-md-3">
    <label class="form-label small fw-semibold">Price (Rp)</label>
    <input type="number" step="1000" min="0" name="price" class="form-control" value="{{ old('price', $product->price ?? '') }}" required />
    <div class="form-text">Ignored (auto-computed) once you add variants below.</div>
  </div>
  <div class="col-md-3">
    <label class="form-label small fw-semibold">Sale price (Rp)</label>
    <input type="number" step="1000" min="0" name="sale_price" class="form-control" value="{{ old('sale_price', isset($product) ? $product->getRawOriginal('sale_price') : '') }}" />
    <div class="form-text">Leave empty if not on sale. Scheduled discounts and flash sales are set under Kupon.</div>
  </div>
  <div class="col-md-3">
    <label class="form-label small fw-semibold">Stock</label>
    <input type="number" min="0" name="stock" class="form-control" value="{{ old('stock', $product->stock ?? 0) }}" required />
  </div>
  <div class="col-md-3">
    <label class="form-label small fw-semibold">Weight (gram)</label>
    <input type="number" min="0" name="weight" class="form-control" value="{{ old('weight', $product->weight ?? 0) }}" required />
    <div class="form-text">Shown to customers on the product page.</div>
  </div>

  <div class="col-12">
    <hr class="my-1" />
    <div class="d-flex justify-content-between align-items-center mb-2">
      <label class="form-label small fw-semibold mb-0">Specifications</label>
      <button type="button" id="spec-add" class="btn btn-sm btn-outline-secondary">+ Add spec</button>
    </div>
    <div class="form-text mb-2">Optional. For gadgets, list the specs shoppers care about — e.g. "RAM" / "8GB", "Storage" / "256GB", "Screen" / "6.7 inch". Shown on the product page above the auto-generated details.</div>

    <div id="spec-rows" class="d-flex flex-column gap-2 mb-3">
      @foreach($specRows as $i => $spec)
        <div class="row g-2 align-items-end spec-row">
          <div class="col-md-4">
            <label class="form-label small">Label</label>
            <input type="text" name="specs[{{ $i }}][label]" class="form-control form-control-sm" value="{{ $spec['label'] ?? '' }}" placeholder="e.g. RAM" />
          </div>
          <div class="col-md-6">
            <label class="form-label small">Value</label>
            <input type="text" name="specs[{{ $i }}][value]" class="form-control form-control-sm" value="{{ $spec['value'] ?? '' }}" placeholder="e.g. 8GB" />
          </div>
          <div class="col-md-2 d-grid">
            <button type="button" class="btn btn-sm btn-outline-danger spec-remove" aria-label="Remove spec">✕</button>
          </div>
        </div>
      @endforeach
    </div>

    <template id="spec-row-template">
      <div class="row g-2 align-items-end spec-row">
        <div class="col-md-4">
          <label class="form-label small">Label</label>
          <input type="text" name="specs[__INDEX__][label]" class="form-control form-control-sm" placeholder="e.g. RAM" />
        </div>
        <div class="col-md-6">
          <label class="form-label small">Value</label>
          <input type="text" name="specs[__INDEX__][value]" class="form-control form-control-sm" placeholder="e.g. 8GB" />
        </div>
        <div class="col-md-2 d-grid">
          <button type="button" class="btn btn-sm btn-outline-danger spec-remove" aria-label="Remove spec">✕</button>
        </div>
      </div>
    </template>
  </div>

  <div class="col-12">
    <hr class="my-1" />
    <div class="d-flex justify-content-between align-items-center mb-2">
      <label class="form-label small fw-semibold mb-0">Variants (weight / size / packaging)</label>
      <button type="button" id="variant-add" class="btn btn-sm btn-outline-secondary">+ Add variant</button>
    </div>
    <div class="form-text mb-2">Optional. Each variant has its own price and stock — e.g. "250g", "1kg", "Kaleng". When variants exist, the base price/stock/weight above are recalculated from them automatically. Tick "Incl. warranty" for a variant whose price already bundles the travel warranty (e.g. a pricier variant sold together with insurance) — items without it aren't eligible for a return if the courier is at fault.</div>

    <div id="variant-rows" class="d-flex flex-column gap-2">
      @foreach($variantRows as $i => $variant)
        <div class="row g-2 align-items-end variant-row">
          <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant['id'] ?? '' }}" />
          <div class="col-md-2">
            <label class="form-label small">Label</label>
            <input type="text" name="variants[{{ $i }}][label]" class="form-control form-control-sm" value="{{ $variant['label'] }}" placeholder="e.g. 250g" />
          </div>
          <div class="col-md-2">
            <label class="form-label small">SKU</label>
            <input type="text" name="variants[{{ $i }}][sku]" class="form-control form-control-sm" value="{{ $variant['sku'] }}" />
          </div>
          <div class="col-md-2">
            <label class="form-label small">Price (Rp)</label>
            <input type="number" step="1000" min="0" name="variants[{{ $i }}][price]" class="form-control form-control-sm" value="{{ $variant['price'] }}" />
          </div>
          <div class="col-md-2">
            <label class="form-label small">Sale price</label>
            <input type="number" step="1000" min="0" name="variants[{{ $i }}][sale_price]" class="form-control form-control-sm" value="{{ $variant['sale_price'] }}" />
          </div>
          <div class="col-md-1 form-check mb-1">
            <input type="checkbox" name="variants[{{ $i }}][has_warranty]" value="1" class="form-check-input" id="variant-warranty-{{ $i }}" @checked($variant['has_warranty'] ?? false) />
            <label class="form-check-label small" for="variant-warranty-{{ $i }}">Incl. warranty</label>
          </div>
          <div class="col-md-1">
            <label class="form-label small">Stock</label>
            <input type="number" min="0" name="variants[{{ $i }}][stock]" class="form-control form-control-sm" value="{{ $variant['stock'] }}" />
          </div>
          <div class="col-md-1">
            <label class="form-label small">Weight (g)</label>
            <input type="number" min="0" name="variants[{{ $i }}][weight]" class="form-control form-control-sm" value="{{ $variant['weight'] }}" />
          </div>
          <div class="col-md-1 d-grid">
            <button type="button" class="btn btn-sm btn-outline-danger variant-remove" aria-label="Remove variant">✕</button>
          </div>
        </div>
      @endforeach
    </div>

    <template id="variant-row-template">
      <div class="row g-2 align-items-end variant-row">
        <input type="hidden" name="variants[__INDEX__][id]" value="" />
        <div class="col-md-2">
          <label class="form-label small">Label</label>
          <input type="text" name="variants[__INDEX__][label]" class="form-control form-control-sm" placeholder="e.g. 250g" />
        </div>
        <div class="col-md-2">
          <label class="form-label small">SKU</label>
          <input type="text" name="variants[__INDEX__][sku]" class="form-control form-control-sm" />
        </div>
        <div class="col-md-2">
          <label class="form-label small">Price (Rp)</label>
          <input type="number" step="1000" min="0" name="variants[__INDEX__][price]" class="form-control form-control-sm" />
        </div>
        <div class="col-md-2">
          <label class="form-label small">Sale price</label>
          <input type="number" step="1000" min="0" name="variants[__INDEX__][sale_price]" class="form-control form-control-sm" />
        </div>
        <div class="col-md-1 form-check mb-1">
          <input type="checkbox" name="variants[__INDEX__][has_warranty]" value="1" class="form-check-input" id="variant-warranty-__INDEX__" />
          <label class="form-check-label small" for="variant-warranty-__INDEX__">Incl. warranty</label>
        </div>
        <div class="col-md-1">
          <label class="form-label small">Stock</label>
          <input type="number" min="0" name="variants[__INDEX__][stock]" class="form-control form-control-sm" value="0" />
        </div>
        <div class="col-md-1">
          <label class="form-label small">Weight (g)</label>
          <input type="number" min="0" name="variants[__INDEX__][weight]" class="form-control form-control-sm" value="0" />
        </div>
        <div class="col-md-1 d-grid">
          <button type="button" class="btn btn-sm btn-outline-danger variant-remove" aria-label="Remove variant">✕</button>
        </div>
      </div>
    </template>
  </div>
  <div class="col-md-3">
    <label class="form-label small fw-semibold">Status</label>
    <div class="form-check form-switch mt-2">
      <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $product->is_active ?? true)) />
      <label class="form-check-label small" for="is_active">Active</label>
    </div>
  </div>

  @if($product)
    <div class="col-12">
      <div class="alert alert-light border d-flex justify-content-between align-items-center py-2 px-3 mb-0">
        <span class="small text-muted">
          Rating: <strong class="text-dark">{{ $product->rating_avg }} / 5</strong> from <strong class="text-dark">{{ $product->reviews_count }}</strong> review(s) — computed automatically from customer reviews.
        </span>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-outline-secondary">Manage reviews</a>
      </div>
    </div>
  @endif

  @if($product?->images->isNotEmpty())
    <div class="col-12">
      <label class="form-label small fw-semibold">Current images</label>
      <div class="d-flex gap-2 flex-wrap">
        @foreach($product->images as $image)
          <div class="position-relative">
            <img src="{{ $image->image }}" alt="" style="width:76px;height:76px;object-fit:cover;border-radius:8px" />
            {{-- The delete form itself lives outside this form (see edit.blade.php): forms can't be nested, and a nested one made "remove image" delete the whole product. --}}
            <button type="submit" form="delete-image-{{ $image->id }}" class="btn btn-sm btn-danger rounded-circle" aria-label="Remove this image"
                    style="position:absolute; top:-8px; right:-8px; width:24px;height:24px;padding:0;line-height:1;font-size:.7rem">✕</button>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <div class="col-12">
    <label class="form-label small fw-semibold">{{ $product ? 'Add more images' : 'Images' }}</label>
    <input type="file" name="images[]" class="form-control" accept="image/*" multiple />
  </div>
</div>

@push('scripts')
  <script>
    (function () {
      const rows = document.getElementById('variant-rows');
      const template = document.getElementById('variant-row-template');
      const addBtn = document.getElementById('variant-add');
      if (!rows || !template || !addBtn) return;

      let index = rows.querySelectorAll('.variant-row').length;

      addBtn.addEventListener('click', () => {
        const html = template.innerHTML.replaceAll('__INDEX__', index++);
        rows.insertAdjacentHTML('beforeend', html);
      });

      rows.addEventListener('click', (e) => {
        const btn = e.target.closest('.variant-remove');
        if (!btn) return;
        btn.closest('.variant-row').remove();
      });
    })();

    (function () {
      const rows = document.getElementById('spec-rows');
      const template = document.getElementById('spec-row-template');
      const addBtn = document.getElementById('spec-add');
      if (!rows || !template || !addBtn) return;

      let index = rows.querySelectorAll('.spec-row').length;

      addBtn.addEventListener('click', () => {
        const html = template.innerHTML.replaceAll('__INDEX__', index++);
        rows.insertAdjacentHTML('beforeend', html);
      });

      rows.addEventListener('click', (e) => {
        const btn = e.target.closest('.spec-remove');
        if (!btn) return;
        btn.closest('.spec-row').remove();
      });
    })();
  </script>
@endpush
