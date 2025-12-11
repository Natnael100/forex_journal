<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    /**
     * Display system-wide analytics overview
     */
    public function index()
    {
        // System-wide trade statistics
        $totalTrades = Trade::count();
        $totalWins = Trade::where('outcome', 'win')->count();
        $totalLosses = Trade::where('outcome', 'loss')->count();
        $winRate = $totalTrades > 0 ? ($totalWins / $totalTrades) * 100 : 0;

        // P/L statistics
        $totalProfitLoss = Trade::sum('profit_loss');
        $totalProfit = Trade::where('outcome', 'win')->sum('profit_loss');
        $totalLoss = Trade::where('outcome', 'loss')->sum('profit_loss');

        // Risk metrics
        $avgRiskReward = Trade::whereNotNull('risk_reward')->avg('risk_reward');
        $avgWinSize = Trade::where('outcome', 'win')->avg('profit_loss');
        $avgLossSize = Trade::where('outcome', 'loss')->avg('profit_loss');

        // Most traded pairs
        $topPairs = Trade::select('pair', DB::raw('count(*) as count'))
            ->groupBy('pair')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Session statistics
        $sessionStats = Trade::select('session', DB::raw('count(*) as count'))
            ->whereNotNull('session')
            ->groupBy('session')
            ->get();

        // Best performing traders
        $topTraders = User::role('trader')
            ->withCount('trades')
            ->with(['trades' => function ($query) {
                $query->select('user_id', DB::raw('SUM(profit_loss) as total_pl'))
                    ->groupBy('user_id');
            }])
            ->get()
            ->map(function ($trader) {
                $totalPL = $trader->trades->sum('profit_loss') ?? 0;
                $winCount = $trader->trades()->where('outcome', 'win')->count();
                $tradeCount = $trader->trades_count;
                
                return [
                    'id' => $trader->id,
                    'name' => $trader->name,
                    'trades_count' => $tradeCount,
                    'total_pl' => $totalPL,
                    'win_rate' => $tradeCount > 0 ? ($winCount / $tradeCount) * 100 : 0,
                ];
            })
            ->sortByDesc('total_pl')
            ->take(10);

        return view('admin.analytics.index', compact(
            'totalTrades',
            'winRate',
            'totalProfitLoss',
            'totalProfit',
            'totalLoss',
            'avgRiskReward',
            'avgWinSize',
            'avgLossSize',
            'topPairs',
            'sessionStats',
            'topTraders'
        ));
    }

    /**
     * Display all trades with search and filters
     */
    public function allTrades(Request $request)
    {
        $query = Trade::with('user');

        // Search by trader name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by pair
        if ($request->filled('pair')) {
            $query->where('pair', $request->pair);
        }

        // Filter by outcome
        if ($request->filled('outcome')) {
            $query->where('outcome', $request->outcome);
        }

        // Filter by session
        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }

        // Filter by trader
        if ($request->filled('trader_id')) {
            $query->where('user_id', $request->trader_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'entry_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $trades = $query->paginate(20)->withQueryString();

        // Get filter options
        $pairs = Trade::distinct()->pluck('pair')->sort();
        $traders = User::role('trader')->orderBy('name')->get();

        return view('admin.analytics.all-trades', compact('trades', 'pairs', 'traders'));
    }

    /**
     * Display individual trader analytics
     */
    public function traderAnalytics($traderId)
    {
        $trader = User::role('trader')
            ->with(['trades', 'feedbackReceived.analyst'])
            ->findOrFail($traderId);

        // Calculate trader statistics
        $totalTrades = $trader->trades->count();
        $wins = $trader->trades->where('outcome', 'win')->count();
        $losses = $trader->trades->where('outcome', 'loss')->count();
        $winRate = $totalTrades > 0 ? ($wins / $totalTrades) * 100 : 0;

        $totalPL = $trader->trades->sum('profit_loss');
        $avgWin = $trader->trades->where('outcome', 'win')->avg('profit_loss');
        $avgLoss = $trader->trades->where('outcome', 'loss')->avg('profit_loss');
        $profitFactor = $avgLoss != 0 ? abs($avgWin / $avgLoss) : 0;

        $avgRR = $trader->trades->whereNotNull('risk_reward')->avg('risk_reward');

        // Session breakdown
        $sessionStats = $trader->trades()
            ->select('session', DB::raw('count(*) as count'), DB::raw('sum(profit_loss) as pl'))
            ->whereNotNull('session')
            ->groupBy('session')
            ->get();

        // Top pairs
        $topPairs = $trader->trades()
            ->select('pair', DB::raw('count(*) as count'))
            ->groupBy('pair')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return view('admin.analytics.trader', compact(
            'trader',
            'totalTrades',
            'winRate',
            'totalPL',
            'profitFactor',
            'avgRR',
            'sessionStats',
            'topPairs'
        ));
    }
}
