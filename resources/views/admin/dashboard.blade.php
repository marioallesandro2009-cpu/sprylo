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

  {{-- Perlu dikerjakan: hanya muncul kalau memang ada yang harus dikerjakan --}}
  @if($stats['awaiting_verification'] > 0 || $stats['unread_messages'] > 0 || $stats['low_stock'] > 0)
    <div class="todo">
      @if($stats['awaiting_verification'] > 0)
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}">{{ $stats['awaiting_verification'] }} pembayaran perlu dicek</a>
      @endif
      @if($stats['unread_messages'] > 0)
        <a href="{{ route('admin.contact-messages.index') }}">{{ $stats['unread_messages'] }} pesan belum dibaca</a>
      @endif
      @if($stats['low_stock'] > 0)
        <a href="{{ route('admin.products.index') }}">{{ $stats['low_stock'] }} produk stok menipis</a>
      @endif
    </div>
  @else
    <p class="todo-clear">Tidak ada yang perlu dikerjakan sekarang.</p>
  @endif

  {{-- Ringkasan --}}
  <div class="dash-card stats">
    <div class="stat">
      <div class="lbl">Pendapatan</div>
      <div class="val">{{ rupiah($revenueTotal) }}</div>
      <div class="sub">
        Bulan ini {{ rupiah($revenueMonth) }}@if($revenueGrowth !== null) ({{ $revenueGrowth >= 0 ? '+' : '−' }}{{ abs($revenueGrowth) }}%)@endif
      </div>
    </div>
    <div class="stat">
      <div class="lbl">Pesanan</div>
      <div class="val">{{ $stats['orders'] }}</div>
    </div>
    <div class="stat">
      <div class="lbl">Produk</div>
      <div class="val">{{ $stats['products'] }}</div>
    </div>
    <div class="stat">
      <div class="lbl">Pelanggan</div>
      <div class="val">{{ $stats['customers'] }}</div>
    </div>
  </div>

  {{-- Grafik + status --}}
  <div class="dash-grid-2">
    <div class="dash-card">
      <div class="panel-head">
        <h2>Penjualan 7 hari terakhir</h2>
        <span class="meta" title="Hanya pesanan yang sudah dibayar">{{ rupiah($chartTotal) }} dibayar</span>
      </div>
      <div class="panel-body">
        <div class="bars">
          @foreach($chart as $day)
            <div class="bar-col">
              <div class="bar {{ $day['today'] ? 'today' : '' }} {{ $day['total'] > 0 ? '' : 'zero' }}" @if($day['total'] > 0) style="height: {{ $day['percent'] }}%" @endif title="{{ $day['label'] }}: {{ rupiah($day['total']) }}">
                @if($day['total'] > 0)<span class="v">{{ $day['short'] }}</span>@endif
              </div>
            </div>
          @endforeach
        </div>
        <div class="bar-labels">
          @foreach($chart as $day)<span>{{ $day['label'] }}</span>@endforeach
        </div>
      </div>
    </div>

    <div class="dash-card">
      <div class="panel-head">
        <h2>Status pesanan</h2>
      </div>
      <div class="panel-body">
        @forelse($statuses->where('count', '>', 0) as $row)
          <div class="list-row"><span>{{ $row['label'] }}</span><span class="n">{{ $row['count'] }}</span></div>
        @empty
          <p class="empty-note">Belum ada pesanan.</p>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Pesanan terbaru + stok menipis (panel stok hanya muncul kalau ada isinya) --}}
  <div class="dash-grid-2 {{ $lowStockProducts->isEmpty() ? 'single' : '' }}">
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

    @if($lowStockProducts->isNotEmpty())
      <div class="dash-card">
        <div class="panel-head">
          <h2>Stok menipis</h2>
          <span class="meta">&le; {{ $lowStockLimit }} unit</span>
        </div>
        <div class="panel-body">
          @foreach($lowStockProducts as $product)
            <div class="list-row">
              <a href="{{ route('admin.products.edit', $product) }}">{{ $product->name }}</a>
              <span class="n {{ $product->stock <= 0 ? 'out' : '' }}">{{ $product->stock <= 0 ? 'Habis' : 'Sisa '.$product->stock }}</span>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  </div>

@endsection
