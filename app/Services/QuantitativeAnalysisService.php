<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * QuantitativeAnalysisService
 * 
 * Extends existing performance analysis with additional metrics:
 * - Strategy compliance rate
 * - Risk discipline adherence
 * - Execution quality (SL/TP respect)
 * - Consistency trends over time
 */
class QuantitativeAnalysisService
{
    protected $performanceService;

    public function __construct(PerformanceAnalysisService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    /**
     * Main analysis method with filter support
     */
    public function analyzeWithFilters(User $trader, array $filters = []): array
    {
        $trades = $this->applyFilters($trader->trades(), $filters)->get();

        if ($trades->isEmpty()) {
            return $this->getEmptyAnalysis();
        }

        // Get base metrics from existing service
        $baseMetrics = $this->performanceService->analyzeTraderPerformance($trader);

        // Add extended metrics
        return array_merge($baseMetrics, [
            'strategy_compliance' => $this->calculateStrategyCompliance($trades),
            'risk_discipline' => $this->assessRiskDiscipline($trades),
            'execution_quality' => $this->evaluateExecutionQuality($trades),
            'consistency_analysis' => $this->analyzeConsistency($trades),
            'session_performance' => $this->analyzeSessionPerformance($trades),
            'pair_performance' => $this->analyzePairPerformance($trades),
            'filters_applied' => $filters,
            'analysis_metadata' => [
                'trades_analyzed' => $trades->count(),
                'date_range' => $this->getDateRange($trades),
                'data_quality' => $this->assessDataQuality($trades),
            ],
        ]);
    }

    /**
     * Calculate strategy compliance rate
     */
    public function calculateStrategyCompliance(Collection $trades): array
    {
        $tradesWithStrategy = $trades->whereNotNull('strategy_id');
        $complianceRate = $trades->count() > 0 
            ? round(($tradesWithStrategy->count() / $trades->count()) * 100, 1)
            : 0;

        // Analyze performance by strategy assignment
        $strategyAssignedWinRate = $tradesWithStrategy->isNotEmpty() 
            ? $this->calculateWinRate($tradesWithStrategy)
            : 0;

        $noStrategyTrades = $trades->whereNull('strategy_id');
        $noStrategyWinRate = $noStrategyTrades->isNotEmpty()
            ? $this->calculateWinRate($noStrategyTrades)
            : 0;

        return [
            'rate' => $complianceRate,
            'trades_with_strategy' => $tradesWithStrategy->count(),
            'trades_without_strategy' => $noStrategyTrades->count(),
            'strategy_assigned_win_rate' => $strategyAssignedWinRate,
            'no_strategy_win_rate' => $noStrategyWinRate,
            'impact' => $strategyAssignedWinRate - $noStrategyWinRate,
            'assessment' => $this->assessStrategyCompliance($complianceRate, $strategyAssignedWinRate),
        ];
    }

    /**
     * Assess risk discipline adherence
     */
    public function assessRiskDiscipline(Collection $trades): array
    {
        // Risk percentage consistency
        $tradesWithRiskPct = $trades->whereNotNull('risk_percentage')->where('risk_percentage', '>', 0);
        $avgRiskPct = $tradesWithRiskPct->avg('risk_percentage');
        $riskVariance = $tradesWithRiskPct->isNotEmpty() 
            ? $this->calculateVariance($tradesWithRiskPct->pluck('risk_percentage'))
            : 0;

        // Lot size consistency
        $lotSizes = $trades->pluck('lot_size');
        $lotSizeVariance = $this->calculateVariance($lotSizes);

        // Risk-reward ratio adherence
        $tradesWithRR = $trades->whereNotNull('risk_reward_ratio')->where('risk_reward_ratio', '>', 0);
        $avgRR = $tradesWithRR->avg('risk_reward_ratio');
        $targetRR = 2.0; // Industry standard
        $rrAdherence = $avgRR >= $targetRR ? 'good' : 'needs_improvement';

        return [
            'average_risk_percentage' => round($avgRiskPct, 2),
            'risk_percentage_variance' => round($riskVariance, 2),
            'risk_consistency' => $this->assessConsistency($riskVariance),
            'average_risk_reward' => round($avgRR, 2),
            'risk_reward_adherence' => $rrAdherence,
            'lot_size_variance' => round($lotSizeVariance, 3),
            'overall_discipline_score' => $this->calculateOverallRiskScore($riskVariance, $avgRR),
        ];
    }

    /**
     * Evaluate execution quality (SL/TP management)
     */
    public function evaluateExecutionQuality(Collection $trades): array
    {
        // SL set rate
        $tradesWithSL = $trades->whereNotNull('stop_loss')->where('stop_loss', '!=', 0);
        $slSetRate = round(($tradesWithSL->count() / max($trades->count(), 1)) * 100, 1);

        // TP set rate
        $tradesWithTP = $trades->whereNotNull('take_profit')->where('take_profit', '!=', 0);
        $tpSetRate = round(($tradesWithTP->count() / max($trades->count(), 1)) * 100, 1);

        // Check if exits match SL/TP (simplified logic)
        $slHitTrades = $tradesWithSL->filter(function ($trade) {
            return $trade->outcome === 'loss' && $trade->exit_price != null;
        });

        $tpHitTrades = $tradesWithTP->filter(function ($trade) {
            return $trade->outcome === 'win' && $trade->exit_price != null;
        });

        return [
            'sl_set_rate' => $slSetRate,
            'tp_set_rate' => $tpSetRate,
            'sl_respected_count' => $slHitTrades->count(),
            'tp_hit_count' => $tpHitTrades->count(),
            'execution_grade' => $this->getExecutionGrade($slSetRate, $tpSetRate),
            'recommendation' => $this->getExecutionRecommendation($slSetRate, $tpSetRate),
        ];
    }

    /**
     * Analyze consistency over time
     */
    public function analyzeConsistency(Collection $trades): array
    {
        // Split into thirds (recent, middle, early)
        $thirdSize = floor($trades->count() / 3);
        $recent = $trades->take($thirdSize);
        $middle = $trades->skip($thirdSize)->take($thirdSize);
        $early = $trades->skip($thirdSize * 2);

        return [
            'recent_win_rate' => $this->calculateWinRate($recent),
            'middle_win_rate' => $this->calculateWinRate($middle),
            'early_win_rate' => $this->calculateWinRate($early),
            'trend' => $this->determinePerformanceTrend($early, $middle, $recent),
            'volatility' => $this->calculatePerformanceVolatility($trades),
        ];
    }

    /**
     * Analyze performance by session
     */
    protected function analyzeSessionPerformance(Collection $trades): array
    {
        return $trades->whereNotNull('session')
            ->groupBy('session')
            ->map(function ($group, $session) {
                return [
                    'session' => $session,
                    'trade_count' => $group->count(),
                    'win_rate' => $this->calculateWinRate($group),
                    'avg_profit_loss' => round($group->avg('profit_loss'), 2),
                    'total_pl' => round($group->sum('profit_loss'), 2),
                ];
            })
            ->sortByDesc('win_rate')
            ->values()
            ->toArray();
    }

    /**
     * Analyze performance by currency pair
     */
    protected function analyzePairPerformance(Collection $trades): array
    {
        return $trades->groupBy('pair')
            ->map(function ($group, $pair) {
                return [
                    'pair' => $pair,
                    'trade_count' => $group->count(),
                    'win_rate' => $this->calculateWinRate($group),
                    'avg_profit_loss' => round($group->avg('profit_loss'), 2),
                    'total_pl' => round($group->sum('profit_loss'), 2),
                ];
            })
            ->sortByDesc('total_pl')
            ->values()
            ->take(10) // Top 10 pairs
            ->toArray();
    }

    // ==================== FILTER APPLICATION ====================

    protected function applyFilters($query, array $filters)
    {
        // Trade count filter
        if (isset($filters['trade_count']) && $filters['trade_count'] > 0) {
            $query->latest()->limit($filters['trade_count']);
        }

        // Date range filter
        if (isset($filters['date_from'])) {
            $query->where('entry_date', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('entry_date', '<=', $filters['date_to']);
        }

        // Strategy filter
        if (isset($filters['strategy_id']) && !empty($filters['strategy_id'])) {
            $query->where('strategy_id', $filters['strategy_id']);
        }

        // Account filter
        if (isset($filters['account_id']) && !empty($filters['account_id'])) {
            $query->where('trade_account_id', $filters['account_id']);
        }

        // Session filter
        if (isset($filters['session']) && !empty($filters['session'])) {
            $query->where('session', $filters['session']);
        }

        return $query;
    }

    // ==================== HELPER METHODS ====================

    protected function calculateWinRate(Collection $trades): float
    {
        if ($trades->isEmpty()) {
            return 0;
        }
        
        $wins = $trades->where('outcome', 'win')->count();
        return round(($wins / $trades->count()) * 100, 1);
    }

    protected function calculateVariance(Collection $values): float
    {
        if ($values->count() < 2) {
            return 0;
        }

        $mean = $values->avg();
        $squaredDiffs = $values->map(fn($val) => pow($val - $mean, 2));
        
        return $squaredDiffs->sum() / $values->count();
    }

    protected function assessConsistency(float $variance): string
    {
        if ($variance < 0.5) return 'excellent';
        if ($variance < 1.5) return 'good';
        if ($variance < 3.0) return 'moderate';
        return 'poor';
    }

    protected function calculateOverallRiskScore(float $riskVariance, float $avgRR): int
    {
        $varianceScore = $riskVariance < 1.0 ? 50 : max(0, 50 - ($riskVariance * 10));
        $rrScore = min(50, ($avgRR / 2.0) * 50);
        
        return (int) round($varianceScore + $rrScore);
    }

    protected function assessStrategyCompliance(float $rate, float $winRate): string
    {
        if ($rate >= 80 && $winRate >= 55) return 'excellent';
        if ($rate >= 60 && $winRate >= 50) return 'good';
        if ($rate >= 40) return 'moderate';
        return 'poor';
    }

    protected function getExecutionGrade(float $slRate, float $tpRate): string
    {
        $avgRate = ($slRate + $tpRate) / 2;
        
        if ($avgRate >= 90) return 'A';
        if ($avgRate >= 80) return 'B';
        if ($avgRate >= 70) return 'C';
        if ($avgRate >= 60) return 'D';
        return 'F';
    }

    protected function getExecutionRecommendation(float $slRate, float $tpRate): string
    {
        if ($slRate < 80) {
            return 'Always set stop-loss orders for risk management';
        }
        if ($tpRate < 70) {
            return 'Consider setting take-profit targets more consistently';
        }
        return 'Execution discipline is strong';
    }

    protected function determinePerformanceTrend(Collection $early, Collection $middle, Collection $recent): string
    {
        $earlyWR = $this->calculateWinRate($early);
        $middleWR = $this->calculateWinRate($middle);
        $recentWR = $this->calculateWinRate($recent);

        if ($recentWR > $middleWR && $middleWR > $earlyWR) return 'improving';
        if ($recentWR < $middleWR && $middleWR < $earlyWR) return 'declining';
        if (abs($recentWR - $earlyWR) < 5) return 'stable';
        
        return 'volatile';
    }

    protected function calculatePerformanceVolatility(Collection $trades): float
    {
        $plValues = $trades->pluck('profit_loss');
        return $this->calculateVariance($plValues);
    }

    protected function getDateRange(Collection $trades): array
    {
        return [
            'from' => $trades->min('entry_date'),
            'to' => $trades->max('entry_date'),
        ];
    }

    protected function assessDataQuality(Collection $trades): string
    {
        $completenessScore = 0;
        $total = $trades->count();

        // Check for filled key fields
        $completenessScore += ($trades->whereNotNull('outcome')->count() / $total) * 25;
        $completenessScore += ($trades->whereNotNull('profit_loss')->count() / $total) * 25;
        $completenessScore += ($trades->whereNotNull('stop_loss')->count() / $total) * 25;
        $completenessScore += ($trades->whereNotNull('strategy_id')->count() / $total) * 25;

        if ($completenessScore >= 80) return 'high';
        if ($completenessScore >= 60) return 'moderate';
        return 'low';
    }

    protected function getEmptyAnalysis(): array
    {
        return [
            'message' => 'No trades match the specified filters',
            'strategy_compliance' => ['rate' => 0],
            'risk_discipline' => ['overall_discipline_score' => 0],
            'execution_quality' => ['execution_grade' => 'N/A'],
            'consistency_analysis' => ['trend' => 'N/A'],
        ];
    }
}
