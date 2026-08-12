<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    protected $fillable = ['group', 'label', 'url', 'sort_order'];

    public const GROUPS = [
        'help' => 'Let us help you',
        'resources' => 'Resources',
        'company' => 'Get to know us',
    ];
}
