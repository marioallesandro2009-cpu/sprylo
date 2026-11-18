<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    use Auditable;

    protected $fillable = ['group', 'label', 'url', 'sort_order'];

    public const GROUPS = [
        'help' => 'Let us help you',
        'resources' => 'Resources',
        'company' => 'Get to know us',
    ];
}
