<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LoginController extends Controller
{
    /**
     * Maximum login attempts per minute (OWASP recommended: 5)
     */
    private const MAX_ATTEMPTS = 5;
    
    /**
     * Lockout duration in minutes
     */
    private const LOCKOUT_DURATION = 15;

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Rate limiting protection (OWASP A07:2021 - Identification and Authentication Failures)
        $this->checkRateLimit($request);

        // Validate input
        $credentials = $request->validate([
            'email' => 'required|email:rfc,dns',
            'password' => 'required|string|min:8',
            'remember' => 'boolean',
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        // Attempt authentication
        $remember = $request->boolean('remember');
        
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check user status
            if ($user->status === 'pending') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is pending approval. Please contact an administrator.',
                ])->withInput($request->only('email'));
            }
            
            if ($user->status === 'inactive') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact an administrator.',
                ])->withInput($request->only('email'));
            }
            
            if ($user->status === 'suspended') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been suspended. Please contact an administrator.',
                ])->withInput($request->only('email'));
            }

            // Clear rate limiter on successful login
            RateLimiter::clear($this->throttleKey($request));
            
            // Regenerate session to prevent session fixation (OWASP A01:2021 - Broken Access Control)
            $request->session()->regenerate();
            
            // Log successful login
            \Log::info('User logged in', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Check if 2FA is enabled for admin
            if ($user->isAdmin() && $user->two_factor_enabled) {
                // Send 2FA code
                $twoFactorService = app(\App\Services\TwoFactorService::class);
                $code = $twoFactorService->generateCode();
                $twoFactorService->storeCode($user, $code);
                $user->notify(new \App\Notifications\TwoFactorCodeNotification($code));
                
                // Redirect to 2FA verification
                return redirect()->route('2fa.verify-page')
                    ->with('status', 'Please enter the verification code sent to your phone.');
            }

            return redirect()->intended(route('dashboard'));
        }

        // Increment login attempts on failure
        RateLimiter::hit($this->throttleKey($request), self::LOCKOUT_DURATION * 60);

        $remaining = RateLimiter::remaining($this->throttleKey($request), self::MAX_ATTEMPTS);
        
        // Log failed login attempt (OWASP A09:2021 - Security Logging and Monitoring Failures)
        \Log::warning('Failed login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'remaining_attempts' => $remaining,
        ]);

        // Generic error message to prevent user enumeration (OWASP A07:2021)
        throw ValidationException::withMessages([
            'email' => 'These credentials do not match our records.',
        ]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        // Log logout
        if (Auth::check()) {
            \Log::info('User logged out', [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);
        }

        // Logout user
        Auth::logout();

        // Invalidate session (OWASP A07:2021)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'You have been logged out successfully.');
    }

    /**
     * Check rate limit for login attempts
     * 
     * OWASP A07:2021 - Identification and Authentication Failures
     */
    private function checkRateLimit(Request $request): void
    {
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);
            
            \Log::warning('Login rate limit exceeded', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'seconds_until_retry' => $seconds,
            ]);

            throw ValidationException::withMessages([
                'email' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.',
            ]);
        }
    }

    /**
     * Get throttle key for rate limiting
     */
    private function throttleKey(Request $request): string
    {
        return 'login:' . $request->ip() . ':' . strtolower($request->input('email'));
    }
}

