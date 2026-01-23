<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display listing of subscriptions.
     */
    public function index(Request $request)
    {
        // Check if model exists before querying (defensive)
        if (!class_exists('App\Models\Subscription')) {
            return view('admin.subscriptions.index', ['subscriptions' => collect([])])
                ->with('error', 'Subscription module not installed yet');
        }

        $query = Subscription::with(['trader', 'analyst']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('trader', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('analyst', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->latest()->paginate(20);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function show($id)
    {
        $subscription = Subscription::with(['trader', 'analyst'])->findOrFail($id);
        
        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Cancel subscription (as admin)
     */
    public function cancel($id)
    {
        $subscription = Subscription::findOrFail($id);
        
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => 'admin'
        ]);

        // TODO: Log activity
        
        return back()->with('success', 'Subscription cancelled by admin');
    }
}
