<?php

namespace App\Services;

use App\Models\Trade;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TradeAnalyticsService
{
    /**
     * Get total number of trades
     */
    public function getTotalTrades(User $user, ?array $filters = []): int
    {
        return $this->applyFilters($user->trades(), $filters)->count();
    }

    /**
     * Get win rate percentage
     */
    public function getWinRate(User $user, ?array $filters = []): float
    {
        $query = $this->applyFilters($user->trades(), $filters);
        $total = $query->count();
        
        if ($total === 0) {
            return 0;
        }

        $wins = (clone $query)->where('outcome', 'win')->count();
        
        return round(($wins / $total) * 100, 2);
    }

    /**
     * Get average risk:reward ratio
     */
    public function getAverageRiskReward(User $user, ?array $filters = []): float
    {
        $query = $this->applyFilters($user->trades(), $filters);
        
        return round($query->whereNotNull('risk_reward_ratio')->avg('risk_reward_ratio') ?? 0, 2);
    }

    /**
     * Get profit factor (Gross Profit / Gross Loss)
     */
    public function getProfitFactor(User $user, ?array $filters = []): float
    {
        $query = $this->applyFilters($user->trades(), $filters);
        
        $grossProfit = (clone $query)->where('profit_loss', '>', 0)->sum('profit_loss');
        $grossLoss = abs((clone $query)->where('profit_loss', '<', 0)->sum('profit_loss'));
        
        if ($grossLoss == 0) {
            return $grossProfit > 0 ? 999 : 0;
        }

        return round($grossProfit / $grossLoss, 2);
    }

    /**
     * Get expectancy: (Win% × Avg Win) - (Loss% × Avg Loss)
     */
    public function getExpectancy(User $user, ?array $filters = []): float
    {
        $query = $this->applyFilters($user->trades(), $filters);
        $total = $query->count();
        
        if ($total === 0) {
            return 0;
        }

        $wins = (clone $query)->where('outcome', 'win');
        $losses = (clone $query)->where('outcome', 'loss');
        
        $avgWin = $wins->avg('profit_loss') ?? 0;
        $avgLoss = abs($losses->avg('profit_loss') ?? 0);
        $winRate = $this->getWinRate($user, $filters) / 100;
        
        return round(($winRate * $avgWin) - ((1 - $winRate) * $avgLoss), 2);
    }

    /**
     * Get average hold time in hours
     */
    public function getAverageHoldTime(User $user, ?array $filters = []): float
    {
        $query = $this->applyFilters($user->trades(), $filters);
        
        $trades = $query->whereNotNull('exit_date')->get();
        
        if ($trades->isEmpty()) {
            return 0;
        }

        $totalHours = $trades->sum(function ($trade) {
            return $trade->entry_date->diffInHours($trade->exit_date);
        });

        return round($totalHours / $trades->count(), 1);
    }

    /**
     * Get maximum drawdown percentage
     */
    public function getMaxDrawdown(User $user, ?array $filters = []): float
    {
        $query = $this->applyFilters($user->trades(), $filters);
        $trades = $query->orderBy('entry_date')->get();
        
        if ($trades->isEmpty()) {
            return 0;
        }

        $peak = 0;
        $maxDrawdown = 0;
        $runningTotal = 0;

        foreach ($trades as $trade) {
            $runningTotal += $trade->profit_loss;
            
            if ($runningTotal > $peak) {
                $peak = $runningTotal;
            }
            
            $drawdown = $peak - $runningTotal;
            
            if ($drawdown > $maxDrawdown) {
                $maxDrawdown = $drawdown;
            }
        }

        return round($maxDrawdown, 2);
    }

    /**
     * Get recovery factor (Net Profit / Max Drawdown)
     */
    public function getRecoveryFactor(User $user, ?array $filters = []): float
    {
        $query = $this->applyFilters($user->trades(), $filters);
        $netProfit = $query->sum('profit_loss');
        $maxDrawdown = $this->getMaxDrawdown($user, $filters);
        
        if ($maxDrawdown == 0) {
            return $netProfit > 0 ? 999 : 0;
        }

        return round($netProfit / $maxDrawdown, 2);
    }

    /**
     * Get equity curve data for chart
     */
    public function getEquityCurveData(User $user, ?array $filters = []): array
    {
        $query = $this->applyFilters($user->trades(), $filters);
        $trades = $query->orderBy('entry_date')->get();
        
        $data = [];
        $runningTotal = 0;

        foreach ($trades as $trade) {
            $runningTotal += $trade->profit_loss;
            $data[] = [
                'date' => $trade->entry_date->format('Y-m-d'),
                'equity' => round($runningTotal, 2),
            ];
        }

        return $data;
    }

    /**
     * Get monthly P&L data for chart
     */
    public function getMonthlyPLData(User $user, int $year): array
    {
        $data = $user->trades()
            ->whereYear('entry_date', $year)
            ->selectRaw("CAST(strftime('%m', entry_date) AS INTEGER) as month, SUM(profit_loss) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->map(fn($item) => round($item->total, 2))
            ->toArray();

        // Fill in missing months with 0
        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $result[] = $data[$i] ?? 0;
        }

        return $result;
    }

    /**
     * Get session performance data
     */
    public function getSessionPerformance(User $user, ?array $filters = []): array
    {
        $query = $this->applyFilters($user->trades(), $filters);
        
        return $query->selectRaw('session, COUNT(*) as trades, SUM(profit_loss) as profit, AVG(profit_loss) as avg_profit')
            ->groupBy('session')
            ->get()
            ->map(function ($item) {
                return [
                    'session' => $item->session->label(), // Use enum's label() method
                    'trades' => $item->trades,
                    'profit' => round($item->profit, 2),
                    'avg_profit' => round($item->avg_profit, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get best and worst performing pairs
     */
    public function getBestWorstPairs(User $user, int $limit = 5): array
    {
        $pairStats = $user->trades()
            ->selectRaw('pair, COUNT(*) as trades, SUM(profit_loss) as profit')
            ->groupBy('pair')
            ->having('trades', '>=', 3) // At least 3 trades
            ->orderBy('profit', 'desc')
            ->get();

        return [
            'best' => $pairStats->take($limit)->map(fn($item) => [
                'pair' => $item->pair,
                'trades' => $item->trades,
                'profit' => round($item->profit, 2),
            ])->toArray(),
            'worst' => $pairStats->sortBy('profit')->take($limit)->map(fn($item) => [
                'pair' => $item->pair,
                'trades' => $item->trades,
                'profit' => round($item->profit, 2),
            ])->toArray(),
        ];
    }

    /**
     * Get win/loss distribution
     */
    public function getWinLossDistribution(User $user, ?array $filters = []): array
    {
        $query = $this->applyFilters($user->trades(), $filters);
        
        return $query->selectRaw('outcome, COUNT(*) as count')
            ->groupBy('outcome')
            ->get()
            ->mapWithKeys(fn($item) => [$item->outcome->label() => $item->count]) // Use enum's label() method
            ->toArray();
    }

    /**
     * Detect win/loss streaks
     */
    public function getStreaks(User $user): array
    {
        $trades = $user->trades()->orderBy('entry_date')->get();
        
        $currentStreak = 0;
        $currentType = null;
        $maxWinStreak = 0;
        $maxLossStreak = 0;

        foreach ($trades as $trade) {
            $outcomeValue = $trade->outcome->value; // Get enum value (string)
            
            if ($outcomeValue === $currentType) {
                $currentStreak++;
            } else {
                if ($currentType === 'win' && $currentStreak > $maxWinStreak) {
                    $maxWinStreak = $currentStreak;
                }
                if ($currentType === 'loss' && $currentStreak > $maxLossStreak) {
                    $maxLossStreak = $currentStreak;
                }
                
                $currentType = $outcomeValue;
                $currentStreak = 1;
            }
        }

        // Check last streak
        if ($currentType === 'win' && $currentStreak > $maxWinStreak) {
            $maxWinStreak = $currentStreak;
        }
        if ($currentType === 'loss' && $currentStreak > $maxLossStreak) {
            $maxLossStreak = $currentStreak;
        }

        return [
            'max_win_streak' => $maxWinStreak,
            'max_loss_streak' => $maxLossStreak,
            'current_streak' => $currentStreak,
            'current_type' => $currentType, // Now returns string 'win' or 'loss'
        ];
    }

    /**
     * Get time of day performance
     */
    public function getTimeOfDayPerformance(User $user): array
    {
        return $user->trades()
            ->selectRaw("CAST(strftime('%H', entry_date) AS INTEGER) as hour, COUNT(*) as trades, SUM(profit_loss) as profit")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(fn($item) => [
                'hour' => $item->hour,
                'trades' => $item->trades,
                'profit' => round($item->profit, 2),
            ])
            ->toArray();
    }

    /**
     * Get day of week performance
     */
    public function getDayOfWeekPerformance(User $user): array
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        // SQLite's strftime('%w', date) returns 0-6 where 0 = Sunday
        $data = $user->trades()
            ->selectRaw("CAST(strftime('%w', entry_date) AS INTEGER) as day, COUNT(*) as trades, SUM(profit_loss) as profit")
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day')
            ->map(fn($item) => [
                'trades' => $item->trades,
                'profit' => round($item->profit, 2),
            ])
            ->toArray();

        $result = [];
        for ($i = 0; $i < 7; $i++) {
            $result[] = [
                'day' => $days[$i],
                'trades' => $data[$i]['trades'] ?? 0,
                'profit' => $data[$i]['profit'] ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, ?array $filters = [])
    {
        if (empty($filters)) {
            return $query;
        }

        if (isset($filters['date_from'])) {
            $query->where('entry_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('entry_date', '<=', $filters['date_to']);
        }

        if (isset($filters['pair'])) {
            $query->where('pair', $filters['pair']);
        }

        if (isset($filters['session'])) {
            $query->where('session', $filters['session']);
        }

        if (isset($filters['outcome'])) {
            $query->where('outcome', $filters['outcome']);
        }

        return $query;
    }
}
