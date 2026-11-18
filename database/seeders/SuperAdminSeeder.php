<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Creates (or resets the password of) the super admin account.
 *
 * Run on its own with:  php artisan db:seed --class=SuperAdminSeeder
 * Override the defaults with SUPERADMIN_EMAIL / SUPERADMIN_PASSWORD in .env.
 * These defaults are for local development; set a strong password anywhere else.
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPERADMIN_EMAIL', 'superadmin@sprylo.example');
        $password = env('SUPERADMIN_PASSWORD', 'superadmin123');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'solis Super Admin',
                'password' => Hash::make($password),
                'role' => 'super_admin',
                'status' => 'active',
                'status_reason' => null,
                'suspended_until' => null,
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info("Super admin ready: {$email}");
    }
}
