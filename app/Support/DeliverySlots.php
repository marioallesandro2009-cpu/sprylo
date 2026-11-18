<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Delivery time windows a shopper can book on top of a delivery date, with a limit on how many
 * orders each window takes per day so the dispatch team isn't overbooked.
 */
class DeliverySlots
{
    public const SLOTS = [
        'morning' => ['label' => 'Morning', 'time' => '09:00 – 12:00'],
        'afternoon' => ['label' => 'Afternoon', 'time' => '12:00 – 16:00'],
        'evening' => ['label' => 'Evening', 'time' => '16:00 – 20:00'],
    ];

    public const DEFAULT_CAPACITY = 8;

    /** Orders each slot accepts per day (Settings → delivery_slot_capacity). */
    public static function capacity(): int
    {
        return max(1, (int) (Setting::where('key', 'delivery_slot_capacity')->value('value') ?: self::DEFAULT_CAPACITY));
    }

    public static function keys(): array
    {
        return array_keys(self::SLOTS);
    }

    public static function label(?string $slot): ?string
    {
        return $slot && isset(self::SLOTS[$slot]) ? self::SLOTS[$slot]['label'].' ('.self::SLOTS[$slot]['time'].')' : null;
    }

    /** Orders booked per slot on a date; cancelled orders give their place back. */
    public static function booked(string|Carbon $date): Collection
    {
        return Order::whereDate('requested_delivery_date', Carbon::parse($date)->toDateString())
            ->whereNotNull('requested_delivery_slot')
            ->where('status', '!=', 'cancelled')
            ->selectRaw('requested_delivery_slot as slot, count(*) as total')
            ->groupBy('requested_delivery_slot')
            ->pluck('total', 'slot');
    }

    /**
     * @return array<int, array{key: string, label: string, time: string, booked: int, remaining: int, full: bool}>
     */
    public static function forDate(string|Carbon $date): array
    {
        $booked = static::booked($date);
        $capacity = static::capacity();

        return collect(self::SLOTS)->map(function (array $slot, string $key) use ($booked, $capacity) {
            $taken = (int) ($booked[$key] ?? 0);

            return $slot + [
                'key' => $key,
                'booked' => $taken,
                'remaining' => max($capacity - $taken, 0),
                'full' => $taken >= $capacity,
            ];
        })->values()->all();
    }
}
