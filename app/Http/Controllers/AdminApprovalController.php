<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserApprovedNotification;
use App\Notifications\UserRejectedNotification;
use Illuminate\Http\Request;

class AdminApprovalController extends Controller
{
    public function approve(Request $request, User $user)
    {
        $this->authorize('approve', $user);

        $user->update([
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
        ]);

        // Send notification email
        $user->notify(new UserApprovedNotification($request->user()->name));

        return response()->json(['status' => 'active']);
    }

    public function reject(Request $request, User $user)
    {
        $this->authorize('approve', $user);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $user->update(['status' => 'inactive']);

        // Send rejection email
        $user->notify(new UserRejectedNotification($validated['reason'] ?? null));

        return response()->json(['status' => 'inactive']);
    }
}
