<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
  
    public function create(): View
    {
        return view('auth.login-instructor');
    }

    public function store(LoginRequest $request): RedirectResponse
{
    // Authenticatie
    $request->authenticate();

    // Sessieregeneratie na succesvolle login
    $request->session()->regenerate();

    $loginAs = $request->input('login_as');
    $user = $request->user();

    // Controleer of account actief is
    if (!$user->status) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'email' => 'Uw account is uitgeschakeld. Neem contact op met de beheerder.',
        ]);
    }

    // Controleer of gebruiker mag inloggen op dit portaal
    if ($loginAs && $user->role !== $loginAs) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'email' => 'U bent niet bevoegd om in te loggen via dit portaal.',
        ]);
    }

    // Redirect naar het juiste dashboard
    return redirect()->intended(
        $user->role === 'superadmin'
            ? '/superadmin/dashboard'
            : '/instructor/dashboard'
    );
}


    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/superadmin/login');
    }

}
