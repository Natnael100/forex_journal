<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * BehavioralAnalysisService
 * 
 * Analyzes qualitative trade data to detect behavioral patterns,
 * emotional trends, discipline issues, and learning progression.
 */
class BehavioralAnalysisService
{
    /**
     * Main analysis method - coordinates all behavioral analyses
     */
    public function analyze(Collection $trades): array
    {
        if ($trades->isEmpty()) {
            return $this->getEmptyAnalysis();
        }

        return [
            'emotional_patterns' => $this->detectEmotionalPatterns($trades),
            'violation_analysis' => $this->analyzeViolations($trades),
            'learning_progression' => $this->analyzeLearningProgression($trades),
            'discipline_metrics' => $this->calculateDisciplineMetrics($trades),
            'behavior_outcome_correlation' => $this->correlateBehaviorWithOutcomes($trades),
            'summary' => $this->generateBehavioralSummary($trades),
        ];
    }

    /**
     * Detect emotional patterns in trading behavior
     */
    public function detectEmotionalPatterns(Collection $trades): array
    {
        $patterns = [];
        
        // Keywords to detect
        $fearKeywords = ['fear', 'afraid', 'anxious', 'nervous', 'scared', 'worried'];
        $fomoKeywords = ['fomo', 'missing out', 'rush', 'hurry', 'impulsive'];
        $confidenceKeywords = ['confident', 'certain', 'sure', 'ready', 'prepared'];
        $revengeKeywords = ['revenge', 'get back', 'make up', 'recover losses'];

        // Analyze pre-trade emotions
        $fearTrades = $this->countEmotionMatches($trades, 'pre_trade_emotion', $fearKeywords);
        $fomoTrades = $this->countEmotionMatches($trades, 'pre_trade_emotion', $fomoKeywords);
        $confidentTrades = $this->countEmotionMatches($trades, 'pre_trade_emotion', $confidenceKeywords);
        $revengeTrades = $this->countEmotionMatches($trades, 'pre_trade_emotion', $revengeKeywords);

        // Calculate win rates for each emotional state
        if ($fearTrades->count() > 0) {
            $patterns[] = [
                'pattern' => 'Fear-based trading',
                'frequency' => $fearTrades->count(),
                'percentage' => round(($fearTrades->count() / $trades->count()) * 100, 1),
                'win_rate' => $this->calculateWinRate($fearTrades),
                'avg_pl' => $fearTrades->avg('profit_loss'),
                'severity' => $this->assessSeverity($fearTrades->count(), $trades->count(), $this->calculateWinRate($fearTrades)),
                'trades_affected' => $fearTrades->count(),
            ];
        }

        if ($fomoTrades->count() > 0) {
            $patterns[] = [
                'pattern' => 'FOMO (Fear of Missing Out)',
                'frequency' => $fomoTrades->count(),
                'percentage' => round(($fomoTrades->count() / $trades->count()) * 100, 1),
                'win_rate' => $this->calculateWinRate($fomoTrades),
                'avg_pl' => $fomoTrades->avg('profit_loss'),
                'severity' => $this->assessSeverity($fomoTrades->count(), $trades->count(), $this->calculateWinRate($fomoTrades)),
                'trades_affected' => $fomoTrades->count(),
            ];
        }

        if ($revengeTrades->count() > 0) {
            $patterns[] = [
                'pattern' => 'Revenge trading',
                'frequency' => $revengeTrades->count(),
                'percentage' => round(($revengeTrades->count() / $trades->count()) * 100, 1),
                'win_rate' => $this->calculateWinRate($revengeTrades),
                'avg_pl' => $revengeTrades->avg('profit_loss'),
                'severity' => 'high', // Revenge trading is always severe
                'trades_affected' => $revengeTrades->count(),
            ];
        }

        // Baseline: Confident trading performance
        if ($confidentTrades->count() > 0) {
            $patterns[] = [
                'pattern' => 'Confident/Prepared trading',
                'frequency' => $confidentTrades->count(),
                'percentage' => round(($confidentTrades->count() / $trades->count()) * 100, 1),
                'win_rate' => $this->calculateWinRate($confidentTrades),
                'avg_pl' => $confidentTrades->avg('profit_loss'),
                'severity' => 'positive',
                'trades_affected' => $confidentTrades->count(),
            ];
        }

        return $patterns;
    }

