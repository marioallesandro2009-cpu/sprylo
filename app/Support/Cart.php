<?php

namespace App\Support;

use App\Models\Product;
use App\Models\PromoCode;
use App\Models\Setting;
use Illuminate\Support\Collection;

class Cart
{
    /** PPN, charged on the goods after any promo. Shipping and shipping insurance are not taxed. */
    public const PPN_PERCENT = 12;

    /**
     * Cart entries are keyed "{productId}" or "{productId}:{variantId}" so a product with
     * variants can sit in the cart multiple times — once per variant chosen.
     */
    public static function key(int $productId, ?int $variantId): string
    {
        return $variantId ? "{$productId}:{$variantId}" : (string) $productId;
    }

    /**
     * @return array{product_id: int, variant_id: ?int}
     */
    public static function parseKey(string $key): array
    {
        [$productId, $variantId] = array_pad(explode(':', $key, 2), 2, null);

        return [
            'product_id' => (int) $productId,
            'variant_id' => $variantId !== null ? (int) $variantId : null,
        ];
    }

    public static function items(): Collection
    {
        $cart = session('cart', []);
        $productIds = collect(array_keys($cart))->map(fn ($key) => static::parseKey($key)['product_id'])->unique();

        $products = Product::with(['images', 'variants'])->whereIn('id', $productIds)->get()->keyBy('id');

        return collect($cart)
            ->map(function ($qty, $key) use ($products) {
                $parsed = static::parseKey($key);
                $product = $products->get($parsed['product_id']);

                // A product the admin has since deactivated drops out of the cart instead of being purchasable.
                if (! $product || ! $product->is_active) {
                    return null;
                }

                $variant = $parsed['variant_id'] ? $product->variants->firstWhere('id', $parsed['variant_id']) : null;

                if ($parsed['variant_id'] && ! $variant) {
                    return null;
                }

                $priced = $variant ?? $product;
                $unitPrice = $priced->sale_price ?? $priced->price;
                $discount = $priced->promo;
                $stock = $variant ? $variant->stock : $product->stock;
                $weight = $variant ? $variant->weight : $product->weight;

                return [
                    'cart_key' => $key,
                    'product' => $product,
                    'variant' => $variant,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $qty,
                    'stock' => $stock,
                    'discount_id' => $discount?->id,
                    'discount' => $discount,
                    'discount_remaining' => $discount?->remaining,
                    'weight' => $weight,
                    'has_warranty' => (bool) $variant?->has_warranty,
                ];
            })
            ->filter()
            ->values();
    }

    public static function totals(Collection $items): array
    {
        $subtotal = (float) $items->sum('subtotal');
        $freeThreshold = (float) (Setting::where('key', 'shipping_free_threshold')->value('value') ?? 750000);
        $shippingFee = (float) (Setting::where('key', 'shipping_fee')->value('value') ?? 25000);
        $shipping = ($subtotal > 0 && $subtotal < $freeThreshold) ? $shippingFee : 0.0;

        $promoCode = null;
        $promoDiscount = 0.0;

        if ($code = session('promo_code')) {
            $promo = PromoCode::where('code', $code)->active()->first();

            if ($promo && $promo->appliesTo($subtotal)) {
                $promoCode = $promo->code;
                $promoDiscount = $promo->discountFor($subtotal);
            }
        }

        $tax = round(max($subtotal - $promoDiscount, 0) * self::PPN_PERCENT / 100);

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'promo_code' => $promoCode,
            'promo_discount' => $promoDiscount,
            'total' => max($subtotal + $shipping + $tax - $promoDiscount, 0),
        ];
    }

    public static function clear(): void
    {
        session()->forget(['cart', 'promo_code']);
    }
}
