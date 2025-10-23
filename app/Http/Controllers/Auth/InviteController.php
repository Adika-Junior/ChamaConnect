<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerificationToken;
use App\Http\Requests\InviteRequest;
use App\Mail\InvitationMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    public function invite(InviteRequest $request)
    {
        $this->authorize('create', User::class);

        $email = $request->input('email');

        // generate token
        $token = Str::random(64);

        $expiresAt = now()->addHours(48);

        $evt = EmailVerificationToken::create([
            'email' => $email,
            'token' => $token,
            'created_by' => Auth::id(),
            'expires_at' => $expiresAt,
        ]);

        // send email
        Mail::to($email)->send(new InvitationMail($evt));

        return response()->json(['status' => 'invited'], 201);
    }
}
