@php
  $discount = $discount ?? null;
  $tz = \App\Models\Discount::DISPLAY_TZ;
  $scope = old('scope', $discount->scope ?? 'product');
  $localTime = fn ($value) => $value ? $value->copy()->timezone($tz)->format('Y-m-d\TH:i') : '';
  // update 4: harga ikut dikirim ke JS untuk pratinjau harga setelah diskon
  $catalog = $products->map(fn ($p) => ['id' => $p->id, 'price' => (float) $p->price, 'variants' => $p->variants->map(fn ($v) => ['id' => $v->id, 'label' => $v->label, 'price' => (float) $v->price])->all()])->all();
  $scopeLabels = ['product' => 'Produk / varian', 'category' => 'Kategori', 'brand' => 'Brand'];
  $typeLabels = ['percent' => 'Persen (%)', 'amount' => 'Potongan Rupiah (Rp)'];
@endphp

{{-- update 4: form dibagi per bagian (info, target, diskon, jadwal) dengan pratinjau harga dan durasi --}}
<style>
  .df-sec { font-size: .78rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: var(--muted); margin: 8px 0 -2px; padding-top: 12px; border-top: 1px solid var(--line); }
  .df-sec:first-child { border-top: 0; padding-top: 0; margin-top: 0; }
  .df-preview { background: #f4f6fb; border: 1px dashed var(--brand-line, #c7cdf5); border-radius: 12px; padding: 12px 16px; font-size: .88rem; }
  .df-preview .old { text-decoration: line-through; color: var(--muted); }
  .df-preview .new { font-family: var(--ff-display); font-weight: 700; font-size: 1.15rem; color: #c0392b; }
  .df-preview .save { display: inline-block; margin-left: 6px; font-size: .75rem; font-weight: 700; padding: 1px 8px; border-radius: 999px; background: #e8f6ee; color: #1b7a43; }
  .df-preview .muted { color: var(--muted); font-size: .8rem; }
</style>

<div class="row g-3" id="discount-form" data-catalog="{{ json_encode($catalog) }}" data-variant="{{ old('product_variant_id', $discount->product_variant_id ?? '') }}">
  <div class="col-12 df-sec">1. Informasi</div>
  <div class="col-12">
    <label class="form-label small fw-semibold" for="df-name">Nama</label>
    <input id="df-name" type="text" name="name" class="form-control" value="{{ old('name', $discount->name ?? '') }}" placeholder="{{ $flash ? 'mis. Flash Sale 12.12 — iPad' : 'mis. Diskon Akhir Pekan Smartphone' }}" required />
    <div class="form-text">Nama ini hanya terlihat di admin.</div>
  </div>

  <div class="col-12 df-sec">2. Berlaku untuk</div>
  @if($flash)
    <input type="hidden" name="scope" value="product" />
  @else
    <div class="col-md-4">
      <label class="form-label small fw-semibold" for="scope">Berlaku untuk</label>
      <select name="scope" class="form-select" id="scope" required>
        @foreach(\App\Models\Discount::SCOPES as $key => $label)
          <option value="{{ $key }}" @selected($scope === $key)>{{ $scopeLabels[$key] ?? $label }}</option>
        @endforeach
      </select>
    </div>
  @endif

  <div class="col-md-{{ $flash ? 6 : 4 }}" data-scope-field="product">
    <label class="form-label small fw-semibold" for="product_id">Produk</label>
    <select name="product_id" class="form-select" id="product_id">
      <option value="">— pilih produk —</option>
      @foreach($products as $product)
        <option value="{{ $product->id }}" @selected((int) old('product_id', $discount->product_id ?? 0) === $product->id)>{{ $product->name }}</option>
      @endforeach
    </select>
    @if($flash)<div class="form-text">Flash sale selalu untuk satu produk, karena kuotanya per aturan.</div>@endif
  </div>
  <div class="col-md-{{ $flash ? 6 : 4 }}" data-scope-field="product">
    <label class="form-label small fw-semibold" for="product_variant_id">Varian <span class="text-muted fw-normal">(opsional)</span></label>
    <select name="product_variant_id" class="form-select" id="product_variant_id"><option value="">Semua varian</option></select>
  </div>
  <div class="col-md-4" data-scope-field="category">
    <label class="form-label small fw-semibold">Kategori</label>
    <select name="category_id" class="form-select">
      <option value="">— pilih kategori —</option>
      @foreach($categories as $category)
        <option value="{{ $category->id }}" @selected((int) old('category_id', $discount->category_id ?? 0) === $category->id)>{{ $category->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4" data-scope-field="brand">
    <label class="form-label small fw-semibold">Brand</label>
    <select name="brand_id" class="form-select">
      <option value="">— pilih brand —</option>
      @foreach($brands as $brand)
        <option value="{{ $brand->id }}" @selected((int) old('brand_id', $discount->brand_id ?? 0) === $brand->id)>{{ $brand->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-12 df-sec">3. Diskon{{ $flash ? ' dan kuota' : '' }}</div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold" for="type">Jenis diskon</label>
    <select name="type" id="type" class="form-select" required>
      @foreach(\App\Models\Discount::TYPES as $key => $label)
        <option value="{{ $key }}" @selected(old('type', $discount->type ?? 'percent') === $key)>{{ $typeLabels[$key] ?? $label }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold" for="value">Nilai</label>
    <input type="number" step="0.01" min="0.01" name="value" id="value" class="form-control" value="{{ old('value', isset($discount) ? (float) $discount->value : '') }}" required />
    <div class="form-text">Persen: 1–99. Rupiah: potongan dari harga normal.</div>
  </div>
  @if($flash)
    <div class="col-md-4">
      <label class="form-label small fw-semibold" for="quota">Kuota (unit)</label>
      <input type="number" step="1" min="1" name="quota" id="quota" class="form-control" value="{{ old('quota', $discount->quota ?? '') }}" required />
      <div class="form-text">Jumlah unit yang boleh dijual di harga promo.</div>
    </div>
  @endif

  <div class="col-12" id="df-preview-wrap" hidden>
    <div class="df-preview" id="df-preview" aria-live="polite"></div>
  </div>

  <div class="col-12 df-sec">4. Jadwal <span class="text-muted fw-normal text-lowercase">(WIB)</span></div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold" for="starts_at">Mulai</label>
    <input type="datetime-local" name="starts_at" id="starts_at" class="form-control" value="{{ old('starts_at', $localTime($discount->starts_at ?? null)) }}" />
    <div class="form-text">Kosongkan untuk mulai langsung.</div>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold" for="ends_at">Berakhir</label>
    <input type="datetime-local" name="ends_at" id="ends_at" class="form-control" value="{{ old('ends_at', $localTime($discount->ends_at ?? null)) }}" @required($flash) />
    <div class="form-text">{{ $flash ? 'Wajib diisi untuk flash sale.' : 'Kosongkan untuk tanpa batas.' }} <span id="df-duration" class="fw-semibold"></span></div>
  </div>

  @unless($flash)
    <div class="col-md-4">
      <label class="form-label small fw-semibold" for="quota">Kuota (unit) <span class="text-muted fw-normal">(opsional)</span></label>
      <input type="number" step="1" min="1" name="quota" id="quota" class="form-control" value="{{ old('quota', $discount->quota ?? '') }}" />
      <div class="form-text">Diskon berhenti setelah sejumlah unit ini terjual.</div>
    </div>
  @endunless

  <div class="col-12">
    <div class="form-check form-switch">
      <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $discount->is_active ?? true)) />
      <label class="form-check-label small" for="is_active">Aktif <span class="text-muted">(matikan untuk menjeda tanpa menghapus)</span></label>
    </div>
    <div class="form-text mt-2">Kalau beberapa diskon bertumpuk, harga terendah yang dipakai. Harga coret manual dipakai bila lebih rendah. Diskon tidak pernah dijumlahkan.</div>
  </div>
</div>

<script>
  (function () {
    var root = document.getElementById('discount-form');
    var catalog = JSON.parse(root.dataset.catalog || '[]');
    var scope = document.getElementById('scope');
    var product = document.getElementById('product_id');
    var variant = document.getElementById('product_variant_id');
    var type = document.getElementById('type');
    var value = document.getElementById('value');
    var startsAt = document.getElementById('starts_at');
    var endsAt = document.getElementById('ends_at');
    var wrap = document.getElementById('df-preview-wrap');
    var preview = document.getElementById('df-preview');
    var duration = document.getElementById('df-duration');
    var wantedVariant = root.dataset.variant;
    var rp = function (n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); };

    function showScope() {
      var current = scope ? scope.value : 'product';
      root.querySelectorAll('[data-scope-field]').forEach(function (el) {
        var on = el.dataset.scopeField === current;
        el.style.display = on ? '' : 'none';
        el.querySelectorAll('select').forEach(function (s) { s.disabled = !on; });
      });
      showPreview();
    }

    function fillVariants() {
      var entry = catalog.find(function (p) { return String(p.id) === product.value; });
      variant.innerHTML = '<option value="">Semua varian</option>';
      (entry ? entry.variants : []).forEach(function (v) {
        variant.add(new Option(v.label, v.id, false, String(v.id) === String(wantedVariant)));
      });
      wantedVariant = '';
      showPreview();
    }

    // Pratinjau harga setelah diskon, hanya untuk satu produk (atau satu varian).
    function showPreview() {
      var current = scope ? scope.value : 'product';
      var entry = catalog.find(function (p) { return String(p.id) === product.value; });
      var amount = parseFloat(value.value);
      if (current !== 'product' || !entry || !(amount > 0)) { wrap.hidden = true; return; }

      var chosen = entry.variants.find(function (v) { return String(v.id) === variant.value; });
      var price = chosen ? chosen.price : entry.price;
      var after = type.value === 'percent' ? price * (1 - amount / 100) : price - amount;
      after = Math.max(Math.round(after), 0);

      var note = chosen || !entry.variants.length ? '' : '<div class="muted">Produk punya varian: contoh dihitung dari harga dasar produk.</div>';
      preview.innerHTML = 'Pratinjau harga: <span class="old">' + rp(price) + '</span> → <span class="new">' + rp(after) + '</span>'
        + '<span class="save">hemat ' + rp(price - after) + '</span>' + note;
      wrap.hidden = false;
    }

    function showDuration() {
      duration.textContent = '';
      var a = startsAt.value ? new Date(startsAt.value) : new Date();
      var b = endsAt.value ? new Date(endsAt.value) : null;
      if (!b || isNaN(b) || b <= a) return;
      var minutes = Math.round((b - a) / 60000);
      var d = Math.floor(minutes / 1440), h = Math.floor((minutes % 1440) / 60), m = minutes % 60;
      duration.textContent = '· Durasi ' + [d ? d + ' hari' : '', h ? h + ' jam' : '', !d && m ? m + ' menit' : ''].filter(Boolean).join(' ');
    }

    if (scope) scope.addEventListener('change', showScope);
    product.addEventListener('change', fillVariants);
    [variant, type, value].forEach(function (el) { el.addEventListener('input', showPreview); el.addEventListener('change', showPreview); });
    [startsAt, endsAt].forEach(function (el) { el.addEventListener('input', showDuration); });
    showScope();
    fillVariants();
    showDuration();
  })();
</script>
