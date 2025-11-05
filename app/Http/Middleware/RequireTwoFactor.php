<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    /**
     * Handle an incoming request.
     * Requires 2FA verification for admin routes if 2FA is enabled for the user
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Only enforce for admins
        if (!$user->isAdmin()) {
            return $next($request);
        }

        // Check if 2FA is enabled
        if (!$user->two_factor_enabled) {
            return $next($request);
        }

        // Check if already verified in this session (within last 30 minutes)
        $verifiedAt = session('2fa_verified_at');
        if ($verifiedAt && now()->diffInMinutes($verifiedAt) < 30) {
            return $next($request);
        }

        // Check if this is a 2FA route (to avoid redirect loop)
        if ($request->routeIs('2fa.*')) {
            return $next($request);
        }

        // Redirect to 2FA verification
        return redirect()->route('2fa.verify')
            ->with('error', 'Please verify your identity with two-factor authentication.');
    }
}

