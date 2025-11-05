<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function store(Request $request, Chat $chat)
    {
        abort_unless($chat->participants->contains(Auth::id()), 403);
        $request->validate(['body' => 'required|string']);
        Message::create([
            'chat_id' => $chat->id,
            'sender_id' => Auth::id(),
            'body' => $request->body,
        ]);
        event(new MessageSent(Message::latest()->where('chat_id', $chat->id)->first()));
        return back();
    }
}
