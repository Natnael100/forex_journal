<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use App\Models\AnalystReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalystReviewController extends Controller
{
    /**
     * Store a new review (trader reviews analyst)
     */
    public function store(Request $request, User $analyst)
    {
        $trader = Auth::user();

        // Check if trader has an active subscription with this analyst
        $hasSubscription = $trader->subscriptionsAsTrader()
            ->where('analyst_id', $analyst->id)
            ->where(function ($query) {
                $query->where('status', 'active')
                      ->orWhere('status', 'cancelled'); // Can review even after cancellation
            })
            ->exists();

        if (!$hasSubscription) {
            return redirect()->back()->with('error', 'You can only review analysts you have subscribed to.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        // Create or update review
        AnalystReview::updateOrCreate(
            [
                'analyst_id' => $analyst->id,
                'trader_id' => $trader->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'is_approved' => true, // Auto-approve reviews
            ]
        );

        return redirect()->back()->with('success', 'Review submitted successfully!');
    }

    /**
     * Delete review (trader can delete their own review)
     */
    public function destroy(AnalystReview $review)
    {
        $user = Auth::user();

        if ($review->trader_id !== $user->id && !$user->hasRole('admin')) {
            abort(403);
        }

        $review->delete();

        return redirect()->back()->with('success', 'Review deleted.');
    }

    /**
     * Admin: Approve review
     */
    public function approve(AnalystReview $review)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $review->approve();

        return redirect()->back()->with('success', 'Review approved.');
    }
}
