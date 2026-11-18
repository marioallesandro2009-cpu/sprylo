<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use Auditable;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'topic', 'order_number', 'message', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /** Opening a message marks it read; that's not an edit worth logging. */
    public array $auditIgnore = ['is_read'];
}
