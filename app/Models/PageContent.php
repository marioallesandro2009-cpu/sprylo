<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageContent extends Model
{
    protected $fillable = ['key', 'page', 'label', 'type', 'value', 'default_value', 'sort_order'];

    /** @return array<string, ?string> key => value (null = pakai default dari Blade) */
    public static function map(): array
    {
        return Cache::rememberForever('page_contents', fn () => static::query()->pluck('value', 'key')->all());
    }

    public static function flush(): void
    {
        Cache::forget('page_contents');
    }
}