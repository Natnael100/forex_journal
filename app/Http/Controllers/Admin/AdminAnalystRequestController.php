<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalystRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAnalystRequestController extends Controller
{
    public function index()
    {
        $requests = AnalystRequest::with(['trader', 'reviewer'])
            ->latest()
            ->paginate(20);
            
        $analysts = User::role('analyst')->where('verification_status', 'verified')->get();

        return view('admin.analyst-requests.index', compact('requests', 'analysts'));
    }

    public function update(Request $request, AnalystRequest $analystRequest)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'analyst_id' => 'nullable|exists:users,id',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $analystRequest->update([
            'status' => $request->status,
            'analyst_id' => $request->analyst_id ?: null,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $message = $request->status === 'approved' 
            ? 'Request approved. Trader has been notified to sign the consent form.' 
            : 'Request rejected.';

        // Notify Trader
        \App\Models\Notification::create([
            'user_id' => $analystRequest->trader_id,
            'type' => 'analyst_request_' . $request->status,
            'data' => [
                'message' => $request->status === 'approved' 
                    ? 'Your Analyst Request was APPROVED. Action Required: Sign Agreement.'
                    : 'Your Analyst Request was updated (Rejected).',
                'link' => route('trader.analyst-request.create'),
            ]
        ]);

        return back()->with('success', $message);
    }
}
