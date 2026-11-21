@extends('layouts.admin')

@section('title', $flash ? 'Flash Sale' : 'Diskon Terjadwal')
@section('page-title', $flash ? 'Flash Sale' : 'Diskon Terjadwal')

{{-- update 4: halaman Flash Sale / Diskon Terjadwal dengan ringkasan, tab status, dan kolom lebih detail --}}
@push('styles')
  <style>
    .fs-sum { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 14px; }
    .fs-sum .box { background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: 12px 16px; }
    .fs-sum .lbl { font-size: .78rem; color: var(--muted); }
    .fs-sum .val { font-family: var(--ff-display); font-size: 1.35rem; font-weight: 700; line-height: 1.3; }
    .fs-sum .hint { font-size: .72rem; color: var(--muted); }
    .fs-sum .live .val { color: var(--ok-ink); }
    .fs-tabs { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
    .fs-tabs a { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border: 1px solid var(--line); border-radius: 999px; font-size: .8rem; font-weight: 600; color: var(--muted); text-decoration: none; background: #fff; }
    .fs-tabs a:hover { border-color: var(--brand); color: var(--brand); }
    .fs-tabs a.on { background: var(--brand); border-color: var(--brand); color: #fff; }
    .fs-tabs .n { font-size: .7rem; background: rgba(0,0,0,.08); border-radius: 999px; padding: 0 7px; }
    .fs-tabs a.on .n { background: rgba(255,255,255,.25); }
    .fs-name { font-weight: 700; }
    .fs-sub { font-size: .75rem; color: var(--muted); }
    .fs-off { display: inline-block; font-weight: 700; font-size: .85rem; padding: 2px 10px; border-radius: 8px; background: #fdecec; color: #c0392b; white-space: nowrap; }
    .fs-when { font-size: .8rem; white-space: nowrap; }
    .fs-left { display: inline-block; margin-top: 3px; font-size: .72rem; font-weight: 600; padding: 1px 8px; border-radius: 999px; background: #eef0f6; color: #4b5167; }
    .fs-left.live { background: #e8f6ee; color: #1b7a43; }
    .fs-left.soon { background: #e6f0fd; color: #2456a6; }
    .fs-qty { min-width: 150px; }
    .fs-qty .top { display: flex; justify-content: space-between; font-size: .8rem; }
    .fs-qty .track { height: 7px; background: var(--line); border-radius: 4px; overflow: hidden; margin: 4px 0 3px; }
    .fs-qty .fill { height: 100%; border-radius: 4px; background: #16a34a; }
    .fs-qty .fill.mid { background: #f97316; }
    .fs-qty .fill.high { background: #dc4a4a; }
    .fs-qty .rest { font-size: .72rem; color: var(--muted); }
    .fs-status { display: inline-flex; align-items: center; gap: 6px; font-size: .78rem; font-weight: 600; white-space: nowrap; }
    .fs-status i { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .fs-empty { padding: 36px 12px; text-align: center; color: var(--muted); }
    .fs-empty strong { display: block; color: inherit; margin-bottom: 4px; }
    .fs-row:hover td { background: #fafbfe; }
    @media (max-width: 991px) { .fs-sum { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  </style>
@endpush

@section('content')
  @php
    $tz = \App\Models\Discount::DISPLAY_TZ;
    // label, warna titik status, kelas lencana
    $meta = [
      'live' => ['Berlangsung', '#16a34a'],
      'scheduled' => ['Terjadwal', '#3b82f6'],
      'sold_out' => ['Habis terjual', '#f97316'],
      'ended' => ['Selesai', '#9ca3af'],
      'inactive' => ['Nonaktif', '#9ca3af'],
    ];
    $dur = fn ($from, $to) => $from->copy()->locale('id')->diffForHumans($to, ['parts' => 2, 'minimumUnit' => 'minute', 'syntax' => \Carbon\Carbon::DIFF_ABSOLUTE]);
  @endphp

  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <p class="text-muted small mb-0" style="max-width:660px">
      @if($flash)
        Penawaran terbatas waktu dengan batas jumlah unit di harga promo. Harga kembali normal otomatis saat waktu atau kuota habis. Waktu dalam WIB.
      @else
        Diskon persen atau Rupiah yang menyala dan mati sendiri sesuai jadwal. Bisa untuk satu produk, kategori, atau brand. Waktu dalam WIB.
      @endif
    </p>
    <a href="{{ route($routePrefix.'.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> {{ $flash ? 'Tambah Flash Sale' : 'Tambah Diskon' }}</a>
  </div>

  {{-- Ringkasan --}}
  <div class="fs-sum">
    <div class="box live"><div class="lbl">Sedang berlangsung</div><div class="val">{{ $stats['live'] }}</div><div class="hint">harga promo aktif sekarang</div></div>
    <div class="box"><div class="lbl">Terjadwal</div><div class="val">{{ $stats['scheduled'] }}</div><div class="hint">akan mulai otomatis</div></div>
    @if($flash)
      <div class="box"><div class="lbl">Total unit terjual</div><div class="val">{{ number_format($stats['sold'], 0, ',', '.') }}</div><div class="hint">di semua flash sale</div></div>
      <div class="box"><div class="lbl">Sisa kuota (live)</div><div class="val">{{ number_format($stats['remaining'], 0, ',', '.') }}</div><div class="hint">unit masih di harga promo</div></div>
    @else
      <div class="box"><div class="lbl">Total aturan</div><div class="val">{{ $total }}</div><div class="hint">semua status</div></div>
      <div class="box"><div class="lbl">Selesai / nonaktif</div><div class="val">{{ ($counts['ended'] ?? 0) + ($counts['inactive'] ?? 0) }}</div><div class="hint">tidak lagi berlaku</div></div>
    @endif
  </div>

  {{-- Tab status --}}
  <div class="fs-tabs" role="group" aria-label="Filter status">
    <a href="{{ route($routePrefix.'.index') }}" class="{{ $status === '' ? 'on' : '' }}">Semua <span class="n">{{ $total }}</span></a>
    @foreach($meta as $key => [$label])
      <a href="{{ route($routePrefix.'.index', ['status' => $key]) }}" class="{{ $status === $key ? 'on' : '' }}">{{ $label }} <span class="n">{{ $counts[$key] ?? 0 }}</span></a>
    @endforeach
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr>
            <th>Nama</th><th>Berlaku untuk</th><th>Diskon</th><th>Jadwal (WIB)</th>
            @if($flash)<th>Terjual / kuota</th>@endif
            <th>Status</th><th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($discounts as $discount)
            @php
              $st = $discount->status;
              $now = now();
              $pct = $discount->quota ? min(100, (int) round($discount->sold / $discount->quota * 100)) : 0;
            @endphp
            <tr class="fs-row">
              <td>
                <div class="fs-name">{{ $discount->name }}</div>
                <div class="fs-sub">{{ \App\Models\Discount::SCOPES[$discount->scope] ?? '' }}</div>
              </td>
              <td class="small">{{ $discount->target_label }}</td>
              <td>
                <span class="fs-off">{{ $discount->type === 'percent' ? '−'.(float) $discount->value.'%' : '−'.rupiah($discount->value) }}</span>
              </td>
              <td class="fs-when">
                {{ $discount->starts_at ? $discount->starts_at->copy()->timezone($tz)->format('d M Y, H:i') : 'Langsung' }}
                <div class="text-muted">sampai {{ $discount->ends_at ? $discount->ends_at->copy()->timezone($tz)->format('d M Y, H:i') : 'tanpa batas' }}</div>
                @if($st === 'live' && $discount->ends_at)
                  <span class="fs-left live">Sisa waktu {{ $dur($now, $discount->ends_at) }}</span>
                @elseif($st === 'scheduled' && $discount->starts_at)
                  <span class="fs-left soon">Mulai {{ $dur($now, $discount->starts_at) }} lagi</span>
                @elseif($st === 'ended' && $discount->ends_at)
                  <span class="fs-left">Berakhir {{ $dur($discount->ends_at, $now) }} lalu</span>
                @endif
              </td>
              @if($flash)
                <td class="fs-qty">
                  <div class="top"><span><strong>{{ $discount->sold }}</strong> / {{ $discount->quota }} unit</span><span class="text-muted">{{ $pct }}%</span></div>
                  <div class="track"><div class="fill {{ $pct >= 90 ? 'high' : ($pct >= 60 ? 'mid' : '') }}" style="width: {{ $pct }}%"></div></div>
                  <div class="rest">{{ $discount->remaining > 0 ? 'Sisa '.$discount->remaining.' unit' : 'Kuota habis' }}</div>
                </td>
              @endif
              <td><span class="fs-status"><i style="background: {{ $meta[$st][1] }}"></i>{{ $meta[$st][0] }}</span></td>
              <td class="text-end text-nowrap">
                <a href="{{ route($routePrefix.'.edit', $discount) }}" class="btn btn-sm btn-outline-secondary" title="Ubah" aria-label="Ubah {{ $discount->name }}"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route($routePrefix.'.destroy', $discount) }}" method="POST" class="d-inline" data-confirm="Hapus {{ strtolower($flash ? 'flash sale' : 'diskon') }} ini?">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" aria-label="Hapus {{ $discount->name }}"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="{{ $flash ? 7 : 6 }}">
              <div class="fs-empty">
                @if($status !== '')
                  <strong>Tidak ada yang berstatus "{{ $meta[$status][0] ?? $status }}".</strong>
                  <a href="{{ route($routePrefix.'.index') }}">Lihat semua</a>
                @else
                  <strong>Belum ada {{ $flash ? 'flash sale' : 'diskon terjadwal' }}.</strong>
                  Klik <em>{{ $flash ? 'Tambah Flash Sale' : 'Tambah Diskon' }}</em> untuk membuat yang pertama.
                @endif
              </div>
            </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
