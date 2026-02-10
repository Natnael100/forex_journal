<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display a list of all conversations for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get conversations where user is either analyst or trader
        $conversations = Conversation::where('analyst_id', $user->id)
            ->orWhere('trader_id', $user->id)
            ->with(['analyst', 'trader', 'lastMessage'])
            ->get()
            ->map(function ($conversation) use ($user) {
                $conversation->other_user = $conversation->getOtherParticipant($user->id);
                $conversation->unread_count = $conversation->getUnreadCountFor($user->id);
                return $conversation;
            })
            ->sortByDesc(function ($conversation) {
                return $conversation->lastMessage?->created_at ?? $conversation->created_at;
            });

        return view('chat.index', compact('conversations'));
    }

    /**
     * Display a specific conversation with messages.
     */
    public function show($conversationId)
    {
        $user = Auth::user();
        
        $conversation = Conversation::with(['analyst', 'trader', 'messages.sender'])
            ->findOrFail($conversationId);

        // Verify user is part of this conversation
        if ($conversation->analyst_id !== $user->id && $conversation->trader_id !== $user->id) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        // Mark messages as read
        $conversation->markAsReadFor($user->id);

        $otherUser = $conversation->getOtherParticipant($user->id);

        return view('chat.show', compact('conversation', 'otherUser'));
    }

    /**
     * Send a new message in a conversation.
     */
    public function store(Request $request, $conversationId)
    {
        $user = Auth::user();
        
        $conversation = Conversation::findOrFail($conversationId);

        // Verify user is part of this conversation
        if ($conversation->analyst_id !== $user->id && $conversation->trader_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'content' => $validated['content'],
            'is_read' => false,
        ]);

        // Return JSON for AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
            ]);
        }

        return redirect()->route('chat.show', $conversation->id);
    }

    /**
     * Fetch new messages (for AJAX polling).
     */
    public function fetchMessages($conversationId, Request $request)
    {
        $user = Auth::user();
        $conversation = Conversation::findOrFail($conversationId);

        // Verify access
        if ($conversation->analyst_id !== $user->id && $conversation->trader_id !== $user->id) {
            abort(403);
        }

        $lastMessageId = $request->query('last_message_id', 0);

        $newMessages = $conversation->messages()
            ->where('id', '>', $lastMessageId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark new messages from other user as read
        $conversation->markAsReadFor($user->id);

        return response()->json([
            'messages' => $newMessages,
            'unread_count' => $this->getTotalUnreadCount($user->id),
        ]);
    }

    /**
     * Get total unread message count for the navbar badge.
     */
    public function getUnreadCount()
    {
        $userId = Auth::id();
        $count = $this->getTotalUnreadCount($userId);
        
        return response()->json(['count' => $count]);
    }

    /**
     * Helper: Calculate total unread messages across all conversations.
     */
    private function getTotalUnreadCount($userId): int
    {
        return Message::whereHas('conversation', function ($query) use ($userId) {
            $query->where('analyst_id', $userId)
                  ->orWhere('trader_id', $userId);
        })
        ->where('sender_id', '!=', $userId)
        ->where('is_read', false)
        ->count();
    }

    /**
     * Create or retrieve a conversation with a specific user.
     */
    public function startConversation(Request $request)
    {
        $user = Auth::user();
        $targetUserId = $request->input('user_id');
        $targetUser = User::findOrFail($targetUserId);

        // Determine roles (analyst/trader)
        $isUserAnalyst = $user->hasRole('analyst');
        $isTargetAnalyst = $targetUser->hasRole('analyst');

        // Ensure one is analyst and one is trader
        if ($isUserAnalyst === $isTargetAnalyst) {
            return redirect()->back()->withErrors(['error' => 'Conversations are only allowed between analysts and traders.']);
        }

        // Find or create conversation
        $analystId = $isUserAnalyst ? $user->id : $targetUserId;
        $traderId = $isUserAnalyst ? $targetUserId : $user->id;

        $conversation = Conversation::firstOrCreate([
            'analyst_id' => $analystId,
            'trader_id' => $traderId,
        ]);

        return redirect()->route('chat.show', $conversation->id);
    }
}
