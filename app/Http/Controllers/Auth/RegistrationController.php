<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerificationToken;
use App\Models\Group;
use App\Models\GroupApplication;
use App\Models\Campaign;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function show($token)
    {
        $evt = EmailVerificationToken::where('token', $token)->first();
        
        if (!$evt) {
            return view('auth.register', ['token' => null, 'error' => 'Invalid invitation token']);
        }
        
        if ($evt->expires_at->isPast()) {
            return view('auth.register', ['token' => null, 'error' => 'Invitation has expired']);
        }
        
        if ($evt->verified_at) {
            return view('auth.register', ['token' => null, 'error' => 'This invitation has already been used']);
        }
        
        // Get available SACCOs for selection
        $availableSaccos = Group::where('type', 'sacco')
            ->where('is_public', true)
            ->where('accepting_applications', true)
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'location', 'current_members']);
        
        return view('auth.register', [
            'token' => $token, 
            'email' => $evt->email,
            'availableSaccos' => $availableSaccos
        ]);
    }

    public function getAvailableSaccos()
    {
        $saccos = Group::where('type', 'sacco')
            ->where('is_public', true)
            ->where('accepting_applications', true)
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'location', 'current_members']);
        
        return response()->json($saccos);
    }

    public function register(RegisterRequest $request, $token)
    {
        $evt = EmailVerificationToken::where('token', $token)->firstOrFail();

        if ($evt->expires_at->isPast()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invitation expired'], 410);
            }
            return back()->withErrors(['token' => 'Invitation has expired. Please request a new invitation.'])->withInput();
        }
        
        if ($evt->verified_at) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invitation already used'], 409);
            }
            return back()->withErrors(['token' => 'This invitation has already been used.'])->withInput();
        }

        DB::beginTransaction();
        try {
            // Create user in pending state
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $evt->email,
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->input('password')),
                'status' => 'pending',
            ]);

            $userType = $request->input('user_type');
            
            // Handle different user types
            if ($userType === 'sacco_member') {
                // Create group application for SACCO
                $group = Group::findOrFail($request->input('group_id'));
                
                if (!$group->is_public || !$group->accepting_applications) {
                    throw new \Exception('This SACCO is not accepting applications.');
                }
                
                GroupApplication::create([
                    'group_id' => $group->id,
                    'user_id' => $user->id,
                    'application_data' => [
                        'phone' => $request->input('phone'),
                        'registered_at' => now()->toDateTimeString(),
                    ],
                    'reason' => 'Registration via invitation link',
                    'status' => 'pending',
                ]);
                
            } elseif ($userType === 'chama') {
                // Create a chama group (pending approval)
                $group = Group::create([
                    'name' => $request->input('chama_name'),
                    'type' => 'committee', // Chamas are typically committees
                    'description' => $request->input('chama_description'),
                    'location' => $request->input('chama_location'),
                    'created_by' => $user->id,
                    'is_public' => false,
                    'accepting_applications' => true,
                    'current_members' => 1,
                ]);
                
                // Add creator as admin member (will be active once user is approved)
                $group->members()->attach($user->id, [
                    'role' => 'admin',
                    'total_contributed' => 0,
                    'joined_at' => now(),
                ]);
                
            } elseif ($userType === 'fundraiser') {
                // Create a fundraiser campaign (pending approval)
                $campaign = Campaign::create([
                    'title' => $request->input('campaign_title'),
                    'description' => $request->input('campaign_description'),
                    'goal_amount' => $request->input('campaign_goal_amount'),
                    'current_amount' => 0,
                    'organizer_id' => $user->id,
                    'status' => 'pending',
                    'is_public' => true,
                    'allow_anonymous' => false,
                ]);
            }

            $evt->update(['verified_at' => now()]);
            
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'pending', 
                    'user_id' => $user->id, 
                    'message' => 'Registration successful. Your account is pending approval.'
                ], 201);
            }
            
            return redirect()->route('login')->with('status', 'Registration successful! Your account is pending administrator approval. You will be notified once activated.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
