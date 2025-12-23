<?php

namespace App\Services;

use App\Models\User;
use App\Models\Trade;
use Illuminate\Support\Collection;

class SimulationService
{
    /**
     * Run a simulation on a trader's portfolio by applying exclusionary filters.
     * Returns comparison of Actual vs Shadow metrics.
     */
    public function runSimulation(User $trader, array $filters): array
    {
        // 1. Get Base Trades (e.g., last 100 or date range)
        $query = $trader->trades()
            ->latest()
            ->limit(100); // Default limit for performance, can be adjustable

        $allTrades = $query->get();

        // 2. Filter Trades (Keep only those that PASS the filter rules)
        // In "What-If" simulation, we usually want to SEE what happens if we REMOVE bad habits.
        // So if filter is "Exclude Asia Session", we keep everything EXCEPT Asia.
        
        $shadowTrades = $allTrades->filter(function ($trade) use ($filters) {
            
            // Filter: Session
            if (!empty($filters['exclude_sessions'])) {
                if (in_array($trade->session->value, $filters['exclude_sessions'])) {
                    return false; // Remove this trade
                }
            }

            // Filter: Pairs
            if (!empty($filters['exclude_pairs'])) {
                if (in_array($trade->pair, $filters['exclude_pairs'])) {
                    return false;
                }
            }
            
            // Filter: Direction
            if (!empty($filters['exclude_direction'])) {
                if ($trade->direction->value === $filters['exclude_direction']) {
                    return false;
                }
            }
            
            // Filter: Outcome (e.g., "What if I avoided all large losses?")
             if (!empty($filters['exclude_large_losses'])) {
                 // Assuming large loss is > 2x average loss or fixed amount
                 // For simplicity, let's say loss < -$100
                 if ($trade->profit_loss < -100) {
                     return false;
                 }
             }

            return true; // Keep trade
        });

        // 3. Calculate Metrics
        return [
            'actual' => $this->calculateMetrics($allTrades),
            'shadow' => $this->calculateMetrics($shadowTrades),
            'excluded_count' => $allTrades->count() - $shadowTrades->count(),
            'filters_applied' => $filters
        ];
    }

    protected function calculateMetrics(Collection $trades): array
    {
        if ($trades->isEmpty()) {
            return [
                'total_trades' => 0,
                'win_rate' => 0,
                'net_profit' => 0,
                'profit_factor' => 0,
                'avg_win' => 0,
                'avg_loss' => 0,
            ];
        }

        $wins = $trades->where('profit_loss', '>', 0);
        $losses = $trades->where('profit_loss', '<=', 0);

        $totalProfit = $wins->sum('profit_loss');
        $totalLoss = abs($losses->sum('profit_loss'));

        return [
            'total_trades' => $trades->count(),
            'win_rate' => round(($wins->count() / $trades->count()) * 100, 1),
            'net_profit' => $trades->sum('profit_loss'),
            'profit_factor' => $totalLoss > 0 ? round($totalProfit / $totalLoss, 2) : ($totalProfit > 0 ? 999 : 0),
            'avg_win' => $wins->count() > 0 ? round($wins->avg('profit_loss'), 2) : 0,
            'avg_loss' => $losses->count() > 0 ? round($losses->avg('profit_loss'), 2) : 0,
        ];
    }
}
