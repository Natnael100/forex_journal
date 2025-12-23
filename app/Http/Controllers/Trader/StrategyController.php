<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Models\Strategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Enums\StrategyStatus;

class StrategyController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $strategies = Auth::user()->strategies()->with(['trades' => function($query) {
            $query->select('id', 'strategy_id', 'profit_loss', 'outcome');
        }])->latest()->get();

        // Global Stats
        $activeCount = $strategies->where('status', StrategyStatus::ACTIVE)->count();
        $testingCount = $strategies->where('status', StrategyStatus::TESTING)->count();
        $totalTrades = $strategies->sum(fn($s) => $s->trades->count());
        $totalProfit = $strategies->sum(fn($s) => $s->trades->sum('profit_loss'));

        // Per Strategy Stats (calculated on collection for simplicity, or DB aggregate)
        $strategies->each(function($strategy) {
            $trades = $strategy->trades;
            $strategy->total_trades = $trades->count();
            $strategy->total_profit = $trades->sum('profit_loss');
            
            $wins = $trades->where('outcome', \App\Enums\TradeOutcome::WIN)->count();
            $strategy->win_rate = $strategy->total_trades > 0 
                ? round(($wins / $strategy->total_trades) * 100) 
                : 0;
            
            // Avg R (Simple approximation if RR available, else 0)
            // For now, placeholder or based on Avg Risk/Reward if data exists
            $strategy->avg_r = 0; 
        });

        $selectedStrategy = request('view_strategy') ? $strategies->find(request('view_strategy')) : $strategies->first();

        return view('trader.strategies.index', compact('strategies', 'activeCount', 'testingCount', 'totalTrades', 'totalProfit', 'selectedStrategy'));
    }

    public function create()
    {
        return view('trader.strategies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', new \Illuminate\Validation\Rules\Enum(StrategyStatus::class)],
            'tags' => 'nullable|array',
            'rules' => 'nullable|array',
        ]);

        Auth::user()->strategies()->create($validated);

        return redirect()->route('trader.strategies.index')
            ->with('success', 'Strategy created successfully!');
    }

    public function edit(Strategy $strategy)
    {
        $this->authorize('update', $strategy);
        return view('trader.strategies.edit', compact('strategy'));
    }

    public function update(Request $request, Strategy $strategy)
    {
        $this->authorize('update', $strategy);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', new \Illuminate\Validation\Rules\Enum(StrategyStatus::class)],
            'tags' => 'nullable|array',
            'rules' => 'nullable|array',
        ]);

        $strategy->update($validated);

        return redirect()->route('trader.strategies.index')
            ->with('success', 'Strategy updated successfully!');
    }

    public function destroy(Strategy $strategy)
    {
        $this->authorize('delete', $strategy);
        $strategy->delete(); // Soft delete

        return redirect()->route('trader.strategies.index')
            ->with('success', 'Strategy archived successfully.');
    }
}
