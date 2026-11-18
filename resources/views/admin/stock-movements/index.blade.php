@extends('layouts.admin')

@section('title', 'Riwayat Stok')
@section('page-title', 'Riwayat Stok')

@push('styles')
  <style>
    .stk-filters .form-control, .stk-filters .form-select { font-size: .84rem; }
    .stk-sum { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 14px; }
    .stk-sum .box { flex: 1 1 150px; background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: 12px 16px; }
    .stk-sum .lbl { font-size: .78rem; color: var(--muted); }
    .stk-sum .val { font-family: var(--ff-display); font-size: 1.3rem; font-weight: 700; line-height: 1.3; }
    .stk-sum .plus { color: var(--ok-ink); }
    .stk-sum .minus { color: var(--bad-ink); }
    .stk-quick { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; font-size: .78rem; color: var(--muted); }
    .stk-quick a { padding: 3px 10px; border: 1px solid var(--line); border-radius: 999px; text-decoration: none; color: inherit; font-weight: 600; }
    .stk-quick a:hover { border-color: var(--brand); color: var(--brand); }
    .stk-qty { display: inline-block; min-width: 46px; text-align: center; font-weight: 700; border-radius: 6px; padding: 2px 8px; white-space: nowrap; }
    .stk-qty.plus { color: var(--ok-ink); background: #e8f6ee; }
    .stk-qty.minus { color: var(--bad-ink); background: #fdeaea; }
    .stk-type { display: inline-block; font-size: .72rem; font-weight: 600; border-radius: 999px; padding: 2px 10px; white-space: nowrap; background: #eef0f6; color: #4b5167; }
    .stk-type.t-order { background: #fdf0e3; color: #a65a10; }
    .stk-type.t-return { background: #e8f6ee; color: #1b7a43; }
    .stk-type.t-reactivate { background: #fdf0e3; color: #a65a10; }
    .stk-type.t-manual { background: #ece9fb; color: #4f3fb5; }
    .stk-type.t-initial { background: #e6f0fd; color: #2456a6; }
    .stk-name { color: inherit; text-decoration: none; font-weight: 600; }
    a.stk-name:hover { color: var(--brand); }
    .stk-row:hover td { background: #fafbfe; }
    .stk-empty { padding: 36px 12px; text-align: center; color: var(--muted); }
    .stk-empty strong { display: block; color: inherit; margin-bottom: 4px; }
  </style>
@endpush

@section('content')
  {{-- update 1: halaman riwayat pergerakan stok (filter produk, jenis, tanggal + pagination) --}}
  @php $filtered = collect(request()->only(['product', 'type', 'from', 'to']))->filter()->isNotEmpty(); @endphp

  <p class="text-muted small" style="max-width:680px">
    Setiap kali stok bertambah atau berkurang, satu baris dicatat di sini. Catatan tidak bisa diubah atau dihapus. Waktu dalam WIB.
  </p>

  {{-- update 3: ringkasan catatan, unit masuk, dan unit keluar --}}
  <div class="stk-sum">
    <div class="box"><div class="lbl">Catatan{{ $filtered ? ' (sesuai filter)' : '' }}</div><div class="val">{{ number_format($movements->total(), 0, ',', '.') }}</div></div>
    <div class="box"><div class="lbl">Unit masuk</div><div class="val plus">+{{ number_format($unitsIn, 0, ',', '.') }}</div></div>
    <div class="box"><div class="lbl">Unit keluar</div><div class="val minus">−{{ number_format($unitsOut, 0, ',', '.') }}</div></div>
  </div>

  <form method="GET" action="{{ route('admin.stock-movements.index') }}" class="card-admin stk-filters mb-3">
    <div class="row g-2 align-items-end">
      <div class="col-lg-4 col-md-6">
        <label class="form-label small mb-1" for="f-product">Produk</label>
        <select id="f-product" name="product" class="form-select form-select-sm">
          <option value="">Semua produk</option>
          @foreach($products as $product)
            <option value="{{ $product->id }}" @selected((int) request('product') === $product->id)>{{ $product->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-3 col-md-6">
        <label class="form-label small mb-1" for="f-type">Jenis</label>
        <select id="f-type" name="type" class="form-select form-select-sm">
          <option value="">Semua jenis</option>
          @foreach($types as $key => $label)
            <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2 col-6">
        <label class="form-label small mb-1" for="f-from">Dari tanggal</label>
        <input id="f-from" type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm" />
      </div>
      <div class="col-lg-2 col-6">
        <label class="form-label small mb-1" for="f-to">Sampai tanggal</label>
        <input id="f-to" type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm" />
      </div>
    </div>
    <div class="d-flex flex-wrap gap-3 mt-3 align-items-center">
      <button type="submit" class="btn btn-sm btn-admin-primary">Terapkan filter</button>
      @if($filtered)
        <a href="{{ route('admin.stock-movements.index') }}" class="btn btn-sm btn-outline-secondary">Hapus filter</a>
      @endif
      {{-- update 3: tombol pintas tanggal --}}
      <div class="stk-quick ms-lg-auto">
        <span>Pintas:</span>
        @foreach($shortcuts as $label => [$a, $b])
          <a href="{{ route('admin.stock-movements.index', array_filter(['product' => request('product'), 'type' => request('type'), 'from' => $a, 'to' => $b])) }}">{{ $label }}</a>
        @endforeach
      </div>
    </div>
  </form>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr>
            <th style="width:150px">Waktu</th><th>Produk</th><th style="width:170px">Jenis</th>
            <th class="text-center" style="width:90px">Jumlah</th><th class="text-end" style="width:130px">Stok sebelum / sesudah</th>
            <th>Alasan</th><th style="width:170px">Oleh</th>
          </tr>
        </thead>
        <tbody>
          @forelse($movements as $m)
            @php $when = $m->created_at->copy()->timezone($tz); @endphp
            <tr class="stk-row">
              <td class="small text-nowrap">{{ $when->format('d M Y') }}<div class="text-muted">{{ $when->format('H:i:s') }}</div></td>
              <td class="small">
                @if($m->product)
                  <a href="{{ route('admin.products.edit', $m->product) }}" class="stk-name">{{ $m->product_name }}</a>
                @else
                  <span class="stk-name" title="Produk ini sudah dihapus">{{ $m->product_name }}</span>
                @endif
                @if($m->variant_label)<div class="text-muted">Varian: {{ $m->variant_label }}</div>@endif
              </td>
              <td><span class="stk-type t-{{ $m->type }}">{{ $m->type_label }}</span></td>
              <td class="text-center"><span class="stk-qty {{ $m->quantity >= 0 ? 'plus' : 'minus' }}">{{ $m->quantity > 0 ? '+' : '−' }}{{ abs($m->quantity) }}</span></td>
              <td class="text-end small text-nowrap">{{ $m->stock_before }} → <strong>{{ $m->stock_after }}</strong></td>
              <td class="small">
                @if($m->order_id)
                  <a href="{{ route('admin.orders.show', $m->order_id) }}" class="text-decoration-none">{{ $m->reason }}</a>
                @else
                  {{ $m->reason }}
                @endif
              </td>
              <td class="small">{{ $m->actor_name ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="7">
              {{-- update 3: pesan kosong yang menjelaskan apa yang harus dilakukan --}}
              <div class="stk-empty">
                @if($filtered)
                  <strong>Tidak ada catatan yang cocok.</strong>
                  Coba ubah atau <a href="{{ route('admin.stock-movements.index') }}">hapus filter</a>.
                @else
                  <strong>Belum ada perubahan stok.</strong>
                  Catatan muncul otomatis saat ada pesanan, pembatalan, atau stok diedit di halaman produk.
                @endif
              </div>
            </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($movements->hasPages())
    <div class="mt-3">{{ $movements->links('pagination::bootstrap-5') }}</div>
  @endif
@endsection
