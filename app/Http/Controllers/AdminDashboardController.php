<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function approvals(Request $request)
    {
        abort_unless($request->user() && $request->user()->isAdmin(), 403);

        $pendingUsers = User::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get(['id','name','email','employee_id','phone','created_at']);

        return view('admin.approvals', [
            'pendingUsers' => $pendingUsers,
        ]);
    }
}
