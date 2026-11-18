<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Sales figures for a date range: the one source the page and every download read from, so they can't disagree.
 * Counts orders that have been paid, by the day they were placed in Jakarta time.
 */
class FinancialReport
{
    public const TIMEZONE = 'Asia/Jakarta';

    /** Longest range one report may cover. */
    public const MAX_DAYS = 366;

    /** A range longer than this is summarised per month instead of per day. */
    private const DAILY_LIMIT = 62;

    private const PAYMENT_LABELS = ['bank_transfer' => 'Transfer Bank', 'qris' => 'QRIS'];

    private ?Collection $orders = null;

    public function __construct(public readonly CarbonImmutable $from, public readonly CarbonImmutable $to)
    {
    }

    /** @param string $from Y-m-d  @param string $to Y-m-d */
    public static function between(string $from, string $to): self
    {
        return new self(
            CarbonImmutable::createFromFormat('!Y-m-d', $from, self::TIMEZONE)->startOfDay(),
            CarbonImmutable::createFromFormat('!Y-m-d', $to, self::TIMEZONE)->endOfDay(),
        );
    }

    public function query(): Builder
    {
        return Order::query()
            ->whereIn('status', Order::PAID_STATUSES)
            ->whereBetween('created_at', [
                $this->from->setTimezone(config('app.timezone')),
                $this->to->setTimezone(config('app.timezone')),
            ])
            ->orderBy('created_at')
            ->orderBy('id');
    }

    public function orders(): Collection
    {
        return $this->orders ??= $this->query()->withSum('items', 'qty')->get();
    }

    public function statusLabel(string $status): string
    {
        return ucfirst(str_replace('_', ' ', $status));
    }

    public function siteName(): string
    {
        return (string) (Setting::where('key', 'site_name')->value('value') ?? config('app.name'));
    }

    public function periodLabel(): string
    {
        return $this->from->locale('id')->translatedFormat('j F Y').' – '.$this->to->locale('id')->translatedFormat('j F Y');
    }

    public function filename(string $extension): string
    {
        return 'laporan-keuangan-'.$this->from->toDateString().'_'.$this->to->toDateString().'.'.$extension;
    }

    /** The money in the period, each figure summed from the orders' own columns. */
    public function summary(): array
    {
        return $this->sums($this->orders());
    }

    /** The statement, top to bottom: [label, amount, is it a total line]. */
    public function statement(): array
    {
        $sum = $this->summary();

        return [
            ['Penjualan barang', $sum['goods'], false],
            ['Diskon promo', -$sum['discount'], false],
            ['Penjualan bersih (setelah diskon)', $sum['net'], true],
            ['PPN', $sum['tax'], false],
            ['Ongkos kirim', $sum['shipping'], false],
            ['Asuransi pengiriman', $sum['insurance'], false],
            ['Total diterima', $sum['total'], true],
        ];
    }

    /** Amount by which the orders' stored totals differ from their parts; 0 when the report adds up. */
    public function difference(): float
    {
        $sum = $this->summary();

        return round($sum['total'] - ($sum['net'] + $sum['tax'] + $sum['shipping'] + $sum['insurance']), 2);
    }

    public function isMonthly(): bool
    {
        return $this->from->diffInDays($this->to) > self::DAILY_LIMIT;
    }

    /** One row per day, or per month on a long range; periods without sales are left out. */
    public function periods(): Collection
    {
        $monthly = $this->isMonthly();

        return $this->orders()
            ->groupBy(fn (Order $order) => $this->placedAt($order)->format($monthly ? 'Y-m' : 'Y-m-d'))
            ->map(function (Collection $orders) use ($monthly) {
                $first = $this->placedAt($orders->first())->locale('id');

                return ['label' => $first->translatedFormat($monthly ? 'F Y' : 'j M Y')] + $this->sums($orders);
            })
            ->values();
    }

    // update 2: deret penjualan per hari/bulan untuk grafik dashboard
    /**
     * Like periods(), but every day (or month) in the range gets a row, empty ones included,
     * so a chart can draw the whole range. Totals come from the same orders as the report.
     *
     * @return Collection<int, array{key: string, date: CarbonImmutable, count: int, total: float}>
     */
    public function series(): Collection
    {
        $monthly = $this->isMonthly();
        $format = $monthly ? 'Y-m' : 'Y-m-d';
        $byKey = $this->orders()->groupBy(fn (Order $order) => $this->placedAt($order)->format($format));

        $rows = collect();

        for ($cursor = $monthly ? $this->from->startOfMonth() : $this->from->startOfDay(); $cursor <= $this->to; $cursor = $monthly ? $cursor->addMonthNoOverflow() : $cursor->addDay()) {
            $orders = $byKey->get($cursor->format($format), collect());

            $rows->push([
                'key' => $cursor->format($format),
                'date' => $cursor,
                'count' => $orders->count(),
                'total' => (float) $orders->sum('total'),
            ]);
        }

        return $rows;
    }

    public function paymentMethods(): Collection
    {
        return $this->orders()
            ->groupBy('payment_method')
            ->map(fn (Collection $orders, string $method) => [
                'label' => $this->paymentLabel($method),
                'count' => $orders->count(),
                'total' => (float) $orders->sum('total'),
            ])
            ->sortByDesc('total')
            ->values();
    }

    public function paymentLabel(?string $method): string
    {
        return self::PAYMENT_LABELS[$method] ?? str($method ?: 'lainnya')->replace('_', ' ')->title()->value();
    }

    public function placedAt(Order $order): CarbonImmutable
    {
        return CarbonImmutable::instance($order->created_at)->setTimezone(self::TIMEZONE);
    }

    private function sums(Collection $orders): array
    {
        $goods = (float) $orders->sum('subtotal');
        $discount = (float) $orders->sum('promo_discount');

        return [
            'count' => $orders->count(),
            'goods' => $goods,
            'discount' => $discount,
            'net' => $goods - $discount,
            'tax' => (float) $orders->sum('tax'),
            'shipping' => (float) $orders->sum('shipping'),
            'insurance' => (float) $orders->sum('insurance_fee'),
            'total' => (float) $orders->sum('total'),
        ];
    }
}
