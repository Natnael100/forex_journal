<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Models\AnalystRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TraderAnalystRequestController extends Controller
{
    public function create()
    {
        $trader = Auth::user();
        
        // Check for existing pending/approved requests
        $existingRequest = AnalystRequest::where('trader_id', $trader->id)
            ->whereIn('status', [AnalystRequest::STATUS_PENDING, AnalystRequest::STATUS_APPROVED, AnalystRequest::STATUS_REVIEWED])
            ->first();

        // Check for existing active assignment
        $activeAssignment = $trader->analystAssignments()->first();

        return view('trader.analyst-request.create', compact('existingRequest', 'activeAssignment'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'motivation' => 'required|string|max:1000',
        ]);

        $trader = Auth::user();

        // Double check validation
        if ($trader->analystAssignments()->exists()) {
            return back()->with('error', 'You already have an active analyst assignment.');
        }

        AnalystRequest::create([
            'trader_id' => $trader->id,
            'motivation' => $request->motivation,
            'status' => AnalystRequest::STATUS_PENDING,
            'ip_address' => $request->ip(),
        ]);

        // Notify Admins
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'analyst_request_new',
                'data' => [
                    'message' => 'New Analyst Request from ' . $trader->name,
                    'link' => route('admin.assignments.requests.index'),
                ]
            ]);
        }

        return redirect()->route('trader.analyst-request.create')
            ->with('success', 'Your request has been submitted to the administration for review.');
    }
    
    public function cancel(AnalystRequest $analystRequest)
    {
       if ($analystRequest->trader_id !== Auth::id()) {
            abort(403);
       }
       
       if ($analystRequest->status !== AnalystRequest::STATUS_PENDING) {
           return back()->with('error', 'Cannot cancel a request that has already been processed.');
       }
       
       $analystRequest->delete();
       
       return back()->with('success', 'Request cancelled.');
    }
    public function showConsent(AnalystRequest $analystRequest)
    {
        if ($analystRequest->trader_id !== Auth::id()) {
            abort(403);
        }

        if ($analystRequest->status !== AnalystRequest::STATUS_APPROVED) {
            return redirect()->route('trader.analyst-request.create')
                ->with('error', 'This request is not ready for consent.');
        }

        return view('trader.analyst-request.consent', compact('analystRequest'));
    }

    public function processConsent(Request $request, AnalystRequest $analystRequest)
    {
        if ($analystRequest->trader_id !== Auth::id()) {
            abort(403);
        }

        if ($analystRequest->status !== AnalystRequest::STATUS_APPROVED) {
            return back()->with('error', 'Invalid status.');
        }
        
        // Update to consented state
        $analystRequest->update([
            'status' => AnalystRequest::STATUS_CONSENTED,
            'consented_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        // Notify Admin
        \App\Models\Notification::create([
            'user_id' => $analystRequest->reviewed_by ?? 1,
            'type' => 'analyst_request_consented',
            'data' => [
                'message' => 'Trader ' . Auth::user()->name . ' signed the analyst agreement.',
                'link' => route('admin.assignments.requests.index'),
            ]
        ]);

        // If an analyst was pre-selected by Admin, finalize the assignment now
        if ($analystRequest->analyst_id) {
            \App\Models\AnalystAssignment::create([
                'trader_id' => $analystRequest->trader_id,
                'analyst_id' => $analystRequest->analyst_id,
                'assigned_by' => $analystRequest->reviewed_by ?? 1, // Fallback to system/admin if null
            ]);
            
            $analystRequest->update(['status' => AnalystRequest::STATUS_COMPLETED]);
            
            return redirect()->route('trader.dashboard')->with('success', 'Analyst assignment confirmed! You can now see them in your dashboard.');
        }

        return redirect()->route('trader.analyst-request.create')
            ->with('success', 'Consent recorded. An administrator will finalize your analyst assignment shortly.');
    }
}
