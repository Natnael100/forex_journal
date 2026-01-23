<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $conversations = Conversation::query()
            ->where('analyst_id', $user->id)
            ->orWhere('trader_id', $user->id)
            ->with(['analyst', 'trader', 'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->get()
            ->sortByDesc(function($conversation) {
                return $conversation->messages->first()?->created_at ?? $conversation->created_at;
            });

        return view('conversations.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->load(['analyst', 'trader', 'messages.sender']);

        // Mark messages as read
        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('conversations.show', compact('conversation'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $recipientId = $request->recipient_id;

        // Determine who is who
        // Assuming current user initiates, but we need to know roles.
        // Simplified: Check if exists regardless of who is analyst/trader in the pair logic
        // But our schema has strict analyst_id and trader_id
        
        // We need to resolve roles. 
        // If Auth user is Analyst, recipient should be Trader.
        // If Auth user is Trader, recipient should be Analyst.
        
        $analystId = null;
        $traderId = null;

        if ($user->hasRole('analyst')) {
            $analystId = $user->id;
            $traderId = $recipientId;
        } elseif ($user->hasRole('trader')) {
            $analystId = $recipientId;
            $traderId = $user->id;
        } else {
             // Admin or other?
             return back()->with('error', 'Only Analysts and Traders can start conversations.');
        }

        $conversation = Conversation::firstOrCreate(
            ['analyst_id' => $analystId, 'trader_id' => $traderId]
        );

        return redirect()->route('conversations.show', $conversation);
    }
}
