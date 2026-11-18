<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// update 1: model riwayat pergerakan stok
/**
 * Satu baris = satu perubahan stok. Hanya ditambah, tidak pernah diubah.
 */
class StockMovement extends Model
{
    public const UPDATED_AT = null;

    public const TYPES = [
        'initial' => 'Stok awal',
        'order' => 'Pesanan masuk',
        'return' => 'Pembatalan / pengembalian',
        'reactivate' => 'Pesanan diaktifkan lagi',
        'manual' => 'Edit manual',
    ];

    protected $fillable = [
        'product_id', 'product_variant_id', 'order_id', 'user_id', 'product_name', 'variant_label',
        'type', 'quantity', 'stock_before', 'stock_after', 'reason', 'actor_name',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
        'created_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
