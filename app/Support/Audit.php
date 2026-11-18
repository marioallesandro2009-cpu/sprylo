<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;

/**
 * Writes the admin audit trail. Model changes are recorded automatically by the Auditable
 * trait; login/logout are recorded by the admin auth controller.
 */
class Audit
{
    /** Attributes that must never be written to the log in clear text. */
    private const SECRET_FIELDS = ['password', 'remember_token'];

    private const MAX_VALUE_LENGTH = 300;

    /**
     * The admin performing the current request, or null when the change isn't an admin action
     * (storefront checkout, console commands, seeders, queue jobs).
     */
    public static function actor(): ?User
    {
        $request = request();

        // Sign-in and sign-out record themselves; side effects such as a password rehash must not look like edits.
        if (! $request || ! $request->routeIs('admin.*') || $request->routeIs('admin.login', 'admin.logout')) {
            return null;
        }

        $user = Auth::user();

        return $user instanceof User && $user->isAdmin() ? $user : null;
    }

    /**
     * @param  array<string, array{0: mixed, 1: mixed}>|null  $changes  field => [old, new]
     */
    public static function record(string $action, string $description, ?User $user = null, ?Model $subject = null, ?array $changes = null, array $extra = []): void
    {
        try {
            $request = request();

            AuditLog::create(array_merge([
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'user_email' => $user?->email,
                'user_role' => $user?->role,
                'action' => $action,
                'subject_type' => $subject && method_exists($subject, 'auditType') ? $subject->auditType() : null,
                'subject_id' => $subject?->getKey(),
                'subject_label' => $subject && method_exists($subject, 'auditLabel') ? Str::limit($subject->auditLabel(), 250, '…') : null,
                'description' => Str::limit($description, 1000, '…'),
                'changes' => $changes ?: null,
                'ip_address' => $request?->ip(),
                'user_agent' => $request ? Str::limit((string) $request->userAgent(), 250, '') : null,
            ], $extra));
        } catch (Throwable $e) {
            // An audit failure must never block the admin's actual action.
            report($e);
        }
    }

    /** A single value in a form that is safe and compact to store/show. */
    public static function displayValue(string $field, mixed $value): mixed
    {
        if (in_array($field, self::SECRET_FIELDS, true)) {
            return '••••••';
        }

        if ($value === null || is_bool($value) || is_int($value) || is_float($value)) {
            return $value;
        }

        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return Str::limit((string) $value, self::MAX_VALUE_LENGTH, '…');
    }
}
