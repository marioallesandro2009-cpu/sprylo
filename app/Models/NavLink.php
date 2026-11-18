<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class NavLink extends Model
{
    use Auditable;

    protected $fillable = ['label', 'url', 'placement', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public const PLACEMENTS = [
        'both' => 'Navbar & mobile menu',
        'nav' => 'Navbar only',
        'drawer' => 'Mobile menu only',
    ];

    /** Internal paths ("/shop") become absolute URLs; external links and "#" are kept as-is. */
    public function resolvedUrl(): string
    {
        return str_starts_with($this->url, '/') ? url($this->url) : $this->url;
    }

    public function isCurrent(): bool
    {
        if (! str_starts_with($this->url, '/')) {
            return false;
        }

        $path = trim($this->url, '/');

        return request()->is($path === '' ? '/' : $path);
    }
}
