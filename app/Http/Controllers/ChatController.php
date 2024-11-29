<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('chat.index', compact('users'));
    }

    public function show($userId)
    {
        $otherUser = User::findOrFail($userId);
        $messages = Message::where(function($query) use ($userId) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();

        return view('chat.show', compact('messages', 'otherUser'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|exists:users,id'
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }
}