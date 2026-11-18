<?php

namespace App\Support;

use App\Models\Discount;

/**
 * Shared price rules for products and variants, so both models fold the scheduled promo into
 * their sale price the same way.
 */
class Pricing
{
    /**
     * The lower of the manual sale price and the promo price, or null when neither undercuts the list price.
     */
    public static function effectiveSalePrice(mixed $manualSale, mixed $promoPrice, mixed $price): ?string
    {
        $candidates = array_filter([$manualSale, $promoPrice], fn ($value) => $value !== null);

        if ($candidates === []) {
            return null;
        }

        $best = (float) min($candidates);

        if ($price !== null && $best >= (float) $price) {
            return null;
        }

        return number_format($best, 2, '.', '');
    }

    /**
     * The live discount behind a promo price — only when that promo is what is actually
     * being charged (a lower manual sale price takes precedence over it).
     */
    public static function promoFor(mixed $discountId, mixed $promoPrice, mixed $manualSale): ?Discount
    {
        if ($discountId === null || $promoPrice === null) {
            return null;
        }

        if ($manualSale !== null && (float) $manualSale < (float) $promoPrice) {
            return null;
        }

        return Discount::liveMap()->get((int) $discountId);
    }
}
