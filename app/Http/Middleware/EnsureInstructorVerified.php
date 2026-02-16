<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureInstructorVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('instructor')->user();

        if (!$user) {
            return redirect()->route('instructor.login');
        }

        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
