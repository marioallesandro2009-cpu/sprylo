<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasUniqueSlug;

    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 'description',
        'price', 'sale_price', 'stock', 'weight', 'specs', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'integer',
        'specs' => 'array',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function scopeWithRatingStats(Builder $query): Builder
    {
        return $query->withCount('reviews as reviews_count')->withAvg('reviews as rating_avg', 'rating')->withCount('variants');
    }

    public function getHasVariantsAttribute(): bool
    {
        if (array_key_exists('variants_count', $this->attributes)) {
            return (int) $this->attributes['variants_count'] > 0;
        }

        return $this->variants()->exists();
    }

    /**
     * Keeps the product's own price/stock/weight in sync with its variants, so every
     * existing listing/filter/sort query (which reads those columns directly) still works
     * without having to know about variants.
     */
    public function syncFromVariants(): void
    {
        $variants = $this->variants()->get();

        if ($variants->isEmpty()) {
            return;
        }

        $minPrice = $variants->min(fn (ProductVariant $v) => (float) $v->price);
        $minEffective = $variants->min(fn (ProductVariant $v) => (float) ($v->sale_price ?? $v->price));

        $this->update([
            'price' => $minPrice,
            'sale_price' => $minEffective < $minPrice ? $minEffective : null,
            'stock' => $variants->sum('stock'),
            'weight' => $variants->max('weight'),
        ]);
    }

    public function getBadgeAttribute(): ?string
    {
        if (! is_null($this->sale_price)) {
            return 'Sale';
        }

        if ($this->created_at && $this->created_at->gt(now()->subDays(14))) {
            return 'New';
        }

        return null;
    }

    public function getRatingAvgAttribute(): float
    {
        return round((float) ($this->attributes['rating_avg'] ?? $this->reviews()->avg('rating') ?? 0), 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return (int) ($this->attributes['reviews_count'] ?? $this->reviews()->count());
    }

    public function getRatingAttribute(): int
    {
        return (int) round($this->rating_avg);
    }
}
