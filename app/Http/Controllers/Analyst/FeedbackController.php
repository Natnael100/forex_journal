<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\User;
use App\Models\Trade;
use App\Services\PerformanceAnalysisService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    protected $performanceAnalysis;
    protected $notificationService;

    public function __construct(PerformanceAnalysisService $performanceAnalysis, NotificationService $notificationService)
    {
        $this->performanceAnalysis = $performanceAnalysis;
        $this->notificationService = $notificationService;
    }

    /**
     * Show feedback creation form
     */
    public function create($traderId, $tradeId = null)
    {
        $analyst = Auth::user();
        $trader = User::findOrFail($traderId);
        $trade = $tradeId ? Trade::findOrFail($tradeId) : null;

        // Verify analyst is assigned
        if (!$analyst->tradersAssigned()->where('trader_id', $traderId)->exists() && !$analyst->hasRole('admin')) {
            abort(403);
        }

        // Generate AI suggestions
        $aiSuggestions = $this->performanceAnalysis->analyzeTraderPerformance($trader);

        // Recent feedback for context
        $recentFeedback = $trader->feedbackReceived()
            ->with('analyst')
            ->latest()
            ->take(10)
            ->get();

        return view('analyst.feedback.create', compact('trader', 'trade', 'aiSuggestions', 'recentFeedback'));
    }

    /**
     * Store feedback
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trader_id' => 'required|exists:users,id',
            'trade_id' => 'nullable|exists:trades,id',
            'content' => 'required|string|min:20',
        ]);

        $analyst = Auth::user();

        // Verify assignment
        if (!$analyst->tradersAssigned()->where('trader_id', $validated['trader_id'])->exists() && !$analyst->hasRole('admin')) {
            abort(403);
        }

        // Re-generate AI suggestions at submission time
        $trader = User::findOrFail($validated['trader_id']);
        $aiSuggestions = $this->performanceAnalysis->analyzeTraderPerformance($trader);

        $feedback = Feedback::create([
            'trader_id' => $validated['trader_id'],
            'analyst_id' => $analyst->id,
            'trade_id' => $validated['trade_id'],
            'content' => $validated['content'],
            'ai_suggestions' => $aiSuggestions,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Send notification
        $this->notificationService->notifyFeedbackReceived($trader, $feedback);

        return redirect()
            ->route('analyst.trader.profile', $trader->id)
            ->with('success', 'Feedback submitted successfully!');
    }

    /**
     * Show edit form
     */
    public function edit($feedbackId)
    {
        $feedback = Feedback::with(['trader', 'trade'])->findOrFail($feedbackId);
        $analyst = Auth::user();

        // Check if editable and belongs to analyst
        if (!$feedback->canBeEditedBy($analyst)) {
            abort(403, $feedback->isEditable() ? 'This feedback does not belong to you.' : 'Feedback can no longer be edited (24-hour window expired).');
        }

        // Recent feedback for context
        $recentFeedback = $feedback->trader->feedbackReceived()
            ->with('analyst')
            ->where('id', '!=', $feedbackId)
            ->latest()
            ->take(10)
            ->get();

        return view('analyst.feedback.edit', compact('feedback', 'recentFeedback'));
    }

    /**
     * Update feedback
     */
    public function update(Request $request, $feedbackId)
    {
        $feedback = Feedback::findOrFail($feedbackId);
        $analyst = Auth::user();

        if (!$feedback->canBeEditedBy($analyst)) {
            abort(403, 'Cannot edit this feedback.');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:20',
        ]);

        $feedback->update(['content' => $validated['content']]);

        // Notify trader of edit
        $this->notificationService->notifyFeedbackEdited($feedback->trader, $feedback);

        return redirect()
            ->route('analyst.trader.profile', $feedback->trader_id)
            ->with('success', 'Feedback updated successfully!');
    }

    /**
     * Delete feedback (within 24 hours)
     */
    public function destroy($feedbackId)
    {
        $feedback = Feedback::findOrFail($feedbackId);
        $analyst = Auth::user();

        if (!$feedback->canBeEditedBy($analyst)) {
            abort(403, 'Cannot delete this feedback.');
        }

        $traderId = $feedback->trader_id;
        $feedback->delete();

        return redirect()
            ->route('analyst.trader.profile', $traderId)
            ->with('success', 'Feedback deleted successfully!');
    }
}
