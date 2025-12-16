<?php

namespace App\Http\Controllers\Trader;

use App\Enums\MarketSession;
use App\Enums\TradeDirection;
use App\Enums\TradeOutcome;
use App\Http\Controllers\Controller;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TradeController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('trader.trades.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pairs = $this->getCommonPairs();
        $directions = TradeDirection::cases();
        $sessions = MarketSession::cases();
        $outcomes = TradeOutcome::cases();

        return view('trader.trades.create', compact('pairs', 'directions', 'sessions', 'outcomes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pair' => 'required|string|max:255',
            'direction' => 'required|in:buy,sell',
            'entry_date' => 'required|date',
            'exit_date' => 'nullable|date|after:entry_date',
            'strategy' => 'nullable|string|max:255',
            'session' => 'required|in:london,newyork,asia,sydney',
            'emotion' => 'nullable|string|max:255',
            'risk_reward_ratio' => 'nullable|numeric|min:0',
            'outcome' => 'required|in:win,loss,breakeven',
            'pips' => 'nullable|numeric',
            'profit_loss' => 'required|numeric',
            'tradingview_link' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $trade = Auth::user()->trades()->create($validated);

        // Add strategy tag if provided
        if ($request->strategy) {
            $trade->attachTag($request->strategy, 'strategy');
        }

        return redirect()->route('trader.trades.index')
            ->with('success', 'Trade logged successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Trade $trade)
    {
        // Ensure user owns the trade
        $this->authorize('view', $trade);

        return view('trader.trades.show', compact('trade'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trade $trade)
    {
        // Ensure user owns the trade
        $this->authorize('update', $trade);

        $pairs = $this->getCommonPairs();
        $directions = TradeDirection::cases();
        $sessions = MarketSession::cases();
        $outcomes = TradeOutcome::cases();

        return view('trader.trades.edit', compact('trade', 'pairs', 'directions', 'sessions', 'outcomes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trade $trade)
    {
        // Ensure user owns the trade
        $this->authorize('update', $trade);

        $validated = $request->validate([
            'pair' => 'required|string|max:255',
            'direction' => 'required|in:buy,sell',
            'entry_date' => 'required|date',
            'exit_date' => 'nullable|date|after:entry_date',
            'strategy' => 'nullable|string|max:255',
            'session' => 'required|in:london,newyork,asia,sydney',
            'emotion' => 'nullable|string|max:255',
            'risk_reward_ratio' => 'nullable|numeric|min:0',
            'outcome' => 'required|in:win,loss,breakeven',
            'pips' => 'nullable|numeric',
            'profit_loss' => 'required|numeric',
            'tradingview_link' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $trade->update($validated);

        // Sync strategy tag
        if ($request->strategy) {
            $trade->syncTagsWithType([$request->strategy], 'strategy');
        } else {
            $trade->detachTags($trade->tagsWithType('strategy'));
        }

        return redirect()->route('trader.trades.show', $trade)
            ->with('success', 'Trade updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trade $trade)
    {
        // Ensure user owns the trade
        $this->authorize('delete', $trade);

        $trade->delete();

        return redirect()->route('trader.trades.index')
            ->with('success', 'Trade deleted successfully!');
    }

    /**
     * Get common forex pairs
     */
    private function getCommonPairs(): array
    {
        return [
            'EUR/USD', 'GBP/USD', 'USD/JPY', 'USD/CHF', 'AUD/USD', 'USD/CAD', 'NZD/USD',
            'EUR/GBP', 'EUR/JPY', 'GBP/JPY', 'EUR/CHF', 'EUR/AUD', 'EUR/CAD', 'EUR/NZD',
            'GBP/CHF', 'GBP/AUD', 'GBP/CAD', 'GBP/NZD', 'AUD/JPY', 'AUD/NZD', 'AUD/CAD',
            'CAD/JPY', 'CHF/JPY', 'NZD/JPY', 'GBP/ZAR', 'GBP/TRY',
        ];
    }
}
