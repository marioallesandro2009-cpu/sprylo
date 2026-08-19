<?php

namespace App\Models;

use App\Support\PromoTheme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoBanner extends Model
{
    protected $fillable = ['product_id', 'label', 'theme', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const THEMES = PromoTheme::OPTIONS;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->product || $this->product->sale_price === null || (float) $this->product->price <= 0) {
            return null;
        }

        return (int) round((1 - $this->product->sale_price / $this->product->price) * 100);
    }
}
