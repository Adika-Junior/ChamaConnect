<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // Show forgot password form
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // Send reset link
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            // Don't reveal if email exists (security best practice)
            return back()->with('status', 'If an account exists with that email, a reset link has been sent.');
        }

        // Generate reset token
        $token = Str::random(64);
        
        // Store in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Log the reset link for development (in production, send email)
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);
        Log::info('Password reset link generated', [
            'email' => $request->email,
            'url' => $resetUrl
        ]);

        return back()->with('status', 'Password reset link: ' . $resetUrl);
    }

    // Show reset password form
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    // Reset password
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetToken) {
            return back()->withErrors(['email' => 'Invalid reset token.']);
        }

        if (!Hash::check($request->token, $resetToken->token)) {
            return back()->withErrors(['email' => 'Invalid reset token.']);
        }

        // Check if token expired (1 hour)
        if (now()->diffInMinutes($resetToken->created_at) > 60) {
            return back()->withErrors(['email' => 'Reset token has expired. Please request a new one.']);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Delete the reset token
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            Log::info('Password reset successful', ['email' => $request->email]);

            return redirect()->route('login')->with('status', 'Your password has been reset successfully.');
        }

        return back()->withErrors(['email' => 'Unable to reset password.']);
    }
}
