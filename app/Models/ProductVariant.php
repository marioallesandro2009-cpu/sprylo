<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
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

    public function getDisplayPriceAttribute(): string
    {
        return (string) ($this->sale_price ?? $this->price);
    }
}
