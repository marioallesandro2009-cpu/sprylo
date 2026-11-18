<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

// update 1: halaman riwayat stok
/**
 * Riwayat pergerakan stok (hanya baca): setiap tambah/kurang stok produk atau varian.
 */
class StockMovementController extends Controller
{
    private const TZ = 'Asia/Jakarta';

    public function index(Request $request): View
    {
        $type = $request->input('type');
        $from = $this->date($request->input('from'));
        $to = $this->date($request->input('to'));

        $query = StockMovement::query()
            ->when($request->filled('product'), fn ($q) => $q->where('product_id', (int) $request->input('product')))
            ->when(is_string($type) && isset(StockMovement::TYPES[$type]), fn ($q) => $q->where('type', $type))
            ->when($from, fn ($q) => $q->where('created_at', '>=', Carbon::parse($from, self::TZ)->startOfDay()->utc()))
            ->when($to, fn ($q) => $q->where('created_at', '<=', Carbon::parse($to, self::TZ)->endOfDay()->utc()));

        // update 3: ringkasan unit masuk/keluar dan pintasan tanggal
        // Total unit masuk dan keluar untuk filter yang sedang dipakai (seluruh halaman, bukan hanya halaman ini).
        $sums = (clone $query)->selectRaw('coalesce(sum(case when quantity > 0 then quantity else 0 end), 0) as masuk, coalesce(sum(case when quantity < 0 then -quantity else 0 end), 0) as keluar')->first();

        $movements = $query
            ->with('product:id,slug')
            ->latest('created_at')
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        $today = now(self::TZ);

        return view('admin.stock-movements.index', [
            'movements' => $movements,
            'unitsIn' => (int) $sums->masuk,
            'unitsOut' => (int) $sums->keluar,
            'shortcuts' => [
                'Hari ini' => [$today->toDateString(), $today->toDateString()],
                '7 hari' => [$today->copy()->subDays(6)->toDateString(), $today->toDateString()],
                '30 hari' => [$today->copy()->subDays(29)->toDateString(), $today->toDateString()],
            ],
            'products' => Product::orderBy('name')->get(['id', 'name']),
            'types' => StockMovement::TYPES,
            'tz' => self::TZ,
        ]);
    }

    private function date(mixed $value): ?string
    {
        return is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) && strtotime($value) ? $value : null;
    }
}
