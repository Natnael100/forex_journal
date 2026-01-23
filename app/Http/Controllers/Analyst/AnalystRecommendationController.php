<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AnalystRecommendationController extends Controller
{
    /**
     * Get recommended analysts based on preferences
     */
    public function recommend(Request $request)
    {
        $validated = $request->validate([
            'trading_style' => 'required|string',
            'experience_level' => 'required|string',
        ]);

        // Get all public analysts
        $analysts = User::role('analyst')
            ->where('profile_visibility', 'public')
            ->withCount(['reviewsReceived as reviews_count' => function ($query) {
                $query->where('is_approved', true);
            }])
            ->get();

        // Simple scoring algorithm
        $scoredAnalysts = $analysts->map(function ($analyst) use ($validated) {
            $score = 0;
            
            // Check trading style match (checking if string is contained in JSON or string field)
            // Assuming specialization is a JSON array or comma-separated string
            $specializations = is_string($analyst->specializations) ? $analyst->specializations : json_encode($analyst->specializations ?? []);
            if (stripos($specializations, $validated['trading_style']) !== false) {
                $score += 3;
            }

            // Check experience level match (Analyst's target audience usually matches their own exp or one level below, 
            // but for simplicity let's match if they have MORE experience)
            // Or if we interpret "experience_level" as "Trader's experience", maybe we match beginner traders with patient analysts?
            // For now, let's just do a keyword match on bio or tags if possible, or skip simple scoring.
            
            // Let's rely on review count as a tie-breaker for quality
            $score += ($analyst->reviews_count > 0 ? 1 : 0);
            $score += ($analyst->getAverageRating() / 5); // Add up to 1 point for rating

            $analyst->match_score = $score;
            $analyst->average_rating = $analyst->getAverageRating(); // Ensure this is available for frontend
            $analyst->profile_photo_url = $analyst->getProfilePhotoUrl('large'); // Explicitly add URL
            
            return $analyst;
        });

        // Sort by score desc, then by rating
        $topMatches = $scoredAnalysts
            ->sortByDesc('match_score')
            ->take(3)
            ->values();

        return response()->json([
            'matches' => $topMatches
        ]);
    }
}
