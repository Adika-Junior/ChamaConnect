<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chats = Chat::with('participants')->whereHas('participants', function($q){
            $q->where('users.id', Auth::id());
        })->latest()->get();
        return view('chats.index', compact('chats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('chats.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:direct,group',
            'name' => 'nullable|string|max:255',
            'participant_email' => 'required_if:type,direct|nullable|email',
        ]);

        $chat = Chat::create([
            'type' => $validated['type'],
            'name' => $validated['name'] ?? null,
            'created_by' => Auth::id(),
        ]);
        $chat->participants()->attach(Auth::id());

        if ($chat->type === 'direct' && !empty($validated['participant_email'])) {
            if ($user = User::where('email', $validated['participant_email'])->first()) {
                $chat->participants()->syncWithoutDetaching([$user->id]);
            }
        }

        return redirect()->route('chats.show', $chat);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat)
    {
        abort_unless($chat->participants->contains(Auth::id()), 403);
        $chat->load(['messages.sender','participants']);
        return view('chats.show', compact('chat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
