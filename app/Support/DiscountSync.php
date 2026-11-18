<?php

namespace App\Support;

use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Keeps products.promo_price / product_variants.promo_price in step with the discount schedule.
 *
 * The cheapest live discount per product (or variant) is written next to sale_price so storefront
 * queries that filter and sort on price stay plain SQL. It reruns whenever a rule is saved, a
 * flash-sale quota changes, or the next scheduled start/end moment passes.
 */
class DiscountSync
{
    private const NEXT_BOUNDARY_KEY = 'discounts.next_boundary';

    private static bool $checked = false;

    /**
     * Cheap per-request guard: only does real work once a rule has started or ended since the last run.
     */
    public static function ensureFresh(): void
    {
        if (self::$checked) {
            return;
        }

        self::$checked = true;

        try {
            $next = Cache::get(self::NEXT_BOUNDARY_KEY);

            if ($next === null || now()->timestamp >= (int) $next) {
                static::run();
            }
        } catch (Throwable $e) {
            // Never take the storefront down because the schedule couldn't be synced (e.g. before migrating).
            report($e);
        }
    }

    public static function run(): void
    {
        Discount::forgetMemo();
        $live = Discount::live()->get();

        DB::transaction(function () use ($live) {
            Product::with('variants')->orderBy('id')->chunkById(200, function ($products) use ($live) {
                foreach ($products as $product) {
                    static::syncProduct($product, $live);
                }
            });
        });

        Discount::forgetMemo();
        Cache::forever(self::NEXT_BOUNDARY_KEY, static::nextBoundary() ?? PHP_INT_MAX);
    }

    private static function syncProduct(Product $product, Collection $live): void
    {
        $basePrice = (float) $product->price;

        if ($product->variants->isEmpty()) {
            [$price, $id] = static::best($live, $product, null, $basePrice, static::nullableFloat($product->getRawOriginal('sale_price')));
            static::write('products', $product->id, $price, $id, $product->getRawOriginal('promo_price'), $product->promo_discount_id);

            return;
        }

        $cheapest = null; // [final price, discount id] of the cheapest variant after promos
        $manualFloor = null;

        foreach ($product->variants as $variant) {
            $variantBase = (float) $variant->price;
            $variantManual = static::nullableFloat($variant->getRawOriginal('sale_price'));
            [$price, $id] = static::best($live, $product, $variant, $variantBase, $variantManual);

            static::write('product_variants', $variant->id, $price, $id, $variant->getRawOriginal('promo_price'), $variant->promo_discount_id);

            $manualEffective = min($variantBase, $variantManual ?? $variantBase);
            $manualFloor = $manualFloor === null ? $manualEffective : min($manualFloor, $manualEffective);

            $final = $price ?? $manualEffective;
            if ($cheapest === null || $final < $cheapest[0]) {
                $cheapest = [$final, $price !== null ? $id : null];
            }
        }

        // The product row mirrors its cheapest variant, so "From Rp …" cards and price sorting reflect the promo too.
        $promo = $cheapest !== null && $cheapest[0] < $manualFloor ? $cheapest : [null, null];
        static::write('products', $product->id, $promo[0], $promo[1], $product->getRawOriginal('promo_price'), $product->promo_discount_id);
    }

    /**
     * The lowest price any live rule gives this product/variant, provided it beats the
     * manual price. A flash sale wins a tie so its quota is the one that gets consumed.
     *
     * @return array{0: ?float, 1: ?int} [promo price, discount id]
     */
    private static function best(Collection $live, Product $product, ?ProductVariant $variant, float $base, ?float $manualSale): array
    {
        $floor = min($base, $manualSale ?? $base);
        $bestPrice = null;
        $bestId = null;
        $bestIsFlash = false;

        foreach ($live as $discount) {
            if (! $discount->appliesTo($product, $variant)) {
                continue;
            }

            $price = $discount->apply($base);

            if ($price <= 0 || $price >= $floor) {
                continue;
            }

            if ($bestPrice === null || $price < $bestPrice || ($price === $bestPrice && $discount->is_flash && ! $bestIsFlash)) {
                $bestPrice = $price;
                $bestId = $discount->id;
                $bestIsFlash = $discount->is_flash;
            }
        }

        return [$bestPrice, $bestId];
    }

    private static function write(string $table, int $id, ?float $price, ?int $discountId, mixed $currentPrice, mixed $currentDiscountId): void
    {
        if (static::nullableFloat($currentPrice) === $price && ($currentDiscountId === null ? null : (int) $currentDiscountId) === $discountId) {
            return;
        }

        DB::table($table)->where('id', $id)->update(['promo_price' => $price, 'promo_discount_id' => $discountId]);
    }

    /** The next moment a rule starts or ends, so we know when the materialised prices go stale. */
    private static function nextBoundary(): ?int
    {
        $now = now();
        $moments = collect();

        foreach (Discount::where('is_active', true)->get(['starts_at', 'ends_at']) as $discount) {
            foreach ([$discount->starts_at, $discount->ends_at] as $moment) {
                if ($moment && $moment->gt($now)) {
                    $moments->push($moment->timestamp);
                }
            }
        }

        return $moments->min();
    }

    private static function nullableFloat(mixed $value): ?float
    {
        return $value === null ? null : (float) $value;
    }
}
