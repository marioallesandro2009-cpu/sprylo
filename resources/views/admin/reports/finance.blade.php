@extends('layouts.admin')

@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@push('styles')
  <style>
    .report-filter { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 12px; margin-bottom: 8px; }
    .report-filter label { display: block; font-size: .8rem; color: var(--muted); margin-bottom: 4px; }
    .report-quick { display: flex; flex-wrap: wrap; gap: 14px; font-size: .85rem; margin-bottom: 16px; }
    .report-quick a { text-decoration: none; }
    .report-grid { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr); gap: 16px; margin-bottom: 16px; align-items: start; }
    .report-grid .card-admin { margin-bottom: 0; }
    .report-section { font-size: .95rem; font-weight: 700; margin: 0 0 10px; }
    .statement-line { display: flex; justify-content: space-between; gap: 12px; padding: 8px 0; border-top: 1px solid var(--line); font-size: .9rem; }
    .statement-line:first-of-type { border-top: 0; }
    .statement-line.is-total { font-weight: 700; }
    .report-note { color: var(--muted); font-size: .8rem; margin: 10px 0 0; }
    .num { text-align: right; white-space: nowrap; }
    @media (max-width: 991px) { .report-grid { grid-template-columns: minmax(0, 1fr); } }
  </style>
@endpush

@section('custom-header')
  @php $range = ['from' => $report->from->toDateString(), 'to' => $report->to->toDateString()]; @endphp
  <div class="page-head">
    <div>
      <h1>Laporan Keuangan</h1>
      <p class="text-muted small mb-0">{{ $report->periodLabel() }}</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('admin.reports.finance.download', ['format' => 'xlsx'] + $range) }}" class="btn-brand"><i class="fa-solid fa-file-excel"></i> Excel</a>
      <a href="{{ route('admin.reports.finance.download', ['format' => 'pdf'] + $range) }}" class="btn btn-outline-secondary"><i class="fa-solid fa-file-pdf"></i> PDF</a>
    </div>
  </div>
@endsection

@section('content')
  @php
    $today = \Carbon\CarbonImmutable::now(\App\Services\FinancialReport::TIMEZONE);
    $lastMonth = $today->subMonthNoOverflow();
    $quickRanges = [
      'Bulan ini' => [$today->startOfMonth(), $today],
      'Bulan lalu' => [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()],
      'Tahun ini' => [$today->startOfYear(), $today],
    ];
    $summary = $report->summary();
  @endphp

  @if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  <form method="GET" action="{{ route('admin.reports.finance') }}" class="report-filter">
    <div>
      <label for="from">Dari tanggal</label>
      <input type="date" id="from" name="from" class="form-control form-control-sm" value="{{ $report->from->toDateString() }}" required>
    </div>
    <div>
      <label for="to">Sampai tanggal</label>
      <input type="date" id="to" name="to" class="form-control form-control-sm" value="{{ $report->to->toDateString() }}" required>
    </div>
    <button type="submit" class="btn btn-sm btn-outline-secondary">Tampilkan</button>
  </form>
  <div class="report-quick">
    @foreach($quickRanges as $label => [$start, $end])
      <a href="{{ route('admin.reports.finance', ['from' => $start->toDateString(), 'to' => $end->toDateString()]) }}">{{ $label }}</a>
    @endforeach
  </div>

  @if($report->difference() != 0.0)
    <div class="alert alert-warning">Ada selisih {{ rupiah(abs($report->difference())) }} antara total pesanan dan rinciannya. Periksa pesanan pada periode ini sebelum laporan dipakai.</div>
  @endif

  <div class="report-grid">
    <div class="card-admin">
      <h2 class="report-section">Ringkasan · {{ $summary['count'] }} pesanan dibayar</h2>
      @foreach($report->statement() as [$label, $amount, $isTotal])
        <div class="statement-line {{ $isTotal ? 'is-total' : '' }}">
          <span>{{ $label }}</span>
          <span>{{ $amount < 0 ? '−'.rupiah(abs($amount)) : rupiah($amount) }}</span>
        </div>
      @endforeach
      <p class="report-note">Menghitung pesanan berstatus paid, processing, shipped, dan completed menurut tanggal pesanan (WIB). Biaya modal dan pengeluaran belum termasuk.</p>
    </div>

    <div class="card-admin">
      <h2 class="report-section">Metode pembayaran</h2>
      @forelse($report->paymentMethods() as $method)
        <div class="statement-line">
          <span>{{ $method['label'] }} · {{ $method['count'] }} pesanan</span>
          <span>{{ rupiah($method['total']) }}</span>
        </div>
      @empty
        <p class="text-muted small mb-0">Belum ada pesanan dibayar pada periode ini.</p>
      @endforelse
    </div>
  </div>

  @if($report->periods()->isNotEmpty())
    <div class="card-admin">
      <h2 class="report-section">Per {{ $report->isMonthly() ? 'bulan' : 'hari' }}</h2>
      <div class="table-responsive">
        <table class="table table-admin align-middle">
          <thead><tr><th>Periode</th><th class="num">Pesanan</th><th class="num">Barang</th><th class="num">Diskon</th><th class="num">PPN</th><th class="num">Ongkir</th><th class="num">Asuransi</th><th class="num">Total</th></tr></thead>
          <tbody>
            @foreach($report->periods() as $period)
              <tr>
                <td>{{ $period['label'] }}</td>
                <td class="num">{{ $period['count'] }}</td>
                <td class="num">{{ rupiah($period['goods']) }}</td>
                <td class="num">{{ rupiah($period['discount']) }}</td>
                <td class="num">{{ rupiah($period['tax']) }}</td>
                <td class="num">{{ rupiah($period['shipping']) }}</td>
                <td class="num">{{ rupiah($period['insurance']) }}</td>
                <td class="num fw-semibold">{{ rupiah($period['total']) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-admin">
      <h2 class="report-section">Daftar pesanan</h2>
      <div class="table-responsive">
        <table class="table table-admin align-middle">
          <thead><tr><th>Tanggal</th><th>Pesanan</th><th>Pelanggan</th><th>Metode</th><th class="num">Barang</th><th class="num">Diskon</th><th class="num">PPN</th><th class="num">Total</th></tr></thead>
          <tbody>
            @foreach($orders as $order)
              <tr>
                <td class="text-muted small">{{ $report->placedAt($order)->format('d M Y, H:i') }}</td>
                <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold text-decoration-none">{{ $order->order_number }}</a></td>
                <td>{{ $order->name }}</td>
                <td>{{ $report->paymentLabel($order->payment_method) }}</td>
                <td class="num">{{ rupiah($order->subtotal) }}</td>
                <td class="num">{{ rupiah($order->promo_discount) }}</td>
                <td class="num">{{ rupiah($order->tax) }}</td>
                <td class="num fw-semibold">{{ rupiah($order->total) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    @if($orders->hasPages())
      <div class="mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
    @endif
  @endif
@endsection
