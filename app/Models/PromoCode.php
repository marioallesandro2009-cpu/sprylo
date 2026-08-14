<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_subtotal', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_subtotal' => 'decimal:2',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function appliesTo(float $subtotal): bool
    {
        return is_null($this->min_subtotal) || $subtotal >= (float) $this->min_subtotal;
    }

    public function discountFor(float $subtotal): float
    {
        if (! $this->appliesTo($subtotal)) {
            return 0.0;
        }

        return $this->type === 'percent'
            ? round($subtotal * ((float) $this->value / 100))
            : min((float) $this->value, $subtotal);
    }
}
