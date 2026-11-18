<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            Log::warning('Admin login failed: invalid credentials', [
                'email' => $credentials['email'],
                'ip' => $request->ip(),
            ]);

            $this->recordFailedLogin($credentials['email'], 'Failed admin sign-in for');

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        if (! Auth::user()->isAdmin()) {
            $userId = Auth::id();

            Auth::logout();

            RateLimiter::hit($throttleKey, 60);

            Log::warning('Admin login blocked: authenticated user is not an admin', [
                'user_id' => $userId,
                'email' => $credentials['email'],
                'ip' => $request->ip(),
            ]);

            $this->recordFailedLogin($credentials['email'], 'Non-admin account tried to sign in to the admin panel:');

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        Log::info('Admin login succeeded', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
        ]);

        Audit::record('login', Auth::user()->name.' signed in', Auth::user());

        return redirect()->intended(route('admin.dashboard'));
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }

    /** A failed attempt is attributed to the matching admin account when the email exists, so it shows up on their row. */
    private function recordFailedLogin(string $email, string $prefix): void
    {
        $account = User::where('email', $email)->first();

        Audit::record(
            'login_failed',
            "{$prefix} {$email}",
            $account && $account->isAdmin() ? $account : null,
            null,
            null,
            ['user_email' => $account?->email ?? $email, 'user_name' => $account?->name],
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        if ($user = Auth::user()) {
            Audit::record('logout', $user->name.' signed out', $user);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
