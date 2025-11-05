<?php

namespace App\Http\Controllers;

use App\Services\TwoFactorService;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorAuthController extends Controller
{
    public function __construct(
        private TwoFactorService $twoFactorService
    ) {
        $this->middleware('auth');
    }

    /**
     * Show 2FA setup page
     */
    public function show()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, '2FA is only available for administrators.');
        }

        return view('auth.2fa.setup', [
            'user' => $user,
            'enabled' => $user->two_factor_enabled,
        ]);
    }

    /**
     * Show 2FA verification page
     */
    public function showVerify()
    {
        $user = Auth::user();
        
        if (!$user->two_factor_enabled) {
            return redirect()->route('dashboard');
        }

        return view('auth.2fa.verify');
    }

    /**
     * Enable 2FA and generate backup codes
     */
    public function enable(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $backupCodes = $this->twoFactorService->enableTwoFactor($user);

        return redirect()->route('2fa.show')
            ->with('status', 'Two-factor authentication enabled successfully.')
            ->with('backup_codes', $backupCodes);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'password' => 'required|current_password',
        ]);

        $this->twoFactorService->disableTwoFactor($user);

        return redirect()->route('2fa.show')
            ->with('status', 'Two-factor authentication disabled successfully.');
    }

    /**
     * Send verification code
     */
    public function sendCode(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->two_factor_enabled) {
            return response()->json(['error' => '2FA is not enabled'], 400);
        }

        $code = $this->twoFactorService->generateCode();
        $this->twoFactorService->storeCode($user, $code);

        // Send notification (SMS and/or email)
        $user->notify(new TwoFactorCodeNotification($code));

        return response()->json([
            'message' => 'Verification code sent to your registered phone number.'
        ]);
    }

    /**
     * Verify code during login
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        
        if ($this->twoFactorService->verifyCode($user, $request->code)) {
            // Store verification in session
            session(['2fa_verified' => true]);
            session(['2fa_verified_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Verification successful'
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Invalid verification code'
        ], 422);
    }

    /**
     * Regenerate backup codes
     */
    public function regenerateBackupCodes(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->two_factor_enabled) {
            return redirect()->route('2fa.show')
                ->with('error', '2FA is not enabled');
        }

        $request->validate([
            'password' => 'required|current_password',
        ]);

        $backupCodes = $this->twoFactorService->generateBackupCodes();
        $user->update(['two_factor_backup_codes' => $backupCodes]);

        return redirect()->route('2fa.show')
            ->with('status', 'Backup codes regenerated successfully.')
            ->with('backup_codes', $backupCodes);
    }
}

