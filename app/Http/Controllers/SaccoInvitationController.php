<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\SaccoInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SaccoInvitationController extends Controller
{
    public function create(Group $group)
    {
        $this->authorize('update', $group);
        return view('sacco.invitations.create', compact('group'));
    }

    public function store(Request $request, Group $group)
    {
        $this->authorize('update', $group);
        $validated = $request->validate([
            'email' => 'nullable|email|required_without:phone',
            'phone' => 'nullable|string|max:50|required_without:email',
        ]);

        $inv = SaccoInvitation::create([
            'group_id' => $group->id,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'token' => Str::random(48),
            'invited_by' => optional($request->user())->id,
            'expires_at' => now()->addDays(7),
        ]);

        if (!empty($inv->email)) {
            Mail::raw("You are invited to join {$group->name}. Click to accept: " . route('sacco.invitations.accept', $inv->token), function ($m) use ($inv) {
                $m->to($inv->email)->subject('Invitation to join group');
            });
        }

        // TODO: send SMS if phone present (use SmsChannel or provider)

        return redirect()->back()->with('status', 'Invitation sent');
    }

    public function accept(string $token)
    {
        $inv = SaccoInvitation::where('token', $token)->firstOrFail();
        if ($inv->expires_at && $inv->expires_at->isPast()) {
            return view('sacco.invitations.accepted', ['error' => 'Invitation has expired', 'group' => null]);
        }

        $inv->accepted_at = now();
        $inv->save();

        return view('sacco.invitations.accepted', ['group' => Group::find($inv->group_id), 'error' => null]);
    }
}


