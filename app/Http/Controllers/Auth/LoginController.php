<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::lower($credentials['email']).'|'.$request->ip();
        $maxAttempts = (int) Setting::get('login_max_attempts', 5);
        $lockoutMinutes = (int) Setting::get('login_lockout_minutes', 15);

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Demasiados intentos fallidos. Intenta de nuevo en ".ceil($seconds / 60)." minuto(s).",
            ]);
        }

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (! $user || ! $user->is_active || ! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, $lockoutMinutes * 60);

            throw ValidationException::withMessages([
                'email' => $user && ! $user->is_active
                    ? 'Esta cuenta se encuentra desactivada.'
                    : 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        activity('auth')
            ->causedBy($user)
            ->event('login')
            ->log('Inicio de sesión');

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        if ($user = Auth::user()) {
            activity('auth')->causedBy($user)->event('logout')->log('Cierre de sesión');
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
