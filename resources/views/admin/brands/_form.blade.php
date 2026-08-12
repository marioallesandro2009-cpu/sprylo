@php $brand = $brand ?? null; @endphp

<div class="mb-3">
  <label class="form-label small fw-semibold">Name</label>
  <input type="text" name="name" class="form-control" value="{{ old('name', $brand->name ?? '') }}" required />
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Website URL</label>
  <input type="text" name="website_url" class="form-control" value="{{ old('website_url', $brand->website_url ?? '') }}" placeholder="https://…" />
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Sort order</label>
  <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $brand->sort_order ?? 0) }}" style="max-width:120px" />
</div>

@if($brand?->logo)
  <div class="mb-3">
    <label class="form-label small fw-semibold">Current logo</label>
    <div><img src="{{ $brand->logo }}" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:8px" /></div>
  </div>
@endif

<div class="mb-3">
  <label class="form-label small fw-semibold">{{ $brand?->logo ? 'Replace logo' : 'Logo' }}</label>
  <input type="file" name="logo" class="form-control" accept="image/*" />
</div>
