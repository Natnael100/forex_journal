<?php

namespace App\Http\Controllers\Trader;

use App\Enums\MarketSession;
use App\Enums\TradeDirection;
use App\Enums\TradeOutcome;
use App\Enums\TradeEmotion;
use App\Enums\PostTradeEmotion;
use App\Http\Controllers\Controller;
use App\Models\Trade;
use App\Models\Strategy;
use App\Services\AchievementService;
use App\Services\RiskComplianceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

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

    public function create()
    {
        $pairs = $this->getCommonPairs();
        $directions = TradeDirection::cases();
        $sessions = MarketSession::cases();
        $outcomes = TradeOutcome::cases();
        $emotions = TradeEmotion::cases();
        $postEmotions = PostTradeEmotion::cases();
        $accounts = Auth::user()->tradeAccounts()->get();
        $strategies = Strategy::where('user_id', Auth::id())->get();
        
        // Phase 6: Guided Journaling
        $focusArea = 'standard';
        $assignment = \App\Models\AnalystAssignment::where('trader_id', Auth::id())->first();
        if ($assignment) {
            $focusArea = $assignment->current_focus_area;
            
            // Feature Gating: Only allow guided journaling if user has the feature (Elite)
            // If they don't, fallback to standard even if analyst assigned a focus
            $subscription = Auth::user()->activeSubscription;
            if (!$subscription || !$subscription->hasFeature('guided_journaling')) {
                $focusArea = 'standard';
            }
        }

        return view('trader.trades.create', compact('pairs', 'directions', 'sessions', 'outcomes', 'emotions', 'postEmotions', 'accounts', 'strategies', 'focusArea'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trade_account_id' => 'required|exists:trade_accounts,id',
            'pair' => 'required|string|max:255',
            'direction' => 'required|in:buy,sell',
            'entry_date' => 'required|date',
            'exit_date' => 'nullable|date|after:entry_date',
            'entry_price' => 'required|numeric|gt:0',
            'exit_price' => 'required|numeric|gt:0',
            'stop_loss' => 'nullable|numeric|gt:0',
            'take_profit' => 'nullable|numeric|gt:0',
            'lot_size' => 'required|numeric|gt:0',
            'risk_percentage' => 'nullable|numeric|min:0|max:100',
            'strategy_id' => 'nullable|exists:strategies,id',
            'trade_type' => 'nullable|string|max:50',
            'session' => 'nullable|string',
            'pre_trade_emotion' => 'nullable|string|max:255',
            'post_trade_emotion' => 'nullable|string|max:255',
            'followed_plan' => 'nullable|boolean',
            'setup_notes' => 'nullable|string',
            'mistakes_lessons' => 'nullable|string',
            'chart_link' => 'nullable|url',
            'notes' => 'nullable|string',
            'focus_data' => 'nullable|array',
        ]);

        // Auto-detect session if missing
        if (empty($validated['session'])) {
            $validated['session'] = $this->determineSession($validated['entry_date']);
        }

        // Calculate and Set Derived Metrics
        $calc = $this->calculateMetrics($validated);
        $validated['pips'] = $calc['pips'];
        $validated['profit_loss'] = $calc['profit_loss'];
        $validated['risk_reward_ratio'] = $calc['rr'];
        $validated['outcome'] = $calc['outcome'];

        $validated['outcome'] = $calc['outcome'];

        // Compliance Check (Phase 6)
        $compliance = app(RiskComplianceService::class)->checkCompliance(Auth::user(), $validated);
        
        if (!$compliance['compliant']) {
            if ($compliance['is_hard_stop']) {
                return back()->withInput()->withErrors(['compliance' => 'Trade blocked by Risk Rule: ' . $compliance['reason']]);
            }
            
            // Soft Stop: Mark as non-compliant but allow save
            $validated['is_compliant'] = false;
            $validated['violation_reason'] = $compliance['reason'];
        }

        $trade = Auth::user()->trades()->create($validated);
        
        $message = 'Trade logged successfully! +10 XP';
        if (isset($validated['is_compliant']) && !$validated['is_compliant']) {
            $message .= ' (Warning: ' . $validated['violation_reason'] . ')';
        }

        // Award XP and check achievements
        app(AchievementService::class)->awardTradeXp(Auth::user());

        return redirect()->route('trader.trades.index')
            ->with('success', $message);
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
        $emotions = TradeEmotion::cases();
        $postEmotions = PostTradeEmotion::cases();
        $accounts = Auth::user()->tradeAccounts()->get();
        $strategies = Strategy::where('user_id', Auth::id())->get();

        return view('trader.trades.edit', compact('trade', 'pairs', 'directions', 'sessions', 'outcomes', 'emotions', 'postEmotions', 'accounts', 'strategies'));
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
            'entry_price' => 'required|numeric|gt:0',
            'exit_price' => 'required|numeric|gt:0',
            'stop_loss' => 'nullable|numeric|gt:0',
            'take_profit' => 'nullable|numeric|gt:0',
            'lot_size' => 'required|numeric|gt:0',
            'risk_percentage' => 'nullable|numeric|min:0|max:100',
            'strategy_id' => 'nullable|exists:strategies,id',
            'trade_type' => 'nullable|string|max:50',
            'session' => 'nullable|string',
            'pre_trade_emotion' => 'nullable|string|max:255',
            'post_trade_emotion' => 'nullable|string|max:255',
            'followed_plan' => 'nullable|boolean',
            'setup_notes' => 'nullable|string',
            'mistakes_lessons' => 'nullable|string',
            'chart_link' => 'nullable|url',
            'notes' => 'nullable|string',
            'focus_data' => 'nullable|array',
        ]);

        // Auto-detect session if missing
        if (empty($validated['session'])) {
            $validated['session'] = $this->determineSession($validated['entry_date']);
        }

        // Calculate Metrics
        $calc = $this->calculateMetrics($validated);
        $validated['pips'] = $calc['pips'];
        $validated['profit_loss'] = $calc['profit_loss'];
        $validated['risk_reward_ratio'] = $calc['rr'];
        $validated['outcome'] = $calc['outcome'];

        $trade->update($validated);

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
            'CAD/JPY', 'CHF/JPY', 'NZD/JPY', 'GBP/ZAR', 'GBP/TRY', 'XAU/USD', 'BTC/USD',
        ];
    }

    /**
     * Determine trading session from time (UTC)
     */
    private function determineSession($date): string
    {
        $hour = Carbon::parse($date)->hour;
        
        if ($hour >= 8 && $hour < 16) return MarketSession::LONDON->value;
        if ($hour >= 13 && $hour < 21) return MarketSession::NEWYORK->value;
        if ($hour >= 21 || $hour < 5) return MarketSession::SYDNEY->value;
        return MarketSession::ASIA->value;
    }

    /**
     * Calculate derived metrics
     */
    private function calculateMetrics(array $data): array
    {
        $entry = $data['entry_price'];
        $exit = $data['exit_price'];
        $direction = $data['direction']; 
        $lot = $data['lot_size'];

        // Pip Multiplier
        $pair = strtoupper($data['pair'] ?? '');
        $isJpy = str_contains($pair, 'JPY');
        $multiplier = $isJpy ? 100 : 10000;
        
        // Pips
        if ($direction === 'buy') {
            $diff = $exit - $entry;
        } else {
            $diff = $entry - $exit;
        }
        $pips = $diff * $multiplier;
        
        // Approx Profit ($10 per pip per lot standard)
        // For JPY pairs, calculation differs ($1000 per lot per 1 movement?)
        // Standard: 1 lot = $10/pip.
        $pl = $pips * $lot * 10; 
        
        // Outcome
        if ($pips > 0) $outcome = TradeOutcome::WIN->value;
        elseif ($pips < 0) $outcome = TradeOutcome::LOSS->value;
        else $outcome = TradeOutcome::BREAKEVEN->value;

        // RR
        $rr = 0;
        if (!empty($data['stop_loss']) && !empty($data['take_profit'])) {
             $risk = abs($entry - $data['stop_loss']);
             $reward = abs($entry - $data['take_profit']);
             if ($risk > 0) {
                 $rr = $reward / $risk;
             }
        }

        return [
            'pips' => round($pips, 2),
            'profit_loss' => round($pl, 2),
            'rr' => round($rr, 2),
            'outcome' => $outcome
        ];
    }
}
