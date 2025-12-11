<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Display all feedback received by the trader
     */
    public function index()
    {
        $trader = Auth::user();
        
        $feedback = $trader->feedbackReceived()
            ->with('analyst')
            ->latest()
            ->paginate(10);

        return view('trader.feedback.index', compact('feedback'));
    }

    /**
     * Show a specific feedback
     */
    public function show($id)
    {
        $trader = Auth::user();
        
        $feedback = $trader->feedbackReceived()
            ->with(['analyst', 'trade'])
            ->findOrFail($id);

        return view('trader.feedback.show', compact('feedback'));
    }
}
