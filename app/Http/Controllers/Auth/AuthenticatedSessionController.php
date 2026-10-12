<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
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

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $user = Auth::user();

        // A suspended/banned account is logged out immediately rather than left with an
        // active session, so it can't reach checkout by staying signed in from before.
        if (! $user->isActive()) {
            Auth::logout();
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => $this->suspensionMessage($user),
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        // Not ->intended(): the admin and storefront logins share this same guard/session,
        // so a stale "intended URL" left behind by an earlier attempt to open an admin page
        // would otherwise bounce a freshly-logged-in customer straight into a 403.
        return redirect()->route('home');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    protected function suspensionMessage($user): string
    {
        if ($user->isBanned()) {
            return 'This account has been banned.'.($user->status_reason ? " Reason: {$user->status_reason}" : '').' Please contact support if you believe this is a mistake.';
        }

        $until = $user->suspended_until ? " until {$user->suspended_until->format('d M Y H:i')}" : '';

        return "This account is suspended{$until}.".($user->status_reason ? " Reason: {$user->status_reason}" : '').' Please contact support if you believe this is a mistake.';
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }
}
