<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    public const STATUSES = ['awaiting_proof', 'pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];

    protected $fillable = [
        'order_number', 'name', 'email', 'phone', 'address', 'city', 'postal_code', 'notes',
        'requested_delivery_date',
        'latitude', 'longitude',
        'courier_code', 'courier_service_code', 'courier_name', 'courier_service_name', 'shipping_duration',
        'subtotal', 'shipping', 'tax', 'promo_code', 'promo_discount', 'has_insurance', 'insurance_fee', 'total', 'status',
        'payment_method', 'payment_proof_path',
    ];

    protected $casts = [
        'requested_delivery_date' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'tax' => 'decimal:2',
        'promo_discount' => 'decimal:2',
        'has_insurance' => 'boolean',
        'insurance_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'awaiting_proof' => 'bg-secondary-subtle text-secondary',
            'pending' => 'bg-warning-subtle text-warning',
            'paid' => 'bg-info-subtle text-info',
            'processing' => 'bg-primary-subtle text-primary',
            'shipped' => 'bg-dark-subtle text-dark',
            'completed' => 'bg-success-subtle text-success',
            'cancelled' => 'bg-danger-subtle text-danger',
            default => 'bg-secondary-subtle text-secondary',
        };
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'SPR-'.strtoupper(Str::random(6));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }
}
