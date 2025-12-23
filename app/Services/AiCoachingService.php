<?php

namespace App\Services;

use App\Models\User;
use App\Models\Trade;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiCoachingService
{
    protected $performanceService;
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct(PerformanceAnalysisService $performanceService)
    {
        $this->performanceService = $performanceService;
        $this->apiKey = env('GEMINI_API_KEY');
        
        if (empty($this->apiKey)) {
            Log::warning('Gemini API Key is missing or empty in environment.');
        } else {
            Log::info('Gemini API Key loaded: ' . substr($this->apiKey, 0, 5) . '...');
        }
    }

    /**
     * Generate a structured feedback draft for a trader using Gemini AI
     */
    public function generateFeedbackDraft(User $trader, ?Trade $contextTrade = null): array
    {
        try {
            // 1. Gather Analysis Data
            $analysis = $this->performanceService->analyzeTraderPerformance($trader);
            $trades = $trader->trades()->latest()->take(10)->get();

            // 2. Construct Prompt
            $prompt = $this->constructPrompt($trader, $analysis, $trades, $contextTrade);

            // 3. Call Gemini API with Retry Logic (3 attempts)
            $response = Http::retry(3, 1000)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                // ... config
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error', ['body' => $response->body()]);
                // Return a friendly error message to the UI
                $errorBody = $response->json();
                $message = $errorBody['error']['message'] ?? 'Unknown API error';
                throw new \Exception("Gemini Service: {$message}");
            }

            // 4. Parse Response
            $responseData = $response->json();
            $generatedText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            // Log raw text
            Log::info('Gemini Raw Response:', ['text' => $generatedText]);

            // Robust JSON Cleaning
            // 1. Remove markdown code blocks (```json ... ```)
            $cleanedText = preg_replace('/```(?:json)?|```/i', '', $generatedText);
            // 2. Trim whitespace
            $cleanedText = trim($cleanedText);
            
            // 3. Attempt parse
            $parsedData = json_decode($cleanedText, true);

            // 4. Fallback if parse failed (sometimes AI adds extra text outside JSON)
            if (json_last_error() !== JSON_ERROR_NONE) {
                 // Try to extract just the JSON object {}
                if (preg_match('/\{[\s\S]*\}/', $cleanedText, $matches)) {
                    $parsedData = json_decode($matches[0], true);
                }
            }

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsedData)) {
                Log::error('JSON Decode Error: ' . json_last_error_msg());
                throw new \Exception('AI returned invalid format. Please try again.');
            }

            // 5. Transform to expected format or fallback
            return [
                'strengths' => $parsedData['strengths'] ?? [],
                'weaknesses' => $parsedData['weaknesses'] ?? [],
                'recommendations' => $parsedData['recommendations'] ?? [],
                'content' => $parsedData['content'] ?? 'AI generation incomplete.',
                'confidence_rating' => $parsedData['confidence_rating'] ?? 5,
                'source' => 'Gemini 1.5 Flash',
            ];

        } catch (\Exception $e) {
            Log::error('AI Generation Failed: ' . $e->getMessage());
            
            // Return error as a system notification in the payload
            return [
                'strengths' => [],
                'weaknesses' => [],
                'recommendations' => [],
                'content' => "System: " . $e->getMessage(),
                'confidence_rating' => 0,
                'source' => 'Error: Failed to generate draft',
            ];
        }
    }

    protected function constructPrompt(User $trader, array $analysis, Collection $trades, ?Trade $contextTrade): string
    {
        $metrics = [
            'Win Rate' => $analysis['win_rate_analysis']['value'],
            'Profit Factor' => $analysis['profit_factor_analysis']['value'],
            'Max Drawdown' => $analysis['drawdown_analysis']['value'],
            'Risk Reward' => $analysis['risk_reward_analysis']['value'],
            'Assessment' => $analysis['summary']['overall_assessment'],
        ];

        $behavioral = [];
        foreach ($analysis['behavioral_pattern_analysis'] as $p) {
            $behavioral[] = $p['pattern'] . " (" . $p['severity'] . ")";
        }

        $tradeContext = "";
        if ($contextTrade) {
            $tradeContext = "Focus specifically on the recent trade: Pair {$contextTrade->pair}, Outcome: {$contextTrade->outcome->value}, P/L: {$contextTrade->profit_loss}.";
        }

        return "You are a professional Forex Trading Performance Analyst. Analyze the following trader data and provide structured feedback.
        
        Trader Name: {$trader->name}
        Key Metrics: " . json_encode($metrics) . "
        Behavioral Issues: " . implode(", ", $behavioral) . "
        Recent Context: {$tradeContext}

        Output ONLY valid JSON with this structure:
        {
            \"strengths\": [\"string\", \"string\"],
            \"weaknesses\": [\"string\", \"string\"],
            \"recommendations\": [\"string\", \"string\"],
            \"content\": \"A professional, encouraging paragraph summarizing their performance and specific advice.\",
            \"confidence_rating\": integer (1-10, mainly based on data quality and performance consistency)
        }
        
        Keep points concise and actionable. Tone: Professional, constructive, coaching.";
    }

    protected function generateFallbackDraft(array $analysis): array
    {
        // Simple fallback using the analysis summary if API fails
        return [
            'strengths' => $analysis['summary']['strengths'] ?? ['Consistent tracking'],
            'weaknesses' => $analysis['summary']['weaknesses'] ?? ['Data analysis unavailable'],
            'recommendations' => ['Continue journaling trades', 'Review risk management rules'],
            'content' => $analysis['summary']['overall_assessment'] ?? 'Analysis unavailable.',
            'confidence_rating' => 5,
            'source' => 'Start-up Logic (Fallback)',
        ];
    }
}
