<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use Auditable;

    protected $fillable = ['product_id', 'image', 'sort_order'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function auditType(): string
    {
        return 'Product image';
    }

    public function auditLabel(): string
    {
        return $this->product?->name ?? 'Product #'.$this->product_id;
    }
}
