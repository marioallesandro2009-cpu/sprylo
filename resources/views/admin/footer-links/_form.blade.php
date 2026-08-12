@php $link = $link ?? null; @endphp

<div class="mb-3">
  <label class="form-label small fw-semibold">Column</label>
  <select name="group" class="form-select" required>
    @foreach(\App\Models\FooterLink::GROUPS as $key => $label)
      <option value="{{ $key }}" @selected(old('group', $link->group ?? '') === $key)>{{ $label }}</option>
    @endforeach
  </select>
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Label</label>
  <input type="text" name="label" class="form-control" value="{{ old('label', $link->label ?? '') }}" required />
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">URL</label>
  <input type="text" name="url" class="form-control" value="{{ old('url', $link->url ?? '#') }}" required />
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Sort order</label>
  <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $link->sort_order ?? 0) }}" style="max-width:120px" />
</div>
