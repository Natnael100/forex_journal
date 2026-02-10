<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\DisputeMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DisputeMessageController extends Controller
{
    /**
     * Store a message in the dispute chat.
     */
    public function store(Request $request, Dispute $dispute)
    {
        // Authorization: Trader, Analyst, or Admin
        $user = Auth::user();
        $isAuthorized = $user->hasRole('admin') || 
                        $dispute->trader_id === $user->id || 
                        $dispute->analyst_id === $user->id;

        if (!$isAuthorized) {
            abort(403);
        }

        $request->validate([
            'content' => 'required_without:attachment|string|max:2000',
            'attachment' => 'nullable|image|max:5120', // Max 5MB images
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('disputes/evidence', 'public');
        }

        $message = $dispute->messages()->create([
            'user_id' => $user->id,
            'content' => $request->content,
            'attachment' => $attachmentPath,
        ]);

        // Notify other participants
        $participants = array_unique([$dispute->trader_id, $dispute->analyst_id]);
        
        // Always include admins
        $admins = \App\Models\User::role('admin')->pluck('id')->toArray();
        $notifyUsers = array_merge($participants, $admins);

        foreach ($notifyUsers as $notifyUserId) {
            if ($notifyUserId != $user->id) {
                \App\Models\Notification::create([
                    'user_id' => $notifyUserId,
                    'type' => 'dispute_message_received',
                    'title' => 'New Dispute Message',
                    'message' => "New message in Dispute #{$dispute->id} from {$user->name}",
                    'data' => [
                        'dispute_id' => $dispute->id,
                        'message_id' => $message->id,
                    ],
                ]);
            }
        }

        return back()->with('success', 'Message sent.');
    }
}
