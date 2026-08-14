@php $promoCode = $promoCode ?? null; @endphp

<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Code</label>
    <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code', $promoCode->code ?? '') }}" required />
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Type</label>
    <select name="type" class="form-select" required>
      <option value="percent" @selected(old('type', $promoCode->type ?? '') === 'percent')>Percent off</option>
      <option value="fixed" @selected(old('type', $promoCode->type ?? '') === 'fixed')>Fixed amount off (Rp)</option>
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Value</label>
    <input type="number" step="1" min="0" name="value" class="form-control" value="{{ old('value', $promoCode->value ?? '') }}" required />
    <div class="form-text">Percent: e.g. 10 for 10%. Fixed: amount in Rupiah.</div>
  </div>

  <div class="col-md-6">
    <label class="form-label small fw-semibold">Minimum subtotal (Rp)</label>
    <input type="number" step="1000" min="0" name="min_subtotal" class="form-control" value="{{ old('min_subtotal', $promoCode->min_subtotal ?? '') }}" />
    <div class="form-text">Leave empty for no minimum.</div>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Expires at</label>
    <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at', $promoCode?->expires_at?->format('Y-m-d')) }}" />
    <div class="form-text">Leave empty for no expiry.</div>
  </div>

  <div class="col-12">
    <div class="form-check form-switch">
      <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $promoCode->is_active ?? true)) />
      <label class="form-check-label small" for="is_active">Active</label>
    </div>
  </div>
</div>
