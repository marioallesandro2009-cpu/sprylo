@php
  $slide = $slide ?? null;
@endphp

<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Card size</label>
    <select name="size" class="form-select" required>
      @foreach(\App\Models\HeroSlide::SIZES as $value => $label)
        <option value="{{ $value }}" @selected(old('size', $slide->size ?? 'small') === $value)>{{ $label }}</option>
      @endforeach
    </select>
    <div class="form-text">Large = big featured card (2× width &amp; height). Small = compact grid card. Add as many of each as you like — the grid packs them automatically.</div>
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Color theme</label>
    <select name="theme" class="form-select" required>
      @foreach(\App\Models\HeroSlide::THEMES as $value => $label)
        <option value="{{ $value }}" @selected(old('theme', $slide->theme ?? 'indigo') === $value)>{{ $label }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Sort order</label>
    <input type="number" name="sort_order" min="0" class="form-control" value="{{ old('sort_order', $slide->sort_order ?? 0) }}" />
    <div class="form-text">Lower numbers appear first.</div>
  </div>

  <div class="col-md-6">
    <label class="form-label small fw-semibold">Eyebrow label</label>
    <input type="text" name="eyebrow" class="form-control" value="{{ old('eyebrow', $slide->eyebrow ?? '') }}" required placeholder="e.g. Audio · Featured" />
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">CTA button label</label>
    <input type="text" name="cta_label" class="form-control" value="{{ old('cta_label', $slide->cta_label ?? '') }}" placeholder="Shop Now" />
  </div>

  <div class="col-12">
    <label class="form-label small fw-semibold">Heading</label>
    <input type="text" name="heading" class="form-control" value="{{ old('heading', $slide->heading ?? '') }}" required />
  </div>
  <div class="col-12">
    <label class="form-label small fw-semibold">Description</label>
    <textarea name="description" rows="2" class="form-control">{{ old('description', $slide->description ?? '') }}</textarea>
    <div class="form-text">Only shown on Large cards.</div>
  </div>

  <div class="col-md-6">
    <label class="form-label small fw-semibold">Linked product</label>
    <select name="product_id" class="form-select">
      <option value="">None (links to shop)</option>
      @foreach($products as $product)
        <option value="{{ $product->id }}" @selected(old('product_id', $slide->product_id ?? '') == $product->id)>{{ $product->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Status</label>
    <div class="form-check form-switch mt-2">
      <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $slide->is_active ?? true)) />
      <label class="form-check-label small" for="is_active">Visible on homepage</label>
    </div>
  </div>

  @if($slide?->image)
    <div class="col-12">
      <img src="{{ $slide->image }}" alt="" style="width:70px;height:70px;object-fit:cover;border-radius:8px" />
    </div>
  @endif
  <div class="col-12">
    <label class="form-label small fw-semibold">{{ $slide?->image ? 'Replace image' : 'Product image' }}</label>
    <input type="file" name="image" class="form-control" accept="image/*" {{ $slide?->image ? '' : 'required' }} />
  </div>
</div>
