<?php

namespace App\Services;

use App\Models\User;
use App\Services\TradeAnalyticsService;
use Illuminate\Support\Collection;

class PerformanceAnalysisService
{
    protected $analyticsService;

    public function __construct(TradeAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Main analysis method - generates all AI suggestions
     */
    public function analyzeTraderPerformance(User $trader): array
    {
        return [
            'win_rate_analysis' => $this->analyzeWinRate($trader),
            'risk_reward_analysis' => $this->analyzeRiskReward($trader),
            'expectancy_analysis' => $this->analyzeExpectancy($trader),
            'profit_factor_analysis' => $this->analyzeProfitFactor($trader),
            'drawdown_analysis' => $this->analyzeDrawdown($trader),
            'recovery_factor_analysis' => $this->analyzeRecoveryFactor($trader),
            'strategy_analysis' => $this->analyzeStrategies($trader),
            'session_analysis' => $this->analyzeSessions($trader),
            'emotional_analysis' => $this->analyzeEmotions($trader),
            'streak_analysis' => $this->analyzeStreaks($trader),
            'behavioral_pattern_analysis' => $this->analyzeBehavioralPatterns($trader),
            'summary' => $this->generateSummary($trader),
        ];
    }

    /**
     * Win Rate Analysis
     * < 40% → Critical warning
     * 40%-55% → Needs improvement
     * >= 55% → Strong
     */
    protected function analyzeWinRate(User $trader): array
    {
        $winRate = $this->analyticsService->getWinRate($trader);
        $totalTrades = $this->analyticsService->getTotalTrades($trader);

        $severity = 'neutral';
        $icon = '📊';
        $suggestion = '';

        if ($winRate < 40) {
            $severity = 'critical';
            $icon = '🚨';
            $suggestion = "CRITICAL: Win rate below 40%. Immediately review entry criteria and stop trading new setups until pattern recognition improves. Focus on demo trading and journal review.";
        } elseif ($winRate < 55) {
            $severity = 'warning';
            $icon = '⚠️';
            $suggestion = "Win rate needs improvement. Review winning trades for common patterns. Consider tightening entry rules and improving trade selection criteria.";
        } else {
            $severity = 'good';
            $icon = '✅';
            $suggestion = "Strong win rate. Maintain current discipline and continue following your trading plan.";
        }

        return [
            'metric' => 'Win Rate',
            'value' => round($winRate, 2) . '%',
            'severity' => $severity,
            'icon' => $icon,
            'suggestion' => $suggestion,
            'context' => "Based on {$totalTrades} trades",
        ];
    }

    /**
     * Risk-Reward Analysis
     * < 1:1 → Negative, poor risk quality
     * 1:1 to 1:1.5 → Acceptable but needs optimization
     * >= 1:1.5 → Good
     * >= 1:2 → Excellent
     */
    protected function analyzeRiskReward(User $trader): array
    {
        $avgRR = $this->analyticsService->getAverageRiskReward($trader);

        $severity = 'neutral';
        $icon = '⚖️';
        $suggestion = '';

        if ($avgRR < 1) {
            $severity = 'critical';
            $icon = '🚨';
            $suggestion = "CRITICAL: Risk-reward ratio below 1:1 indicates poor risk quality. Stops are too wide or targets too close. Review and adjust stop-loss placement immediately.";
        } elseif ($avgRR < 1.5) {
            $severity = 'warning';
            $icon = '⚠️';
            $suggestion = "R:R ratio needs optimization. Consider wider profit targets or tighter stops. Aim for minimum 1:1.5 to improve profitability.";
        } elseif ($avgRR < 2) {
            $severity = 'good';
            $icon = '✅';
            $suggestion = "Good risk-reward ratio. This provides solid foundation for profitability.";
        } else {
            $severity = 'excellent';
            $icon = '🌟';
            $suggestion = "Excellent R:R ratio. Even with lower win rates, this can generate consistent profits. Maintain this discipline.";
        }

        return [
            'metric' => 'Risk:Reward Ratio',
            'value' => '1:' . round($avgRR, 2),
            'severity' => $severity,
            'icon' => $icon,
            'suggestion' => $suggestion,
        ];
    }

    /**
     * Expectancy Analysis
     */
    protected function analyzeExpectancy(User $trader): array
    {
        $expectancy = $this->analyticsService->getExpectancy($trader);

        $severity = 'neutral';
        $icon = '📈';
        $suggestion = '';

        if ($expectancy < 0) {
            $severity = 'critical';
            $icon = '🚨';
            $suggestion = "CRITICAL: Negative expectancy means this is a losing system. Stop trading immediately and review both entry criteria and risk management.";
        } elseif ($expectancy < 10) {
            $severity = 'warning';
            $icon = '⚠️';
            $suggestion = "Low expectancy. While positive, profits per trade are minimal. Focus on improving win rate or risk-reward ratio.";
        } else {
            $severity = 'good';
            $icon = '✅';
            $suggestion = "Positive expectancy indicates profitable system. Continue executing your plan with discipline.";
        }

        return [
            'metric' => 'Expectancy',
            'value' => '$' . number_format($expectancy, 2) . ' per trade',
            'severity' => $severity,
            'icon' => $icon,
            'suggestion' => $suggestion,
        ];
    }

    /**
     * Profit Factor Analysis
     * < 1.0 → Losing system
     * 1.0-1.5 → Weak profitability
     * >= 1.5 → Strong profitability
     */
    protected function analyzeProfitFactor(User $trader): array
    {
        $profitFactor = $this->analyticsService->getProfitFactor($trader);

        $severity = 'neutral';
        $icon = '💰';
        $suggestion = '';

        if ($profitFactor < 1) {
            $severity = 'critical';
            $icon = '🚨';
            $suggestion = "CRITICAL: Profit factor below 1.0 means losses exceed profits. This is a losing system. Halt trading and rebuild strategy from fundamentals.";
        } elseif ($profitFactor < 1.5) {
            $severity = 'warning';
            $icon = '⚠️';
            $suggestion = "Weak profitability. While profitable, the margin is thin. Focus on cutting losses faster or letting winners run longer.";
        } else {
            $severity = 'good';
            $icon = '✅';
            $suggestion = "Strong profit factor indicates healthy trading system. Profits significantly outweigh losses.";
        }

        return [
            'metric' => 'Profit Factor',
            'value' => round($profitFactor, 2),
            'severity' => $severity,
            'icon' => $icon,
            'suggestion' => $suggestion,
        ];
    }

    /**
     * Max Drawdown Analysis
     * >= 20% → Critical risk alert
     * 10%-20% → Needs review
     * < 10% → Healthy
     */
    protected function analyzeDrawdown(User $trader): array
    {
        $maxDrawdown = $this->analyticsService->getMaxDrawdown($trader);

        $severity = 'neutral';
        $icon = '📉';
        $suggestion = '';

        if ($maxDrawdown >= 20) {
            $severity = 'critical';
            $icon = '🚨';
            $suggestion = "CRITICAL: Drawdown exceeds 20%. Reduce position size immediately by 50%. Review risk management rules and consider taking a break to reset psychology.";
        } elseif ($maxDrawdown >= 10) {
            $severity = 'warning';
            $icon = '⚠️';
            $suggestion = "Drawdown approaching risk limit. Review trade frequency, position sizing, and stop-loss discipline. Consider reducing risk per trade.";
        } else {
            $severity = 'good';
            $icon = '✅';
            $suggestion = "Healthy drawdown levels. Current risk management is appropriate.";
        }

        return [
            'metric' => 'Max Drawdown',
            'value' => '$' . number_format($maxDrawdown, 2),
            'severity' => $severity,
            'icon' => $icon,
            'suggestion' => $suggestion,
        ];
    }

    /**
     * Recovery Factor Analysis
     */
    protected function analyzeRecoveryFactor(User $trader): array
    {
        $recoveryFactor = $this->analyticsService->getRecoveryFactor($trader);

        $icon = '🔄';
        $severity = $recoveryFactor >= 2 ? 'good' : 'warning';
        $suggestion = $recoveryFactor >= 2 
            ? "Good recovery factor. System recovers well from drawdowns."
            : "Low recovery factor. Consider improving profit-to-drawdown ratio through better risk management.";

        return [
            'metric' => 'Recovery Factor',
            'value' => round($recoveryFactor, 2),
            'severity' => $severity,
            'icon' => $icon,
            'suggestion' => $suggestion,
        ];
    }

    /**
     * Strategy/Tag Analysis
     */
    protected function analyzeStrategies(User $trader): array
    {
        $trades = $trader->trades()->with('tags')->get();
        $insights = [];

        // Group by strategy tag
        $byStrategy = $trades->groupBy(fn($trade) => $trade->strategy ?? 'No Strategy');

        foreach ($byStrategy as $strategy => $strategyTrades) {
            $winRate = ($strategyTrades->where('outcome', 'win')->count() / $strategyTrades->count()) * 100;
            $profitLoss = $strategyTrades->sum('profit_loss');
            $consecutiveLosses = 0;
            $maxConsecutiveLosses = 0;

            foreach ($strategyTrades->sortBy('entry_date') as $trade) {
                if ($trade->outcome->value === 'loss') {
                    $consecutiveLosses++;
                    $maxConsecutiveLosses = max($maxConsecutiveLosses, $consecutiveLosses);
                } else {
                    $consecutiveLosses = 0;
                }
            }

            $severity = 'neutral';
            $icon = '📋';
            $suggestion = '';

            if ($maxConsecutiveLosses >= 5) {
                $severity = 'critical';
                $icon = '⏸️';
                $suggestion = "{$strategy} has {$maxConsecutiveLosses} consecutive losses. PAUSE this setup and review its validity.";
            } elseif ($winRate < 40) {
                $severity = 'warning';
                $icon = '⚠️';
                $suggestion = "{$strategy} shows low win rate ({$winRate}%). Consider refining entry rules or removing from playbook.";
            } elseif ($profitLoss > 0) {
                $severity = 'good';
                $icon = '✅';
                $suggestion = "{$strategy} is profitable. Continue executing with discipline.";
            }

            if ($suggestion) {
                $insights[] = [
                    'strategy' => $strategy,
                    'trades' => $strategyTrades->count(),
                    'win_rate' => round($winRate, 1) . '%',
                    'profit_loss' => '$' . number_format($profitLoss, 2),
                    'severity' => $severity,
                    'icon' => $icon,
                    'suggestion' => $suggestion,
                ];
            }
        }

        return $insights;
    }

    /**
     * Session Analysis
     */
    protected function analyzeSessions(User $trader): array
    {
        $sessionPerf = $this->analyticsService->getSessionPerformance($trader);
        
        if (empty($sessionPerf)) {
            return [];
        }

        $bestSession = collect($sessionPerf)->sortByDesc('profit')->first();
        $mostTraded = collect($sessionPerf)->sortByDesc('trades')->first();

        $insights = [];

        if ($bestSession['session'] !== $mostTraded['session']) {
            $improvement = (($bestSession['profit'] - $mostTraded['profit']) / abs($mostTraded['profit']) * 100);
            
            $insights[] = [
                'metric' => 'Session Mismatch',
                'icon' => '💡',
                'severity' => 'warning',
                'suggestion' => "{$bestSession['session']} session shows " . round($improvement, 1) . "% better results than {$mostTraded['session']} (most traded). Focus trading hours on best-performing session.",
            ];
        }

        foreach($sessionPerf as $session) {
            if ($session['profit'] < 0) {
                $insights[] = [
                    'metric' => "Session: {$session['session']}",
                    'icon' => '🔴',
                    'severity' => 'warning',
                    'suggestion' => "{$session['session']} session is unprofitable. Avoid trading during this time or review why trades fail in this session.",
                ];
            }
        }

        return $insights;
    }

    /**
     * Emotional Pattern Analysis
     */
    protected function analyzeEmotions(User $trader): array
    {
        $trades = $trader->trades;
        $emotionGroups = $trades->groupBy('emotion');

        $insights = [];

        foreach ($emotionGroups as $emotion => $emotionTrades) {
            $winRate = ($emotionTrades->where('outcome', 'win')->count() / $emotionTrades->count()) * 100;
            $avgPL = $emotionTrades->avg('profit_loss');

            if ($emotion && strtolower($emotion) === 'revenge' || strtolower($emotion) === 'angry') {
                $insights[] = [
                    'emotion' => ucfirst($emotion),
                    'icon' => '😤',
                    'severity' => 'critical',
                    'suggestion' => "Revenge/angry trading detected in {$emotionTrades->count()} trades. Implement mandatory cooldown period after losses.",
                ];
            }

            if ($winRate < 30 && $emotionTrades->count() >=3) {
                $insights[] = [
                    'emotion' => ucfirst($emotion),
                    'icon' => '⚠️',
                    'severity' => 'warning',
                    'suggestion' => "Trading while feeling '{$emotion}' shows only {$winRate}% win rate. Avoid trading in this emotional state.",
                ];
            }
        }

        return $insights;
    }

    /**
     * Streak Analysis
     */
    protected function analyzeStreaks(User $trader): array
    {
        $streaks = $this->analyticsService->getStreaks($trader);

        $insights = [];

        if ($streaks['max_loss_streak'] >= 5) {
            $insights[] = [
                'metric' => 'Loss Streak',
                'icon' => '🔴',
                'severity' => 'critical',
                'value' => $streaks['max_loss_streak'] . ' consecutive losses',
                'suggestion' => "Experienced {$streaks['max_loss_streak']} consecutive losses. After 3-4 losses, take a break to reset psychology and review recent trades.",
            ];
        }

        if ($streaks['current_type'] === 'loss' && $streaks['current_streak'] >= 3) {
            $insights[] = [
                'metric' => 'Current Streak',
                'icon' => '⚠️',
                'severity' => 'warning',
                'value' => "Currently on {$streaks['current_streak']} loss streak",
                'suggestion' => "Stop trading immediately. Take 24-48 hour break and review last 10 trades before continuing.",
            ];
        }

        return $insights;
    }

    /**
     * Behavioral Pattern Detection
     */
    protected function analyzeBehavioralPatterns(User $trader): array
    {
        $trades = $trader->trades()->orderBy('entry_date')->get();
        $insights = [];

        // Revenge Trading Detection (trade < 10 minutes after loss)
        $revengeCount = 0;
        for ($i = 1; $i < $trades->count(); $i++) {
            $prevTrade = $trades[$i - 1];
            $currentTrade = $trades[$i];

            if ($prevTrade->outcome->value === 'loss') {
                $minutesSinceLoss = $prevTrade->entry_date->diffInMinutes($currentTrade->entry_date);
                if ($minutesSinceLoss < 10) {
                    $revengeCount++;
                }
            }
        }

        if ($revengeCount >= 3) {
            $insights[] = [
                'pattern' => 'Revenge Trading',
                'icon' => '🔴',
                'severity' => 'critical',
                'occurrences' => $revengeCount,
                'suggestion' => "Revenge trading detected {$revengeCount} times: trades opened within 10 minutes of a loss. Implement MANDATORY 2-hour cooldown after any loss.",
            ];
        }

        // Overtrading Detection (> 5 trades/day multiple times)
        $tradesByDay = $trades->groupBy(fn($t) => $t->entry_date->format('Y-m-d'));
        $overtradingDays = $tradesByDay->filter(fn($dayTrades) => $dayTrades->count() > 5)->count();

        if ($overtradingDays >= 3) {
            $insights[] = [
                'pattern' => 'Overtrading',
                'icon' => '⚠️',
                'severity' => 'warning',
                'occurrences' => $overtradingDays . ' days',
                'suggestion' => "Overtrading detected on {$overtradingDays} days (>5 trades/day). Set daily trade limit discipline. Quality over quantity.",
            ];
        }

        // Holding Losers Longer Than Winners
        $winDurations = $trades->where('outcome', 'win')->whereNotNull('exit_date')
            ->map(fn($t) => $t->entry_date->diffInHours($t->exit_date));
        $lossDurations = $trades->where('outcome', 'loss')->whereNotNull('exit_date')
            ->map(fn($t) => $t->entry_date->diffInHours($t->exit_date));

        if ($winDurations->isNotEmpty() && $lossDurations->isNotEmpty()) {
            $avgWinDuration = $winDurations->avg();
            $avgLossDuration = $lossDurations->avg();

            if ($avgLossDuration > $avgWinDuration * 1.5) {
                $insights[] = [
                    'pattern' => 'Holding Losers Too Long',
                    'icon' => '⏱️',
                    'severity' => 'warning',
                    'suggestion' => "Holding losing trades " . round($avgLossDuration / $avgWinDuration, 1) . "x longer than winners. Cut losses faster and respect stops.",
                ];
            }
        }

        return $insights;
    }

    /**
     * Generate overall summary
     */
    protected function generateSummary(User $trader): array
    {
        $winRate = $this->analyticsService->getWinRate($trader);
        $profitFactor = $this->analyticsService->getProfitFactor($trader);
        $expectancy = $this->analyticsService->getExpectancy($trader);

        $strengths = [];
        $weaknesses = [];

        // Identify strengths
        if ($winRate >= 55) $strengths[] = "High win rate ({$winRate}%)";
        if ($profitFactor >= 1.5) $strengths[] = "Strong profit factor ({$profitFactor})";
        if ($expectancy > 0) $strengths[] = "Positive expectancy";

        // Identify weaknesses
        if ($winRate < 40) $weaknesses[] = "Win rate below 40%";
        if ($profitFactor < 1) $weaknesses[] = "Losing system (PF < 1.0)";
        if ($expectancy < 0) $weaknesses[] = "Negative expectancy";

        return [
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
            'overall_assessment' => $this->getOverallAssessment($winRate, $profitFactor, $expectancy),
        ];
    }

    protected function getOverallAssessment($winRate, $profitFactor, $expectancy): string
    {
        if ($expectancy > 0 && $profitFactor >= 1.5) {
            return "This is a profitable trading system with solid fundamentals. Continue with current approach while maintaining discipline.";
        } elseif ($expectancy > 0 && $profitFactor >= 1.0) {
            return "Marginally profitable system. Focus on optimization through improved risk-reward or entry criteria.";
        } else {
            return "System requires significant review and adjustment. Consider returning to demo trading while fixing fundamental issues.";
        }
    }
}
