<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Display the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:trader,analyst'],
            // Common profile fields
            'username' => ['required', 'string', 'min:3', 'max:20', 'regex:/^[a-z0-9_]+$/', 'unique:users'],
            'country' => ['nullable', 'string', 'max:100'],
            'timezone' => ['nullable', 'string', 'max:100'],
        ];

        // Trader-specific fields
        if ($request->role === 'trader') {
            $validationRules += [
                'experience_level' => ['nullable', 'in:beginner,intermediate,advanced'],
                'trading_style' => ['nullable', 'string', 'max:100'],
                'specialization' => ['nullable', 'string', 'max:100'],
                'preferred_sessions' => ['nullable', 'array'],
                'favorite_pairs' => ['nullable', 'array'],
            ];
        }

        // Analyst-specific fields
        if ($request->role === 'analyst') {
            $validationRules += [
                'years_of_experience' => ['nullable', 'integer', 'min:0', 'max:50'],
                'analysis_specialization' => ['nullable', 'string', 'max:100'],
                'psychology_focus_areas' => ['nullable', 'array'],
                'feedback_style' => ['nullable', 'string', 'max:100'],
                'max_traders_assigned' => ['nullable', 'integer', 'min:1', 'max:20'],
            ];
        }

        $request->validate($validationRules);


        // Prepare user data with common fields
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Auto-verify email
            'verification_status' => 'pending', // Requires admin approval
            'is_active' => true, // Active but unverified
            // Common profile fields
            'username' => $request->username,
            'country' => $request->country,
            'timezone' => $request->timezone,
            'profile_visibility' => 'public', // Default visibility
        ];

        // Add trader-specific fields
        if ($request->role === 'trader') {
            $userData += [
                'experience_level' => $request->experience_level,
                'trading_style' => $request->trading_style,
                'specialization' => $request->specialization,
                'preferred_sessions' => $request->preferred_sessions,
                'favorite_pairs' => $request->favorite_pairs,
            ];
        }

        // Add analyst-specific fields
        if ($request->role === 'analyst') {
            $userData += [
                'years_of_experience' => $request->years_of_experience,
                'analysis_specialization' => $request->analysis_specialization,
                'psychology_focus_areas' => $request->psychology_focus_areas,
                'feedback_style' => $request->feedback_style,
                'max_traders_assigned' => $request->max_traders_assigned ?? 5,
            ];
        }

        $user = User::create($userData);

        // Assign selected role
        $user->assignRole($request->role);

        event(new Registered($user));

        Auth::login($user);

        // Check verification status - redirect pending users to verification page
        if ($user->verification_status === 'pending') {
            return redirect()
                ->route('verification.pending')
                ->with('info', 'Your account has been created and is pending admin approval.');
        }

        // Redirect verified users to role-specific dashboard
        if ($user->hasRole('analyst')) {
            return redirect()->route('analyst.dashboard');
        } else {
            return redirect()->route('trader.dashboard');
        }
    }
}
