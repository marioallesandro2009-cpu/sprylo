@php $category = $category ?? null; @endphp

<div class="mb-3">
  <label class="form-label small fw-semibold">Name</label>
  <input type="text" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required />
</div>

@if($category?->image)
  <div class="mb-3">
    <label class="form-label small fw-semibold">Current image</label>
    <div><img src="{{ $category->image }}" alt="" style="width:80px;height:80px;object-fit:cover;border-radius:8px" /></div>
  </div>
@endif

<div class="mb-3">
  <label class="form-label small fw-semibold">{{ $category?->image ? 'Replace image' : 'Image' }}</label>
  <input type="file" name="image" class="form-control" accept="image/*" />
</div>
