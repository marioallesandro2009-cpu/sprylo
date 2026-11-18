<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row of the admin audit trail. Append-only: nothing in the app updates or deletes these.
 */
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    public const ACTIONS = [
        'login' => 'Login',
        'logout' => 'Logout',
        'login_failed' => 'Failed login',
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
    ];

    protected $fillable = [
        'user_id', 'user_name', 'user_email', 'user_role', 'action',
        'subject_type', 'subject_id', 'subject_label', 'description', 'changes',
        'ip_address', 'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute(): string
    {
        return self::ACTIONS[$this->action] ?? ucfirst($this->action);
    }

    /** Bootstrap badge colour for the action. */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created' => 'success',
            'updated' => 'primary',
            'deleted', 'login_failed' => 'danger',
            default => 'secondary',
        };
    }
}
