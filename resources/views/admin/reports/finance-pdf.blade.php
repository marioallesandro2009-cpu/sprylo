<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Keuangan {{ $report->periodLabel() }}</title>
  <style>
    @page { margin: 28px 32px; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #000; }
    h1 { font-size: 14px; margin: 0 0 2px; }
    .muted { color: #555; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 3px 5px; }
    th { background: #{{ $headerColour }}; font-weight: bold; text-align: center; }
    .num { text-align: right; white-space: nowrap; }
    tr { page-break-inside: avoid; }
  </style>
</head>
<body>
  <h1>Laporan Keuangan</h1>
  <div class="muted">Periode: {{ $report->periodLabel() }}</div>

  <table>
    <thead><tr>@foreach($headers as $header)<th>{{ $header }}</th>@endforeach</tr></thead>
    <tbody>
      @foreach($report->orders() as $order)
        <tr>
          <td class="num">{{ $loop->iteration }}</td>
          <td>{{ $order->order_number }}</td>
          <td>{{ $order->name }}</td>
          <td>{{ $order->email }}</td>
          <td class="num">{{ (int) $order->items_sum_qty }}</td>
          <td class="num">{{ rupiah($order->total) }}</td>
          <td>{{ $report->statusLabel($order->status) }}</td>
          <td class="num">{{ $report->placedAt($order)->format('d M Y H:i') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
