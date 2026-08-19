<?php

namespace App\Models;

use App\Support\PromoTheme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroSlide extends Model
{
    protected $fillable = [
        'size', 'theme', 'eyebrow', 'heading', 'description',
        'cta_label', 'image', 'product_id', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const SIZES = [
        'large' => 'Large (featured card)',
        'small' => 'Small (grid card)',
    ];

    public const THEMES = PromoTheme::OPTIONS;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
