<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalystServicesController extends Controller
{
    /**
     * Display a listing of the services/features the analyst provides.
     */
    public function index()
    {
        $analyst = Auth::user();
        
        // Define all system features with details
        $allFeatures = [
            'text_feedback' => [
                'name' => 'Text Feedback',
                'description' => 'Provide personalized written feedback on trader journals.',
                'icon' => 'card-text', 
                'tier' => 'basic'
            ],
            'email_support' => [
                'name' => 'Email Support',
                'description' => 'Priority support via email for subscriber questions.',
                'icon' => 'envelope',
                'tier' => 'basic'
            ],
            'monthly_review' => [
                'name' => 'Monthly Review',
                'description' => 'Comprehensive performance review at the end of each month.',
                'icon' => 'calendar-check',
                'tier' => 'basic'
            ],
            'weekly_checkins' => [
                'name' => 'Weekly Check-ins',
                'description' => 'Regular touchpoints to track progress and adjust goals.',
                'icon' => 'calendar-week',
                'tier' => 'premium'
            ],
            'risk_rules' => [
                'name' => 'Risk Rules',
                'description' => 'Set and enforce risk management rules for traders.',
                'icon' => 'shield-check',
                'tier' => 'premium'
            ],
            'custom_reports' => [
                'name' => 'Custom Reports',
                'description' => 'Generate tailored analytics reports for specific needs.',
                'icon' => 'file-bar-graph',
                'tier' => 'premium'
            ],
            'video_consultations' => [
                'name' => 'Video Consultations',
                'description' => 'Live 1-on-1 video coaching sessions.',
                'icon' => 'camera-video',
                'tier' => 'elite'
            ],
            'guided_journaling' => [
                'name' => 'Guided Journaling',
                'description' => 'Provide specific prompts and focus areas for journaling.',
                'icon' => 'journal-medical',
                'tier' => 'elite'
            ],
            'direct_access' => [
                'name' => 'Direct Access',
                'description' => 'Direct messaging priority and 24/7 availability.',
                'icon' => 'chat-dots',
                'tier' => 'basic'
            ],
        ];

        // Check which features are active based on Analyst's Plans
        // We check if the feature is present in ANY of the analyst's active plans
        $activeFeatures = [];
        
        // Helper to check plan status
        $plans = [
            'basic' => $analyst->getPlan('basic'),
            'premium' => $analyst->getPlan('premium'),
            'elite' => $analyst->getPlan('elite'),
        ];
        
        foreach ($allFeatures as $key => $feature) {
            $isEnabled = false;
            $enabledInTiers = [];

            foreach ($plans as $tierName => $plan) {
                if ($plan && $plan->is_active && in_array($key, $plan->features ?? [])) {
                    $isEnabled = true;
                    $enabledInTiers[] = ucfirst($tierName);
                }
            }
            
            $activeFeatures[$key] = [
                'is_enabled' => $isEnabled,
                'tiers' => $enabledInTiers
            ];
        }

        return view('analyst.services.index', compact('analyst', 'allFeatures', 'activeFeatures', 'plans'));
    }
}