    /**
     * Analyze plan violations and discipline issues
     */
    public function analyzeViolations(Collection $trades): array
    {
        $violatedTrades = $trades->where('followed_plan', false);
        
        if ($violatedTrades->isEmpty()) {
            return [
                'total_violations' => 0,
                'violation_rate' => 0,
                'common_violations' => [],
                'impact_on_performance' => 'N/A - No violations recorded',
            ];
        }

        // Categorize violations by reason
        $violationReasons = $violatedTrades->whereNotNull('violation_reason')
            ->groupBy('violation_reason')
            ->map(function ($group) use ($trades) {
                return [
                    'reason' => $group->first()->violation_reason,
                    'count' => $group->count(),
                    'percentage' => round(($group->count() / $trades->count()) * 100, 1),
                    'win_rate' => $this->calculateWinRate($group),
                    'avg_pl' => round($group->avg('profit_loss'), 2),
                ];
            })
            ->sortByDesc('count')
            ->values()
            ->take(5) // Top 5 violation types
            ->toArray();

        // Compare performance: following plan vs violating
        $followedPlanTrades = $trades->where('followed_plan', true);
        $followedPlanWinRate = $followedPlanTrades->isNotEmpty() ? $this->calculateWinRate($followedPlanTrades) : 0;
        $violatedWinRate = $this->calculateWinRate($violatedTrades);

        return [
            'total_violations' => $violatedTrades->count(),
            'violation_rate' => round(($violatedTrades->count() / $trades->count()) * 100, 1),
            'common_violations' => $violationReasons,
            'performance_comparison' => [
                'followed_plan_win_rate' => $followedPlanWinRate,
                'violated_plan_win_rate' => $violatedWinRate,
                'win_rate_impact' => round($followedPlanWinRate - $violatedWinRate, 1),
            ],
            'impact_on_performance' => $followedPlanWinRate > $violatedWinRate 
                ? "Negative impact: {$followedPlanWinRate}% win rate when following plan vs {$violatedWinRate}% when violating"
                : "Neutral or positive (unusual - may indicate plan needs revision)",
        ];
    }

    /**
     * Analyze learning progression through mistakes_lessons field
     */
    public function analyzeLearningProgression(Collection $trades): array
    {
        $tradesWithLessons = $trades->whereNotNull('mistakes_lessons')
            ->where('mistakes_lessons', '!=', '');

        $engagementRate = $trades->count() > 0 
            ? round(($tradesWithLessons->count() / $trades->count()) * 100, 1)
            : 0;

        // Check for repeated mistakes (simple keyword matching)
        $lessonTexts = $tradesWithLessons->pluck('mistakes_lessons')->map(fn($text) => strtolower($text));
        $repeatedIssues = $this->detectRepeatedIssues($lessonTexts);

        return [
            'engagement_rate' => $engagementRate,
            'trades_with_lessons' => $tradesWithLessons->count(),
            'total_trades' => $trades->count(),
            'self_awareness_level' => $this->assessSelfAwareness($engagementRate),
            'repeated_issues' => $repeatedIssues,
            'learning_trend' => $this->calculateLearningTrend($trades),
        ];
    }

    /**
     * Calculate overall discipline metrics
     */
    public function calculateDisciplineMetrics(Collection $trades): array
    {
        $planAdherenceRate = $trades->where('followed_plan', true)->count() / max($trades->count(), 1) * 100;
        
        // Check SL/TP respect (if stop_loss and take_profit are set, were they hit or moved?)
        $tradesWithSL = $trades->whereNotNull('stop_loss')->where('stop_loss', '!=', 0);
        $slRespected = $tradesWithSL->filter(function ($trade) {
            // Logic: if loss trade and SL was set, assume it was respected
            // This is simplified - real logic would check if exit_price matches SL
            return $trade->outcome === 'loss' || $trade->outcome === 'win';
        })->count();

        $slRespectRate = $tradesWithSL->count() > 0 
            ? round(($slRespected / $tradesWithSL->count()) * 100, 1)
            : 100;

        return [
            'plan_adherence_rate' => round($planAdherenceRate, 1),
            'sl_tp_respect_rate' => $slRespectRate,
            'overall_discipline_score' => round(($planAdherenceRate + $slRespectRate) / 2, 1),
            'discipline_grade' => $this->getDisciplineGrade(($planAdherenceRate + $slRespectRate) / 2),
        ];
    }

    /**
     * Correlate behavioral factors with trading outcomes
     */
    public function correlateBehaviorWithOutcomes(Collection $trades): array
    {
        $correlations = [];

        // Emotion vs Outcome
        $emotionalTrades = $trades->whereNotNull('pre_trade_emotion');
        if ($emotionalTrades->count() > 5) {
            $positiveEmotions = ['confident', 'prepared', 'calm', 'patient'];
            $negativeEmotions = ['fear', 'anxious', 'fomo', 'revenge'];

            $positiveEmotionTrades = $this->countEmotionMatches($emotionalTrades, 'pre_trade_emotion', $positiveEmotions);
            $negativeEmotionTrades = $this->countEmotionMatches($emotionalTrades, 'pre_trade_emotion', $negativeEmotions);

            $correlations[] = [
                'factor' => 'Pre-trade emotions',
                'positive_emotion_win_rate' => $this->calculateWinRate($positiveEmotionTrades),
                'negative_emotion_win_rate' => $this->calculateWinRate($negativeEmotionTrades),
                'correlation_strength' => 'moderate', // Simplified
            ];
        }

        // Plan adherence vs Outcome
        $followedPlan = $trades->where('followed_plan', true);
        $violatedPlan = $trades->where('followed_plan', false);

        if ($followedPlan->count() > 0 && $violatedPlan->count() > 0) {
            $correlations[] = [
                'factor' => 'Plan adherence',
                'followed_plan_win_rate' => $this->calculateWinRate($followedPlan),
                'violated_plan_win_rate' => $this->calculateWinRate($violatedPlan),
                'correlation_strength' => 'strong',
            ];
        }

        return $correlations;
    }

