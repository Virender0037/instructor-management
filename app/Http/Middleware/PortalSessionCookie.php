<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PortalSessionCookie
{
    public function handle(Request $request, Closure $next)
    {
        // Superadmin portal
        if ($request->is('superadmin*')) {
            config(['session.cookie' => 'superadmin_session']);
        }

        // Instructor portal + shared instructor auth pages (must use instructor_session)
        if (
            $request->is('instructor*') ||
            $request->is('login') ||
            $request->is('dashboard') ||

            // ✅ Forgot/reset password (both GET+POST routes)
            $request->is('forgot-password') ||
            $request->is('reset-password') ||          // POST /reset-password
            $request->is('reset-password/*') ||        // GET /reset-password/{token}

            // ✅ Email verification routes (GET+POST)
            $request->is('verify-email') ||
            $request->is('verify-email/*') ||
            $request->is('email/verification-notification')
        ) {
            config(['session.cookie' => 'instructor_session']);
        }

        return $next($request);
    }
}
