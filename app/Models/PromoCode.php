<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use Auditable;

    protected $fillable = [
        'code', 'type', 'value', 'min_subtotal', 'expires_at', 'is_active',
        'voucher_rule_id', 'issued_to_email', 'usage_limit', 'used_count',
    ];

    /** Counting a redemption isn't an admin edit. */
    public array $auditIgnore = ['used_count'];

    protected $casts = [
        'value' => 'decimal:2',
        'min_subtotal' => 'decimal:2',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', now()->toDateString()))
            ->where(fn ($q) => $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit'));
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
