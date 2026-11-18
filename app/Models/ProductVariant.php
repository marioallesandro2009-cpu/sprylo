<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Support\Pricing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use Auditable;

    protected $fillable = [
        'product_id', 'label', 'sku', 'price', 'sale_price', 'has_warranty', 'stock', 'weight', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'has_warranty' => 'boolean',
        'stock' => 'integer',
        'weight' => 'integer',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @see Product::getSalePriceAttribute() */
    public function getSalePriceAttribute($value)
    {
        return Pricing::effectiveSalePrice($value, $this->attributes['promo_price'] ?? null, $this->attributes['price'] ?? null);
    }

    public function getPromoAttribute(): ?Discount
    {
        return Pricing::promoFor($this->attributes['promo_discount_id'] ?? null, $this->attributes['promo_price'] ?? null, $this->getRawOriginal('sale_price'));
    }

    public function getDisplayPriceAttribute(): string
    {
        return (string) ($this->sale_price ?? $this->price);
    }

    public function auditType(): string
    {
        return 'Product variant';
    }

    public function auditLabel(): string
    {
        return ($this->product?->name ?? 'Product #'.$this->product_id).' — '.$this->label;
    }
}
