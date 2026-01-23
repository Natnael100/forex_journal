<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        // Simple authorization check
        if ($conversation->analyst_id !== Auth::id() && $conversation->trader_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'content' => $request->content,
        ]);

        // If generic AJAX request, return JSON
        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $message->load('sender')
            ]);
        }

        return back();
    }

    public function poll(Request $request, Conversation $conversation)
    {
        if ($conversation->analyst_id !== Auth::id() && $conversation->trader_id !== Auth::id()) {
            abort(403);
        }

        $lastId = $request->query('last_id', 0);

        $messages = $conversation->messages()
            ->where('id', '>', $lastId)
            ->with('sender')
            ->oldest()
            ->get();
            
        // Mark as read if they are not from me
        if ($messages->isNotEmpty()) {
             $conversation->messages()
                ->whereIn('id', $messages->pluck('id'))
                ->where('sender_id', '!=', Auth::id())
                ->update(['is_read' => true]);
        }

        return response()->json([
            'messages' => $messages
        ]);
    }
}
