<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use Auditable;

    public const STATUSES = ['awaiting_proof', 'pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];

    protected $fillable = [
        'order_number', 'name', 'email', 'phone', 'address', 'city', 'postal_code', 'notes',
        'invoice_number', 'invoiced_at',
        'requested_delivery_date', 'requested_delivery_slot',
        'latitude', 'longitude',
        'courier_code', 'courier_service_code', 'courier_name', 'courier_service_name', 'shipping_duration',
        'subtotal', 'shipping', 'tax', 'promo_code', 'promo_discount', 'has_insurance', 'insurance_fee', 'total', 'status',
        'payment_method', 'payment_proof_path',
    ];

    /** The issue time is bookkeeping; the invoice number itself is worth showing in the audit trail. */
    public array $auditIgnore = ['invoiced_at'];

    protected $casts = [
        'invoiced_at' => 'datetime',
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

    /**
     * The proof is stored as a root-relative "/storage/..." path, which breaks when the site lives under a
     * sub-folder (e.g. XAMPP's /sprylo/public); asset() builds the URL from the address actually in use.
     */
    public function getPaymentProofUrlAttribute(): ?string
    {
        if (! $this->payment_proof_path) {
            return null;
        }

        return asset(ltrim((string) parse_url($this->payment_proof_path, PHP_URL_PATH), '/'));
    }

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

    /** Orders are referred to by number in the audit log, with the customer for context. */
    public function auditLabel(): string
    {
        return $this->order_number.' ('.$this->name.')';
    }

    /** "20 Oct 2026 · Morning (09:00 – 12:00)" — what the customer asked for, or null when no date was picked. */
    public function getDeliveryScheduleAttribute(): ?string
    {
        if (! $this->requested_delivery_date) {
            return null;
        }

        $slot = \App\Support\DeliverySlots::label($this->requested_delivery_slot);

        return $this->requested_delivery_date->format('d M Y').($slot ? ' · '.$slot : '');
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'SPR-'.strtoupper(Str::random(6));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }
}
