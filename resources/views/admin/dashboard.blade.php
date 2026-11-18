@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
  <style>
    .dash-card { background: #fff; border: 1px solid var(--line); border-radius: 12px; margin-bottom: 16px; }
    .dash-grid-2 { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 16px; }
    .dash-grid-2.single { grid-template-columns: minmax(0, 1fr); }
    .dash-date { color: var(--muted); font-size: .85rem; margin: 2px 0 0; }

    .todo { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 16px; }
    .todo a { display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; border-radius: 8px; background: var(--warn-soft); color: var(--warn-ink); font-size: .85rem; font-weight: 600; text-decoration: none; }
    .todo a:hover { color: var(--warn-ink); text-decoration: underline; }
    .todo a .chev { font-size: .65rem; opacity: .7; }
    .todo a:hover { box-shadow: 0 0 0 2px var(--warn-soft), 0 2px 8px rgba(0,0,0,.08); text-decoration: none; }
    .range-tabs a { min-height: 30px; display: inline-flex; align-items: center; }
    .stat-link { color: inherit; text-decoration: none; display: block; }
    .stat-link:hover .val { color: var(--brand); }
    .hbar-row:hover .hbar-track { background: #dfe3ee; }
    .todo-clear { color: var(--muted); font-size: .85rem; margin: 0 0 16px; }

    .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .stat { padding: 18px 20px; border-left: 1px solid var(--line); }
    .stat:first-child { border-left: 0; }
    .stat .lbl { font-size: .8rem; color: var(--muted); }
    .stat .val { font-family: var(--ff-display); font-size: 1.4rem; font-weight: 700; line-height: 1.3; }
    .stat .sub { font-size: .75rem; color: var(--muted); }

    .panel-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 16px 20px 0; }
    .panel-head h2 { font-size: .95rem; font-weight: 700; margin: 0; }
    .panel-head .meta { font-size: .85rem; color: var(--muted); }
    .panel-body { padding: 14px 20px 18px; }

    .bars { display: flex; align-items: flex-end; gap: 14px; height: 225px; padding-top: 24px; border-bottom: 1px solid var(--line); }
    .bar-col { flex: 1; height: 100%; display: flex; align-items: flex-end; justify-content: center; }
    .bar { position: relative; width: min(100%, 40px); background: var(--brand-line); border-radius: 4px 4px 0 0; }
    .bar.today { background: var(--brand); }
    .bar.zero { height: 3px !important; background: var(--line); border-radius: 2px; }
    .bar .v { position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 4px; font-size: .68rem; font-weight: 600; white-space: nowrap; }
    .bar-labels { display: flex; gap: 14px; padding-top: 8px; }
    .bar-labels span { flex: 1; text-align: center; font-size: .75rem; color: var(--muted); }

    .range-tabs { display: inline-flex; border: 1px solid var(--line); border-radius: 8px; overflow: hidden; }
    .range-tabs a { padding: 4px 12px; font-size: .78rem; font-weight: 600; color: var(--muted); text-decoration: none; border-left: 1px solid var(--line); }
    .range-tabs a:first-child { border-left: 0; }
    .range-tabs a:hover { color: var(--brand); }
    .range-tabs a.on { background: var(--brand); color: #fff; }
    .meta-line { font-size: .85rem; color: var(--muted); margin: 0 0 4px; }
    .meta-line strong { color: inherit; font-family: var(--ff-display); }
    .bars.dense, .bar-labels.dense { gap: 3px; }
    .bars.dense .bar { width: 100%; border-radius: 2px 2px 0 0; }
    .bar-labels.dense span { font-size: .68rem; overflow: visible; white-space: nowrap; }

    .hbar-row { padding: 7px 0; font-size: .85rem; }
    .hbar-top { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 4px; }
    .hbar-top a { color: inherit; text-decoration: none; }
    .hbar-top a:hover { color: var(--brand); }
    .hbar-top .n { font-weight: 600; white-space: nowrap; }
    .hbar-top small { font-weight: 400; color: var(--muted); }
    .hbar-track { height: 8px; background: var(--line); border-radius: 4px; overflow: hidden; }
    .hbar-fill { height: 100%; border-radius: 4px; }

    .list-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 8px 0; border-top: 1px solid var(--line); font-size: .85rem; }
    .list-row:first-child { border-top: 0; }
    .list-row .n { font-weight: 600; white-space: nowrap; }
    .list-row a { color: inherit; text-decoration: none; }
    .list-row a:hover { color: var(--brand); }
    .out { color: var(--bad-ink); }

    .mini-table { width: 100%; font-size: .85rem; }
    .mini-table th { font-size: .78rem; color: var(--muted); font-weight: 600; padding: 0 14px 8px 0; white-space: nowrap; }
    .mini-table td { padding: 9px 14px 9px 0; border-top: 1px solid var(--line); vertical-align: middle; }
    .mini-table td:nth-child(3) { white-space: nowrap; }
    .empty-note { color: var(--muted); font-size: .85rem; padding: 8px 0; margin: 0; }

    @media (max-width: 1199px) { .dash-grid-2 { grid-template-columns: minmax(0, 1fr); gap: 0; } }
    @media (max-width: 767px) {
      .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .stat { padding: 14px 16px; }
      .stat .val { font-size: 1.15rem; white-space: nowrap; }
      .stat:nth-child(odd) { border-left: 0; }
      .stat:nth-child(n+3) { border-top: 1px solid var(--line); }
      .bars, .bar-labels { gap: 6px; }
    }
  </style>
@endpush

@section('custom-header')
  <div class="page-head">
    <div>
      <h1>Dashboard</h1>
      <p class="dash-date">{{ $today }}</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn-brand"><i class="fa-solid fa-plus"></i> Tambah Produk</a>
  </div>
@endsection

@section('content')

  {{-- update 3: chip "perlu dikerjakan" dengan ikon dan link langsung --}}
  {{-- Perlu dikerjakan: hanya muncul kalau memang ada yang harus dikerjakan --}}
  @if($stats['awaiting_verification'] > 0 || $stats['unread_messages'] > 0 || $stats['low_stock'] > 0)
    <div class="todo">
      @if($stats['awaiting_verification'] > 0)
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"><i class="fa-solid fa-money-check-dollar"></i> {{ $stats['awaiting_verification'] }} pembayaran perlu dicek <i class="fa-solid fa-chevron-right chev"></i></a>
      @endif
      @if($stats['unread_messages'] > 0)
        <a href="{{ route('admin.contact-messages.index') }}"><i class="fa-solid fa-envelope"></i> {{ $stats['unread_messages'] }} pesan belum dibaca <i class="fa-solid fa-chevron-right chev"></i></a>
      @endif
      @if($stats['low_stock'] > 0)
        <a href="{{ route('admin.stock-movements.index') }}"><i class="fa-solid fa-box-open"></i> {{ $stats['low_stock'] }} produk stok menipis <i class="fa-solid fa-chevron-right chev"></i></a>
      @endif
    </div>
  @else
    <p class="todo-clear">Tidak ada yang perlu dikerjakan sekarang.</p>
  @endif

  {{-- update 3: kartu ringkasan yang bisa diklik --}}
  {{-- Ringkasan --}}
  <div class="dash-card stats">
    <div class="stat">
      <a class="stat-link" href="{{ route('admin.reports.finance') }}" title="Buka Laporan Keuangan">
        <div class="lbl">Pendapatan (pesanan dibayar)</div>
        <div class="val">{{ rupiah($revenueTotal) }}</div>
        <div class="sub">
          Bulan ini {{ rupiah($revenueMonth) }}@if($revenueGrowth !== null) ({{ $revenueGrowth >= 0 ? '+' : '−' }}{{ abs($revenueGrowth) }}% dari bulan lalu)@endif
        </div>
      </a>
    </div>
    <div class="stat">
      <a class="stat-link" href="{{ route('admin.orders.index') }}" title="Lihat semua pesanan">
        <div class="lbl">Pesanan</div>
        <div class="val">{{ $stats['orders'] }}</div>
        <div class="sub">semua status</div>
      </a>
    </div>
    <div class="stat">
      <a class="stat-link" href="{{ route('admin.products.index') }}" title="Lihat semua produk">
        <div class="lbl">Produk</div>
        <div class="val">{{ $stats['products'] }}</div>
        <div class="sub">{{ $stats['low_stock'] }} stok menipis</div>
      </a>
    </div>
    <div class="stat">
      <a class="stat-link" href="{{ route('admin.users.index') }}" title="Lihat pelanggan">
        <div class="lbl">Pelanggan</div>
        <div class="val">{{ $stats['customers'] }}</div>
        <div class="sub">akun terdaftar</div>
      </a>
    </div>
  </div>

  {{-- update 2: grafik penjualan per hari/bulan dengan pilihan rentang waktu + pesanan per status (update 3: baris status jadi link) --}}
  {{-- Grafik + status --}}
  <div class="dash-grid-2">
    <div class="dash-card">
      <div class="panel-head">
        <h2>Penjualan per {{ $chartMonthly ? 'bulan' : 'hari' }}</h2>
        <div class="range-tabs" role="group" aria-label="Rentang waktu">
          @foreach($ranges as $key => $label)
            <a href="{{ route('admin.dashboard', ['range' => $key]) }}" class="{{ $range === $key ? 'on' : '' }}" @if($range === $key) aria-current="true" @endif>{{ $label }}</a>
          @endforeach
        </div>
      </div>
      <div class="panel-body">
        <p class="meta-line" title="Hanya pesanan yang sudah dibayar; angkanya sama dengan Laporan Keuangan">
          <strong>{{ rupiah($chartTotal) }}</strong> dari {{ $chartOrders }} pesanan dibayar · {{ $rangeLabel }} terakhir
        </p>
        <div class="bars {{ $chart->count() > 14 ? 'dense' : '' }}">
          @foreach($chart as $day)
            <div class="bar-col">
              <div class="bar {{ $day['today'] ? 'today' : '' }} {{ $day['total'] > 0 ? '' : 'zero' }}" @if($day['total'] > 0) style="height: {{ $day['percent'] }}%" @endif title="{{ $day['title'] }}: {{ rupiah($day['total']) }} ({{ $day['count'] }} pesanan)">
                @if($day['total'] > 0 && $chart->count() <= 14)<span class="v">{{ $day['short'] }}</span>@endif
              </div>
            </div>
          @endforeach
        </div>
        <div class="bar-labels {{ $chart->count() > 14 ? 'dense' : '' }}">
          @foreach($chart as $i => $day)<span>{{ $chart->count() > 14 && ! $loop->first && ! $loop->last && $i % 5 !== 0 ? '' : $day['label'] }}</span>@endforeach
        </div>
      </div>
    </div>

    <div class="dash-card">
      <div class="panel-head">
        <h2>Pesanan per status</h2>
        <span class="meta">{{ $rangeLabel }} terakhir</span>
      </div>
      <div class="panel-body">
        @forelse($statuses->where('count', '>', 0) as $row)
          <div class="hbar-row">
            <div class="hbar-top"><a href="{{ route('admin.orders.index', ['status' => $row['key']]) }}" title="Lihat pesanan {{ $row['label'] }}">{{ $row['label'] }}</a><span class="n">{{ $row['count'] }} <small>({{ $row['percent'] }}%)</small></span></div>
            <div class="hbar-track"><div class="hbar-fill" style="width: {{ max($row['percent'], 3) }}%; background: {{ $row['color'] }}"></div></div>
          </div>
        @empty
          <p class="empty-note">Belum ada pesanan pada rentang ini.</p>
        @endforelse
      </div>
    </div>
  </div>

  {{-- update 2: produk terlaris dan stok menipis dalam bentuk batang --}}
  {{-- Produk terlaris + stok menipis --}}
  <div class="dash-grid-2 {{ $lowStockProducts->isEmpty() ? 'single' : '' }}">
    <div class="dash-card">
      <div class="panel-head">
        <h2>Produk terlaris</h2>
        <span class="meta">unit terjual, {{ $rangeLabel }} terakhir</span>
      </div>
      <div class="panel-body">
        @forelse($topProducts as $row)
          <div class="hbar-row">
            <div class="hbar-top"><span>{{ $row->product_name }}</span><span class="n">{{ (int) $row->units }} unit <small>· {{ rupiah($row->revenue) }}</small></span></div>
            <div class="hbar-track"><div class="hbar-fill" style="width: {{ max(3, round($row->units / $topMax * 100)) }}%; background: var(--brand)"></div></div>
          </div>
        @empty
          <p class="empty-note">Belum ada penjualan pada rentang ini.</p>
        @endforelse
      </div>
    </div>

    @if($lowStockProducts->isNotEmpty())
      <div class="dash-card">
        <div class="panel-head">
          <h2>Stok menipis</h2>
          <span class="meta">&le; {{ $lowStockLimit }} unit</span>
        </div>
        <div class="panel-body">
          @foreach($lowStockProducts as $product)
            <div class="hbar-row">
              <div class="hbar-top">
                <a href="{{ route('admin.products.edit', $product) }}">{{ $product->name }}</a>
                <span class="n {{ $product->stock <= 0 ? 'out' : '' }}">{{ $product->stock <= 0 ? 'Habis' : 'Sisa '.$product->stock }}</span>
              </div>
              <div class="hbar-track"><div class="hbar-fill" style="width: {{ $product->stock <= 0 ? 0 : max(4, round($product->stock / $lowStockLimit * 100)) }}%; background: {{ $product->stock <= 2 ? '#dc4a4a' : '#f97316' }}"></div></div>
            </div>
          @endforeach
          <a href="{{ route('admin.stock-movements.index') }}" class="small fw-semibold text-decoration-none d-inline-block mt-2">Lihat riwayat stok</a>
        </div>
      </div>
    @endif
  </div>

  {{-- Pesanan terbaru --}}
  <div class="dash-grid-2 single">
    <div class="dash-card">
      <div class="panel-head">
        <h2>Pesanan terbaru</h2>
        <a href="{{ route('admin.orders.index') }}" class="small fw-semibold text-decoration-none">Lihat semua</a>
      </div>
      <div class="panel-body">
        @if($recentOrders->isEmpty())
          <p class="empty-note">Belum ada pesanan.</p>
        @else
          <div class="table-responsive">
            <table class="mini-table">
              <thead><tr><th>Pesanan</th><th>Pelanggan</th><th>Total</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($recentOrders as $order)
                  <tr>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold text-decoration-none">{{ $order->order_number }}</a><div class="text-muted" style="font-size:.75rem">{{ $order->created_at->copy()->timezone('Asia/Jakarta')->format('d M, H:i') }}</div></td>
                    <td>{{ $order->name }}</td>
                    <td>{{ rupiah($order->total) }}</td>
                    <td><span class="badge {{ $order->status_badge_class }} text-capitalize">{{ str_replace('_', ' ', $order->status) }}</span></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>

@endsection
