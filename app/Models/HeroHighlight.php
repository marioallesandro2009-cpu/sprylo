<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroHighlight extends Model
{
    protected $fillable = ['position', 'eyebrow', 'heading', 'image', 'product_id'];

    public const POSITIONS = [
        'purple' => 'Card 1 (top right, purple)',
        'teal' => 'Card 2 (top right, teal)',
        'orange' => 'Card 3 (bottom row, orange)',
        'green' => 'Card 4 (bottom row, green)',
        'black' => 'Card 5 (bottom row, black)',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
