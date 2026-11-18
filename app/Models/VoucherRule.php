<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VoucherRule extends Model
{
    use Auditable;

    public const TRIGGERS = [
        'first_order' => 'After a customer\'s first order',
        'order_milestone' => 'Every Nth order (loyalty)',
    ];

    public array $auditIgnore = ['issued_count'];

    protected $fillable = ['name', 'trigger', 'milestone_every', 'type', 'value', 'min_subtotal', 'valid_days', 'is_active', 'issued_count'];

    protected $casts = [
        'value' => 'decimal:2',
        'min_subtotal' => 'decimal:2',
        'milestone_every' => 'integer',
        'valid_days' => 'integer',
        'is_active' => 'boolean',
        'issued_count' => 'integer',
    ];

    public function vouchers(): HasMany
    {
        return $this->hasMany(PromoCode::class);
    }

    public function getTriggerLabelAttribute(): string
    {
        $label = self::TRIGGERS[$this->trigger] ?? $this->trigger;

        return $this->trigger === 'order_milestone' && $this->milestone_every
            ? 'Every '.$this->milestone_every.' orders (loyalty)'
            : $label;
    }

    public function getValueLabelAttribute(): string
    {
        return $this->type === 'percent' ? (float) $this->value.'% off' : rupiah($this->value).' off';
    }
}
