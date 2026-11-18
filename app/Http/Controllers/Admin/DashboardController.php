<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\FinancialReport;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Zona waktu tampilan dashboard (data di database tersimpan dalam config('app.timezone'), yaitu UTC). */
    private const DISPLAY_TZ = FinancialReport::TIMEZONE;

    /** Produk dengan stok sama atau di bawah angka ini dianggap menipis. */
    private const LOW_STOCK_LIMIT = 5;

    /** Pilihan rentang waktu: label dan jumlah hari ke belakang (12 bulan = per bulan). */
    private const RANGES = [
        '7d' => ['7 hari', 7],
        '30d' => ['30 hari', 30],
        '12m' => ['12 bulan', null],
    ];

    private const DEFAULT_RANGE = '7d';

    /** Urutan, label, dan warna status di kartu "Status Pesanan". */
    private const STATUS_META = [
        'awaiting_proof' => ['Belum Bayar', '#9ca3af'],
        'pending' => ['Verifikasi', '#eab308'],
        'paid' => ['Bayar', '#16a34a'],
        'processing' => ['Kemas Barang', '#f97316'],
        'shipped' => ['Dikirim', '#6d5acd'],
        'completed' => ['Selesai', '#3b82f6'],
        'cancelled' => ['Dibatalkan', '#dc4a4a'],
    ];

    public function index(Request $request): View
    {
        $now = now(self::DISPLAY_TZ);
        // Batas hari/bulan dihitung menurut WIB, lalu diubah ke zona database sebelum dipakai di query.
        $db = fn ($time) => $time->copy()->setTimezone(config('app.timezone'));
        $paid = fn () => Order::whereIn('status', Order::PAID_STATUSES);

        // Pendapatan
        $revenueTotal = (float) $paid()->sum('total');
        $revenueMonth = (float) $paid()->where('created_at', '>=', $db($now->copy()->startOfMonth()))->sum('total');
        $lastMonth = $now->copy()->subMonthNoOverflow();
        $revenueLastMonth = (float) $paid()
            ->whereBetween('created_at', [$db($lastMonth->copy()->startOfMonth()), $db($lastMonth->copy()->endOfMonth())])
            ->sum('total');
        $revenueGrowth = $revenueLastMonth > 0
            ? (int) round((($revenueMonth - $revenueLastMonth) / $revenueLastMonth) * 100)
            : null;

        // update 2: pilihan rentang waktu untuk semua grafik di bawah
        $range = array_key_exists((string) $request->query('range'), self::RANGES) ? (string) $request->query('range') : self::DEFAULT_RANGE;
        [$rangeLabel, $days] = self::RANGES[$range];
        $today = CarbonImmutable::instance($now)->startOfDay();
        $from = $days ? $today->subDays($days - 1) : $today->startOfMonth()->subMonthsNoOverflow(11);
        $report = FinancialReport::between($from->toDateString(), $today->toDateString());

        // Penjualan per hari/bulan: angka yang sama dengan Laporan Keuangan (hanya pesanan yang sudah dibayar)
        $monthly = $report->isMonthly();
        $chart = $report->series()->map(fn (array $row) => [
            'label' => $row['date']->locale('id')->translatedFormat($monthly ? 'M' : ($days > 7 ? 'j' : 'D')),
            'title' => $row['date']->locale('id')->translatedFormat($monthly ? 'F Y' : 'l, j F Y'),
            'total' => $row['total'],
            'count' => $row['count'],
            'today' => $row['key'] === $today->format($monthly ? 'Y-m' : 'Y-m-d'),
        ]);
        $chartMax = max(1, (float) $chart->max('total'));
        $chart = $chart->map(fn ($day) => $day + [
            'percent' => $day['total'] > 0 ? max(6, round($day['total'] / $chartMax * 100)) : 0,
            'short' => $this->shortRupiah($day['total']),
        ]);

        // Status pesanan, seluruh pesanan, semua waktu (dipakai kartu "perlu dikerjakan")
        $counts = Order::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $orderTotal = (int) $counts->sum();

        // Status pesanan yang dibuat dalam rentang terpilih
        $rangeCounts = Order::selectRaw('status, count(*) as total')
            ->whereBetween('created_at', [$db($report->from), $db($report->to)])
            ->groupBy('status')->pluck('total', 'status');
        $rangeOrders = (int) $rangeCounts->sum();
        $statuses = collect(self::STATUS_META)->map(function (array $meta, string $key) use ($rangeCounts, $rangeOrders) {
            $count = (int) ($rangeCounts[$key] ?? 0);

            return [
                'key' => $key, // update 3: dipakai untuk link ke daftar pesanan per status
                'label' => $meta[0],
                'color' => $meta[1],
                'count' => $count,
                'percent' => $rangeOrders > 0 ? (int) round($count / $rangeOrders * 100) : 0,
            ];
        })->values();

        // update 2: produk terlaris, jumlah unit dari pesanan yang sudah dibayar dalam rentang
        $topProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', Order::PAID_STATUSES)
            ->whereBetween('orders.created_at', [$db($report->from), $db($report->to)])
            ->selectRaw('order_items.product_id, order_items.product_name, sum(order_items.qty) as units, sum(order_items.subtotal) as revenue')
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('units')
            ->orderBy('order_items.product_name')
            ->limit(5)
            ->get();
        $topMax = max(1, (int) $topProducts->max('units'));

        // Ringkasan lain
        $lowStockCount = Product::where('stock', '<=', self::LOW_STOCK_LIMIT)->count();

        $stats = [
            'products' => Product::count(),
            'low_stock' => $lowStockCount,
            'orders' => $orderTotal,
            'awaiting_verification' => (int) ($counts['pending'] ?? 0),
            'customers' => User::where('role', 'user')->count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
        ];

        $lowStockProducts = Product::where('stock', '<=', self::LOW_STOCK_LIMIT)->orderBy('stock')->orderBy('name')->take(5)->get();
        $recentOrders = Order::withCount('items')->latest()->take(5)->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'revenueTotal' => $revenueTotal,
            'revenueMonth' => $revenueMonth,
            'revenueGrowth' => $revenueGrowth,
            'ranges' => collect(self::RANGES)->map(fn (array $r) => $r[0]),
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'chart' => $chart,
            'chartTotal' => $report->summary()['total'],
            'chartOrders' => $report->summary()['count'],
            'chartMonthly' => $monthly,
            'statuses' => $statuses,
            'rangeOrders' => $rangeOrders,
            'orderTotal' => $orderTotal,
            'topProducts' => $topProducts,
            'topMax' => $topMax,
            'lowStockProducts' => $lowStockProducts,
            'lowStockLimit' => self::LOW_STOCK_LIMIT,
            'recentOrders' => $recentOrders,
            'today' => $now->copy()->locale('id')->translatedFormat('l, j F Y'),
        ]);
    }

    /** 1.400.000 -> "1,4 jt", 963.000 -> "963 rb". */
    private function shortRupiah(float $amount): string
    {
        $format = fn (float $n) => rtrim(rtrim(number_format($n, 1, ',', '.'), '0'), ',');

        return match (true) {
            $amount >= 1_000_000_000 => $format($amount / 1_000_000_000).' M',
            $amount >= 1_000_000 => $format($amount / 1_000_000).' jt',
            $amount >= 1_000 => $format($amount / 1_000).' rb',
            default => number_format($amount, 0, ',', '.'),
        };
    }
}
