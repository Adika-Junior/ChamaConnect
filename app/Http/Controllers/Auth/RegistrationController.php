<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerificationToken;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function register(RegisterRequest $request, $token)
    {
        $evt = EmailVerificationToken::where('token', $token)->firstOrFail();

        if ($evt->expires_at->isPast()) {
            return response()->json(['message' => 'Invitation expired'], 410);
        }

        // create user in pending state
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $evt->email,
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'status' => 'pending',
        ]);

        $evt->update(['verified_at' => now()]);

        return response()->json(['status' => 'pending', 'user_id' => $user->id], 201);
    }
}
