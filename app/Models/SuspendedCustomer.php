<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SuspendedCustomer extends Model
{
    use Auditable;

    protected $fillable = [
        'email', 'phone', 'reason', 'suspended_until', 'created_by',
    ];

    protected $casts = [
        'suspended_until' => 'datetime',
    ];

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isExpired(): bool
    {
        return $this->suspended_until !== null && $this->suspended_until->isPast();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('suspended_until')->orWhere('suspended_until', '>', now());
        });
    }

    /**
     * Find the active suspension blocking this email/phone, if any. Both are matched
     * loosely (case-insensitive email, digits-only phone with country/leading-zero
     * prefix stripped) since guest checkout never normalizes what shoppers type in.
     */
    public static function activeMatch(?string $email, ?string $phone): ?self
    {
        $email = $email ? strtolower(trim($email)) : null;
        $phone = $phone ? static::normalizePhone($phone) : null;

        if (! $email && ! $phone) {
            return null;
        }

        return static::query()->active()->get()->first(function (self $suspension) use ($email, $phone) {
            if ($email && $suspension->email && strtolower($suspension->email) === $email) {
                return true;
            }

            if ($phone && $suspension->phone && static::normalizePhone($suspension->phone) === $phone) {
                return true;
            }

            return false;
        });
    }

    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        return preg_replace('/^(62|0)/', '', $digits) ?? $digits;
    }
}
