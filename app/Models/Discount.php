<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Discount extends Model
{
    use Auditable;

    public const SCOPES = [
        'product' => 'Product / variant',
        'category' => 'Category',
        'brand' => 'Brand',
    ];

    public const TYPES = [
        'percent' => 'Percent off',
        'amount' => 'Amount off (Rp)',
    ];

    /** Admin enters and reads schedule times in the shop's local time; rows are stored in the app timezone (UTC). */
    public const DISPLAY_TZ = 'Asia/Jakarta';

    protected $fillable = [
        'name', 'scope', 'product_id', 'product_variant_id', 'category_id', 'brand_id',
        'type', 'value', 'starts_at', 'ends_at', 'is_flash', 'quota', 'sold', 'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_flash' => 'boolean',
        'is_active' => 'boolean',
        'quota' => 'integer',
        'sold' => 'integer',
    ];

    /** @var Collection<int, Discount>|null Live discounts keyed by id, memoised for the current request. */
    private static ?Collection $liveMemo = null;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Rules that are in effect right now: switched on, inside their window and not out of quota.
     */
    public function scopeLive(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', $now))
            ->where(fn ($q) => $q->whereNull('quota')->orWhereColumn('sold', '<', 'quota'));
    }

    /**
     * Live discounts keyed by id, loaded once per request so product cards can look up the
     * rule behind a promo price without a query each.
     *
     * @return Collection<int, Discount>
     */
    public static function liveMap(): Collection
    {
        return self::$liveMemo ??= static::live()->get()->keyBy('id');
    }

    public static function forgetMemo(): void
    {
        self::$liveMemo = null;
    }

    public function apply(float $price): float
    {
        $discounted = $this->type === 'percent'
            ? $price * (1 - (float) $this->value / 100)
            : $price - (float) $this->value;

        return max(round($discounted), 0);
    }

    public function appliesTo(Product $product, ?ProductVariant $variant = null): bool
    {
        return match ($this->scope) {
            'product' => (int) $this->product_id === (int) $product->id
                && ($this->product_variant_id === null || (int) $this->product_variant_id === (int) $variant?->id),
            'category' => (int) $this->category_id === (int) $product->category_id,
            'brand' => $this->brand_id !== null && (int) $this->brand_id === (int) $product->brand_id,
            default => false,
        };
    }

    /** Units still sellable at the discounted price, or null when the rule has no quota. */
    public function getRemainingAttribute(): ?int
    {
        return $this->quota === null ? null : max($this->quota - $this->sold, 0);
    }

    /** live | scheduled | ended | sold_out | inactive */
    public function getStatusAttribute(): string
    {
        if (! $this->is_active) {
            return 'inactive';
        }

        if ($this->ends_at && $this->ends_at->lte(now())) {
            return 'ended';
        }

        if ($this->quota !== null && $this->sold >= $this->quota) {
            return 'sold_out';
        }

        if ($this->starts_at && $this->starts_at->gt(now())) {
            return 'scheduled';
        }

        return 'live';
    }

    public function getTargetLabelAttribute(): string
    {
        return match ($this->scope) {
            'product' => ($this->product?->name ?? 'Deleted product').($this->variant ? ' — '.$this->variant->label : ''),
            'category' => 'Category: '.($this->category?->name ?? 'deleted'),
            'brand' => 'Brand: '.($this->brand?->name ?? 'deleted'),
            default => '—',
        };
    }

    public function getValueLabelAttribute(): string
    {
        return $this->type === 'percent'
            ? (float) $this->value.'%'
            : rupiah($this->value);
    }

    public function auditType(): string
    {
        return $this->is_flash ? 'Flash sale' : 'Scheduled discount';
    }
}
