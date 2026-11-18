<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use Auditable;

    protected $fillable = ['question', 'answer', 'sort_order'];
}
