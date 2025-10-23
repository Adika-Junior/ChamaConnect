<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        // TODO: send notification email

        return response()->json(['status' => 'active']);
    }

    public function reject(Request $request, User $user)
    {
        $this->authorize('approve', $user);

        $user->update(['status' => 'inactive']);

        // TODO: send rejection email

        return response()->json(['status' => 'inactive']);
    }
}
