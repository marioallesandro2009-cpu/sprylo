@php $product = $product ?? null; @endphp

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
  </div>
  <div class="col-md-3">
    <label class="form-label small fw-semibold">Sale price (Rp)</label>
    <input type="number" step="1000" min="0" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price ?? '') }}" />
    <div class="form-text">Leave empty if not on sale.</div>
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
            <form action="{{ route('admin.products.images.destroy', [$product, $image]) }}" method="POST" onsubmit="return confirm('Remove this image?');"
                  style="position:absolute; top:-8px; right:-8px">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger rounded-circle" style="width:24px;height:24px;padding:0;line-height:1;font-size:.7rem">✕</button>
            </form>
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
