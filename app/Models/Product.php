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
        'price', 'sale_price', 'stock', 'weight', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'integer',
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

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function scopeWithRatingStats(Builder $query): Builder
    {
        return $query->withCount('reviews as reviews_count')->withAvg('reviews as rating_avg', 'rating');
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
