<?php

namespace App\Support;

use App\Models\Product;
use App\Models\PromoCode;
use App\Models\Setting;
use Illuminate\Support\Collection;

class Cart
{
    public static function items(): Collection
    {
        $cart = session('cart', []);
        $products = Product::with('images')->whereIn('id', array_keys($cart))->get()->keyBy('id');

        return collect($cart)
            ->filter(fn ($qty, $productId) => $products->has($productId))
            ->map(function ($qty, $productId) use ($products) {
                $product = $products->get($productId);
                $unitPrice = $product->sale_price ?? $product->price;

                return [
                    'product' => $product,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $qty,
                ];
            })
            ->values();
    }

    public static function totals(Collection $items): array
    {
        $subtotal = (float) $items->sum('subtotal');
        $freeThreshold = (float) (Setting::where('key', 'shipping_free_threshold')->value('value') ?? 750000);
        $shippingFee = (float) (Setting::where('key', 'shipping_fee')->value('value') ?? 25000);
        $shipping = ($subtotal > 0 && $subtotal < $freeThreshold) ? $shippingFee : 0.0;
        $tax = round($subtotal * 0.08);

        $promoCode = null;
        $promoDiscount = 0.0;

        if ($code = session('promo_code')) {
            $promo = PromoCode::where('code', $code)->active()->first();

            if ($promo && $promo->appliesTo($subtotal)) {
                $promoCode = $promo->code;
                $promoDiscount = $promo->discountFor($subtotal);
            }
        }

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
