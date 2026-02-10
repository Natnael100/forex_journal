<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisputeController extends Controller
{
    /**
     * Display listing of analyst disputes.
     */
    public function index()
    {
        $disputes = Dispute::with(['trader', 'subscription', 'resolver'])
            ->where('analyst_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('analyst.disputes.index', compact('disputes'));
    }

    /**
     * Display specific dispute details.
     */
    public function show($id)
    {
        $dispute = Dispute::with(['trader', 'subscription', 'resolver', 'messages.sender'])
            ->where('analyst_id', Auth::id())
            ->findOrFail($id);

        // Mark notifications as read
        \App\Models\Notification::where('user_id', auth()->id())
            ->whereIn('type', ['dispute_message_received', 'dispute_filed', 'dispute_resolved'])
            ->where('data->dispute_id', (int)$id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('analyst.disputes.show', compact('dispute'));
    }
}
