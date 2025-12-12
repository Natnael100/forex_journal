<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ProfilePhotoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    protected $photoService;

    public function __construct(ProfilePhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    /**
     * Show public profile
     */
    public function show(string $username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        // Check if user can view this profile
        $this->authorize('view', $user);
        
        // Calculate profile completion
        $completion = $user->calculateProfileCompletion();
        
        // Get recent trades if trader
        $recentTrades = null;
        if ($user->hasRole('trader')) {
            $recentTrades = $user->trades()
                ->latest()
                ->take(5)
                ->get();
        }
        
        return view('profile.show', compact('user', 'completion', 'recentTrades'));
    }

    /**
     * Show profile settings
     */
    public function edit()
    {
        $user = Auth::user();
        $user->ensureUsername(); // Auto-generate if needed
        
        $completion = $user->calculateProfileCompletion();
        
        return view('profile.edit', compact('user', 'completion'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'min:' . config('profile.username.min', 3),
                'max:' . config('profile.username.max', 20),
                'regex:' . config('profile.username.pattern', '/^[a-z0-9_]+$/'),
                Rule::unique('users')->ignore($user->id),
                function ($attribute, $value, $fail) {
                    $reserved = config('profile.username.reserved', []);
                    if (in_array(strtolower($value), $reserved)) {
                        $fail('This username is reserved.');
                    }
                },
            ],
            'bio' => 'nullable|string|max:' . config('profile.bio.max_length', 500),
            'country' => 'nullable|string|max:100',
            'timezone' => 'nullable|string',
            'experience_level' => 'nullable|in:beginner,intermediate,advanced',
            'trading_style' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'preferred_sessions' => 'nullable|array',
            'favorite_pairs' => 'nullable|array',
            'profile_tags' => 'nullable|array',
            'social_links' => 'nullable|array',
            'profile_visibility' => 'required|in:public,analyst_only,private',
            'show_last_active' => 'boolean',
        ]);
        
        $user->update($validated);
        
        // Check if profile is now 100% complete
        if ($user->calculateProfileCompletion() >= 100 && !$user->isProfileComplete()) {
            $user->update(['profile_completed_at' => now()]);
        }
        
        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:' . config('profile.photo.max_size', 2048),
        ]);
        
        $user = Auth::user();
        
        if (!$this->photoService->validatePhoto($request->file('photo'))) {
            return back()->withErrors(['photo' => 'Invalid photo file.']);
        }
        
        $this->photoService->uploadProfilePhoto($request->file('photo'), $user);
        
        return back()->with('success', 'Profile photo updated!');
    }

    /**
     * Upload cover photo
     */
    public function uploadCover(Request $request)
    {
        $request->validate([
            'cover' => 'required|image|max:' . config('profile.cover.max_size', 4096),
        ]);
        
        $user = Auth::user();
        
        if (!$this->photoService->validateCover($request->file('cover'))) {
            return back()->withErrors(['cover' => 'Invalid cover photo file.']);
        }
        
        $this->photoService->uploadCoverPhoto($request->file('cover'), $user);
        
        return back()->with('success', 'Cover photo updated!');
    }

    /**
     * Delete profile photo
     */
    public function deletePhoto()
    {
        $user = Auth::user();
        $this->photoService->deletePhoto($user, 'profile');
        
        return back()->with('success', 'Profile photo removed.');
    }

    /**
     * Delete cover photo
     */
    public function deleteCover()
    {
        $user = Auth::user();
        $this->photoService->deletePhoto($user, 'cover');
        
        return back()->with('success', 'Cover photo removed.');
    }
}
