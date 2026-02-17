<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorAuthenticatedSessionController extends Controller
{
   public function store(Request $request)
{
    // Valideer invoer
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $remember = $request->boolean('remember');

    // Probeer in te loggen
    if (!Auth::guard('instructor')->attempt($credentials, $remember)) {
        return back()->withErrors([
            'email' => 'Ongeldige inloggegevens.'
        ])->onlyInput('email');
    }

    $request->session()->regenerate();

    $user = Auth::guard('instructor')->user();

    // Controleer of rol correct is
    if ($user->role !== 'instructor') {
        Auth::guard('instructor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'email' => 'U bent niet bevoegd om in te loggen via dit portaal.'
        ]);
    }

    // Controleer of account actief is
    if (!$user->status) {
        Auth::guard('instructor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'email' => 'Uw account is uitgeschakeld.'
        ]);
    }

    // Redirect naar dashboard
    return redirect()->intended(route('instructor.dashboard'));
}


    public function destroy(Request $request)
    {   
        Auth::guard('instructor')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('instructor.login');
    }
}
