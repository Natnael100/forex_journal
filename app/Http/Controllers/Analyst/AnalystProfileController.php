<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\ProfilePhotoService;

class AnalystProfileController extends Controller
{
    /**
     * Show analyst marketplace (browse all public analysts)
     */
    protected $photoService;

    public function __construct(ProfilePhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    /**
     * Show analyst marketplace (browse all public analysts)
     */
    public function index()
    {
        $analysts = User::role('analyst')
            ->where('profile_visibility', 'public')
            ->where('analyst_verification_status', 'verified')
            ->withCount(['reviewsReceived as reviews_count' => function ($query) {
                $query->where('is_approved', true);
            }])
            ->get()
            ->map(function ($analyst) {
                $analyst->average_rating = $analyst->getAverageRating();
                return $analyst;
            })
            ->sortByDesc('average_rating');

        return view('analysts.index', compact('analysts'));
    }

    /**
     * Show individual analyst public profile
     */
    public function show($username)
    {
        $analyst = User::where('username', $username)
            ->orWhere('id', $username)
            ->firstOrFail();

        if (!$analyst->hasRole('analyst') || $analyst->profile_visibility !== 'public') {
            abort(404, 'Analyst not found');
        }

        $analyst->load(['reviewsReceived' => function ($query) {
            $query->where('is_approved', true)->latest()->take(10);
        }]);

        $stats = [
            'average_rating' => $analyst->getAverageRating(),
            'total_reviews' => $analyst->reviewsReceived()->approved()->count(),
            'active_clients' => $analyst->subscriptionsAsAnalyst()->active()->count(),
            'years_experience' => $analyst->years_experience ?? 0,
        ];

        return view('analysts.show', compact('analyst', 'stats'));
    }

    /**
     * Show analyst profile edit page
     */
    public function edit()
    {
        $analyst = Auth::user();

        if (!$analyst->hasRole('analyst')) {
            abort(403);
        }

        return view('analyst.profile.edit', compact('analyst'));
    }

    /**
     * Update analyst profile
     */
    public function update(Request $request)
    {
        $analyst = Auth::user();

        if (!$analyst->hasRole('analyst')) {
            abort(403);
        }

        $validated = $request->validate([
            'bio' => 'nullable|string|max:1000',
            'specializations' => 'nullable|array',
            'certifications' => 'nullable|array',
            'years_experience' => 'nullable|integer|min:0|max:50',
            'hourly_rate' => 'nullable|numeric|min:0|max:500',
            'profile_visibility' => 'boolean',
            'offering_details' => 'nullable|array',
            'photo' => 'nullable|image|max:' . config('profile.photo.max_size', 2048),
            'cover' => 'nullable|image|max:' . config('profile.cover.max_size', 4096),
        ]);

        // Manually handle visibility mapping
        $validated['profile_visibility'] = $request->boolean('profile_visibility') ? 'public' : 'private';

        // Remove photo/cover from array to avoid mass assignment issues if not in fillable (though they are not in fillable anyway usually)
        // But better safe:
        $currentPhoto = $request->file('photo');
        $currentCover = $request->file('cover');
        
        unset($validated['photo']);
        unset($validated['cover']);

        $analyst->update($validated);

        // Handle Profile Photo Upload
        if ($request->hasFile('photo')) {
            if ($this->photoService->validatePhoto($request->file('photo'))) {
                $this->photoService->uploadProfilePhoto($request->file('photo'), $analyst);
            } else {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['photo' => 'Invalid photo. Must be JPG, PNG, or WEBP under 2MB.']);
            }
        }

        // Handle Cover Photo Upload
        if ($request->hasFile('cover')) {
            if ($this->photoService->validateCover($request->file('cover'))) {
                $this->photoService->uploadCoverPhoto($request->file('cover'), $analyst);
            } else {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['cover' => 'Invalid cover photo. Must be JPG, PNG, or WEBP under 4MB.']);
            }
        }

        return redirect()
            ->route('analysts.show', $analyst->username ?? $analyst->id)
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update analyst plans
     */
    public function updatePlans(Request $request)
    {
        $analyst = Auth::user();

        if (!$analyst->hasRole('analyst')) {
            abort(403);
        }

        $validated = $request->validate([
            'plans' => 'required|array',
            'plans.*.price' => 'nullable|numeric|min:0',
            'plans.*.features' => 'nullable|array',
            'plans.*.is_active' => 'nullable',
        ]);

        // Update or create each tier
        foreach (['basic', 'premium', 'elite'] as $tier) {
            $data = $request->input("plans.{$tier}");
            
            // Skip if price is missing (tier not configured)
            if (!isset($data['price']) || $data['price'] === '' || $data['price'] === null) {
                continue;
            }

            $analyst->plans()->updateOrCreate(
                ['tier' => $tier],
                [
                    'price' => $data['price'],
                    'features' => $data['features'] ?? [],
                    'is_active' => isset($data['is_active']) && $data['is_active'] == '1',
                ]
            );
        }

        return redirect()->back()->with('success', 'Subscription plans updated successfully.');
    }
}

