<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| Routes d'authentification
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('login', function (Request $request) {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // ─── Limitation anti brute-force : 5 essais / minute par email+IP ───
        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Trop de tentatives de connexion. Réessayez dans {$seconds} seconde(s).",
            ]);
        }

        // ─── Seuls les comptes ACTIFS peuvent s'authentifier ───
        $ok = Auth::attempt(
            array_merge($credentials, ['is_active' => 1]),
            $request->boolean('remember')
        );

        if ($ok) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($throttleKey, 60); // compte l'échec (fenêtre 60s)

        return back()
            ->withErrors(['email' => 'Identifiants incorrects ou compte désactivé.'])
            ->onlyInput('email');
    });

});

Route::middleware('auth')->group(function () {

    Route::post('logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

});