    /**
     * Generate a behavioral summary for AI prompt
     */
    protected function generateBehavioralSummary(Collection $trades): string
    {
        $emotional = $this->detectEmotionalPatterns($trades);
        $violations = $this->analyzeViolations($trades);
        $discipline = $this->calculateDisciplineMetrics($trades);

        $summary = "Behavioral Analysis Summary:\n";
        $summary .= "- Discipline Score: {$discipline['overall_discipline_score']}% ({$discipline['discipline_grade']})\n";
        $summary .= "- Plan Adherence: {$discipline['plan_adherence_rate']}%\n";
        $summary .= "- Violation Rate: {$violations['violation_rate']}%\n";

        if (!empty($emotional)) {
            $summary .= "- Primary Emotional Patterns: ";
            $summary .= implode(', ', array_column(array_slice($emotional, 0, 3), 'pattern'));
        }

        return $summary;
    }

    // ==================== HELPER METHODS ====================

    protected function countEmotionMatches(Collection $trades, string $field, array $keywords): Collection
    {
        return $trades->filter(function ($trade) use ($field, $keywords) {
            $emotionText = strtolower($trade->$field ?? '');
            foreach ($keywords as $keyword) {
                if (str_contains($emotionText, $keyword)) {
                    return true;
                }
            }
            return false;
        });
    }

    protected function calculateWinRate(Collection $trades): float
    {
        if ($trades->isEmpty()) {
            return 0;
        }
        
        $wins = $trades->where('outcome', 'win')->count();
        return round(($wins / $trades->count()) * 100, 1);
    }

    protected function assessSeverity(int $count, int $total, float $winRate): string
    {
        $percentage = ($count / $total) * 100;
        
        if ($percentage > 40 || $winRate < 40) {
            return 'high';
        } elseif ($percentage > 20 || $winRate < 50) {
            return 'medium';
        }
        
        return 'low';
    }

    protected function assessSelfAwareness(float $engagementRate): string
    {
        if ($engagementRate >= 80) return 'excellent';
        if ($engagementRate >= 60) return 'good';
        if ($engagementRate >= 40) return 'moderate';
        return 'low';
    }

    protected function detectRepeatedIssues(Collection $lessonTexts): array
    {
        // Simple keyword frequency analysis
        $commonIssues = ['entry', 'exit', 'patience', 'risk', 'emotion', 'stop', 'fomo'];
        $repeated = [];

        foreach ($commonIssues as $issue) {
            $count = $lessonTexts->filter(fn($text) => str_contains($text, $issue))->count();
            if ($count >= 3) {
                $repeated[] = [
                    'issue' => ucfirst($issue),
                    'mentions' => $count,
                ];
            }
        }

        return $repeated;
    }

    protected function calculateLearningTrend(Collection $trades): string
    {
        // Compare first half vs second half engagement
        $halfPoint = floor($trades->count() / 2);
        $firstHalf = $trades->take($halfPoint);
        $secondHalf = $trades->skip($halfPoint);

        $firstHalfEngagement = $firstHalf->whereNotNull('mistakes_lessons')->count() / max($firstHalf->count(), 1);
        $secondHalfEngagement = $secondHalf->whereNotNull('mistakes_lessons')->count() / max($secondHalf->count(), 1);

        if ($secondHalfEngagement > $firstHalfEngagement + 0.1) {
            return 'improving';
        } elseif ($secondHalfEngagement < $firstHalfEngagement - 0.1) {
            return 'declining';
        }
        
        return 'stable';
    }

    protected function getDisciplineGrade(float $score): string
    {
        if ($score >= 90) return 'A (Excellent)';
        if ($score >= 80) return 'B (Good)';
        if ($score >= 70) return 'C (Fair)';
        if ($score >= 60) return 'D (Needs Improvement)';
        return 'F (Poor)';
    }

    protected function getEmptyAnalysis(): array
    {
        return [
            'emotional_patterns' => [],
            'violation_analysis' => [
                'total_violations' => 0,
                'violation_rate' => 0,
                'common_violations' => [],
                'impact_on_performance' => 'Insufficient data',
            ],
            'learning_progression' => [
                'engagement_rate' => 0,
                'trades_with_lessons' => 0,
                'total_trades' => 0,
                'self_awareness_level' => 'unknown',
                'repeated_issues' => [],
                'learning_trend' => 'unknown',
            ],
            'discipline_metrics' => [
                'plan_adherence_rate' => 0,
                'sl_tp_respect_rate' => 0,
                'overall_discipline_score' => 0,
                'discipline_grade' => 'N/A',
            ],
            'behavior_outcome_correlation' => [],
            'summary' => 'No trades available for behavioral analysis',
        ];
    }
}
