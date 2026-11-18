@php $rule = $rule ?? null; $trigger = old('trigger', $rule->trigger ?? 'first_order'); @endphp

<div class="row g-3" id="vr-form">
  <div class="col-12">
    <label class="form-label small fw-semibold">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $rule->name ?? '') }}" placeholder="e.g. Welcome voucher" required />
  </div>

  <div class="col-md-8">
    <label class="form-label small fw-semibold">Give a voucher…</label>
    <select name="trigger" class="form-select" id="vr-trigger" required>
      @foreach(\App\Models\VoucherRule::TRIGGERS as $key => $label)
        <option value="{{ $key }}" @selected($trigger === $key)>{{ $label }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4" id="vr-every" @if($trigger !== 'order_milestone') hidden @endif>
    <label class="form-label small fw-semibold">Every N orders</label>
    <input type="number" min="2" max="1000" name="milestone_every" class="form-control" value="{{ old('milestone_every', $rule->milestone_every ?? 5) }}" />
    <div class="form-text">e.g. 5 = on the 5th, 10th, 15th order.</div>
  </div>

  <div class="col-md-4">
    <label class="form-label small fw-semibold">Voucher type</label>
    <select name="type" class="form-select" required>
      <option value="percent" @selected(old('type', $rule->type ?? 'percent') === 'percent')>Percent off</option>
      <option value="fixed" @selected(old('type', $rule->type ?? '') === 'fixed')>Fixed amount off (Rp)</option>
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Value</label>
    <input type="number" step="0.01" min="0.01" name="value" class="form-control" value="{{ old('value', isset($rule) ? (float) $rule->value : '') }}" required />
    <div class="form-text">Percent: 1–100. Fixed: Rupiah.</div>
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Minimum order (Rp)</label>
    <input type="number" step="1000" min="0" name="min_subtotal" class="form-control" value="{{ old('min_subtotal', isset($rule->min_subtotal) ? (int) $rule->min_subtotal : '') }}" />
    <div class="form-text">Leave empty for none.</div>
  </div>

  <div class="col-md-4">
    <label class="form-label small fw-semibold">Valid for (days)</label>
    <input type="number" min="1" max="365" name="valid_days" class="form-control" value="{{ old('valid_days', $rule->valid_days ?? 30) }}" required />
    <div class="form-text">Counted from the day it is issued.</div>
  </div>

  <div class="col-12">
    <div class="form-check form-switch">
      <input type="checkbox" name="is_active" value="1" class="form-check-input" id="vr-active" @checked(old('is_active', $rule->is_active ?? true)) />
      <label class="form-check-label small" for="vr-active">Active <span class="text-muted">(switch off to stop issuing without deleting)</span></label>
    </div>
    <div class="form-text mt-2">Each voucher is single-use with a random code (e.g. WELCOME-K3F9QX), emailed to the customer. A customer gets the “first order” voucher once.</div>
  </div>
</div>

<script>
  (function () {
    var trigger = document.getElementById('vr-trigger'), every = document.getElementById('vr-every');
    trigger.addEventListener('change', function () { every.hidden = trigger.value !== 'order_milestone'; });
  })();
</script>
