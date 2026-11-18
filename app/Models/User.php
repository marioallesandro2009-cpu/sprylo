<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Auditable;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'status_reason',
        'suspended_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'suspended_until' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin'], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isBanned(): bool
    {
        return $this->status === 'banned';
    }

    public function isSuspended(): bool
    {
        if ($this->status !== 'suspended') {
            return false;
        }

        if ($this->suspended_until && $this->suspended_until->isPast()) {
            return false;
        }

        return true;
    }

    public function isActive(): bool
    {
        return ! $this->isBanned() && ! $this->isSuspended();
    }

    public function auditType(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super admin account',
            'admin' => 'Admin account',
            default => 'Customer',
        };
    }

    public function auditLabel(): string
    {
        return $this->name.' ('.$this->email.')';
    }
}
