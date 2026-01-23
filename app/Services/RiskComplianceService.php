<?php

namespace App\Services;

use App\Models\Trade;
use App\Models\User;
use App\Models\RiskRule;
use Carbon\Carbon;

class RiskComplianceService
{
    /**
     * Check if a proposed trade violates any active rules for the trader.
     * Returns an array with ['compliant' => bool, 'reason' => string|null].
     */
    public function checkCompliance(User $trader, array $tradeData): array
    {
        try {
            // Fetch active rules for this trader
            $rules = $trader->riskRules()->where('is_active', true)->get();
        } catch (\Illuminate\Database\QueryException $e) {
            // If table doesn't exist (e.g. pending migration), assume compliant
            return ['compliant' => true, 'reason' => null];
        }

        if ($rules->isEmpty()) {
            return ['compliant' => true, 'reason' => null];
        }

        // Feature Gating: Only apply rules if user has "Automated Risk Rules" feature (Premium+)
        // Assuming relationship: User -> Subscription -> Plan
        $subscription = $trader->activeSubscription; // Ensure this relationship exists or use simpler query
        
        // If no active subscription or feature not present, skip enforcement
        // This effectively makes Risk Rules a paid feature.
        if (!$subscription || !$subscription->hasFeature('risk_rules')) {
            return ['compliant' => true, 'reason' => null];
        }

        foreach ($rules as $rule) {
            $violation = $this->evaluateRule($rule, $tradeData);
            
            if ($violation) {
                return [
                    'compliant' => false,
                    'is_hard_stop' => $rule->is_hard_stop, // Pass this info for controller logic
                    'reason' => $violation
                ];
            }
        }

        return ['compliant' => true, 'reason' => null];
    }

    /**
     * Evaluate a single rule against trade data.
     */
    protected function evaluateRule(RiskRule $rule, array $data): ?string
    {
        switch ($rule->rule_type) {
            case 'max_risk_percent':
                // Data needs 'risk_percentage'
                if (isset($data['risk_percentage']) && $data['risk_percentage'] > $rule->value) {
                    return "Risk of {$data['risk_percentage']}% exceeds limit of {$rule->value}%.";
                }
                break;

            case 'restricted_session':
                // Data needs 'session'. Rule param has restricted session (e.g., 'asia')
                if (isset($data['session']) && strtolower($data['session']) === strtolower($rule->parameters)) {
                    return "Trading restricted during {$rule->parameters} session.";
                }
                break;

            case 'restricted_pair':
                // Data needs 'pair'. Rule param has restricted pair (e.g., 'XAUUSD')
                if (isset($data['pair']) && strtolower($data['pair']) === strtolower($rule->parameters)) {
                    return "Trading restricted on pair {$rule->parameters}.";
                }
                break;
                
            case 'max_lot_size':
                 if (isset($data['lot_size']) && $data['lot_size'] > $rule->value) {
                    return "Lot size {$data['lot_size']} exceeds limit of {$rule->value}.";
                 }
                 break;
        }

        return null; // No violation
    }
}
