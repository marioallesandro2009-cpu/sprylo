<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageMeta extends Model
{
    protected $table = 'page_meta';

    protected $fillable = ['page_slug', 'title', 'meta_description'];

    public static function forPage(string $pageSlug): ?self
    {
        return static::where('page_slug', $pageSlug)->first();
    }
}
