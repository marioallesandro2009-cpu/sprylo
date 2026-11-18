<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use Auditable;

    protected $fillable = ['product_id', 'name', 'rating', 'comment'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
