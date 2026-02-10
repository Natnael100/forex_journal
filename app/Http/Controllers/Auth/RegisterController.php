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

        $request->validate($validationRules);


        // Prepare user data with common fields
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Auto-verify email
            'verification_status' => $request->role === 'trader' ? 'verified' : 'pending',
            'is_active' => true, // Active but unverified
            // Common profile fields
            'country' => $request->country,
            'timezone' => $request->timezone,
            'profile_visibility' => 'public', // Default visibility
        ];

        // Auto-generate username if not provided
        if (empty($userData['username'])) {
            $base = strtolower(str_replace(' ', '_', $request->name));
            $userData['username'] = $base . '_' . substr(md5(uniqid()), 0, 4);
        }



        $user = User::create($userData);

        // Assign selected role
        $user->assignRole($request->role);

        event(new Registered($user));

        Auth::login($user);

        // FIRST: Check if analyst needs to complete application
        if ($user->hasRole('analyst') && !$user->application_id) {
            return redirect()->route('analyst-application.create')
                ->with('info', 'Account created! Please complete your analyst application to get verified.');
        }

        // SECOND: Check verification status - redirect pending users to verification page
        if ($user->verification_status === 'pending') {
            return redirect()
                ->route('verification.pending')
                ->with('info', 'Your account has been created and is pending admin approval.');
        }

        // THIRD: Redirect verified users to role-specific dashboard
        if ($user->hasRole('analyst')) {
            return redirect()->route('analyst.dashboard');
        } else {
            return redirect()->route('trader.dashboard');
        }
    }
}
