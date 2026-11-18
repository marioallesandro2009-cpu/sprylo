<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    protected $signature = 'sprylo:super-admin
                            {email? : Email untuk login}
                            {password? : Password (minimal 8 karakter)}
                            {--name=Super Admin : Nama tampilan}
                            {--force : Izinkan mengubah akun yang sudah ada dengan role lain}';

    protected $description = 'Buat atau perbarui akun super admin dari terminal';

    public function handle(): int
    {
        $email = $this->argument('email') ?? $this->ask('Email super admin');
        $password = $this->argument('password') ?? $this->secret('Password (minimal 8 karakter)');

        $validator = Validator::make(
            ['email' => $email, 'password' => $password],
            [
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($user && $user->role !== 'super_admin' && ! $this->option('force')) {
            $this->error("Email ini sudah dipakai akun dengan role '{$user->role}'. Jalankan lagi dengan --force kalau memang mau dijadikan super admin.");

            return self::FAILURE;
        }

        $user ??= new User();

        $user->forceFill([
            'name' => $this->option('name'),
            'email' => $email,
            'password' => $password,
            'role' => 'super_admin',
            'status' => 'active',
            'status_reason' => null,
            'suspended_until' => null,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        $this->info("Super admin siap. Login di /admin/login dengan email: {$email}");

        return self::SUCCESS;
    }
}