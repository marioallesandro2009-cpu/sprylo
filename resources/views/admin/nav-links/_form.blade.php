@php $link = $link ?? null; @endphp

<div class="mb-3">
  <label class="form-label small fw-semibold">Label</label>
  <input type="text" name="label" class="form-control" value="{{ old('label', $link->label ?? '') }}" required />
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">URL</label>
  <input type="text" name="url" class="form-control" value="{{ old('url', $link->url ?? '/') }}" required />
  <div class="form-text">Use a path such as <code>/shop</code> for pages on this site, or a full address (https://…) for external links.</div>
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Shown in</label>
  <select name="placement" class="form-select" required>
    @foreach(\App\Models\NavLink::PLACEMENTS as $key => $label)
      <option value="{{ $key }}" @selected(old('placement', $link->placement ?? 'both') === $key)>{{ $label }}</option>
    @endforeach
  </select>
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Sort order</label>
  <input type="number" name="sort_order" min="0" class="form-control" value="{{ old('sort_order', $link->sort_order ?? 0) }}" style="max-width:120px" />
</div>

<div class="form-check form-switch mb-3">
  <input type="checkbox" name="is_active" value="1" class="form-check-input" id="nl-active" @checked(old('is_active', $link->is_active ?? true)) />
  <label class="form-check-label small fw-semibold" for="nl-active">Visible on the storefront</label>
</div>
