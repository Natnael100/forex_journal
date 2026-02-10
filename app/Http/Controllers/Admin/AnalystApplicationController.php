<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalystApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\AnalystApprovedMail;
use App\Mail\ApplicationRejectedMail;
use Illuminate\Support\Facades\Password;

class AnalystApplicationController extends Controller
{
    /**
     * List all pending applications
     */
    public function index()
    {
        $applications = AnalystApplication::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.analyst-applications.index', compact('applications'));
    }

    /**
     * Show application details
     */
    public function show(AnalystApplication $application)
    {
        return view('admin.analyst-applications.show', compact('application'));
    }

    /**
     * Approve application and create user account
     */
    public function approve(AnalystApplication $application)
    {
        // Prevent double approval
        if ($application->status !== 'pending') {
            return back()->with('error', 'This application has already been processed.');
        }

        DB::transaction(function () use ($application) {
            // Check if user already exists (new flow: register first, then apply)
            $user = User::where('email', $application->email)->first();
            
            if ($user) {
                // User already exists (registered first) - just verify them
                $user->update([
                    'analyst_verification_status' => 'verified',
                    'verified_at' => now(),
                    'verified_by' => auth()->id(),
                    'application_id' => $application->id,
                    
                    // Update profile fields from application
                    'country' => $application->country ?? $user->country,
                    'timezone' => $application->timezone ?? $user->timezone,
                    'years_experience' => $application->years_experience,
                    'specializations' => $application->specializations,
                    'certifications' => $application->certifications,
                ]);
                
                // Ensure they have analyst role
                if (!$user->hasRole('analyst')) {
                    $user->assignRole('analyst');
                }
            } else {
                // Old flow: User doesn't exist yet - create account
                // Generate a base username from name
                $baseUsername = Str::slug($application->name, '_');
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . '_' . $counter++;
                }

                $user = User::create([
                    'name' => $application->name,
                    'email' => $application->email,
                    'password' => Hash::make(Str::random(32)), // Random password initially
                    'username' => $username,
                    'country' => $application->country,
                    'timezone' => $application->timezone,
                    'years_experience' => $application->years_experience,
                    
                    // Verification fields
                    'analyst_verification_status' => 'verified',
                    'verified_at' => now(),
                    'verified_by' => auth()->id(),
                    'application_id' => $application->id,
                    
                    // Set these from application
                    'specializations' => $application->specializations,
                    'certifications' => $application->certifications,
                ]);

                $user->assignRole('analyst');
            }

            // Update application status
            $application->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // Send approval email
            if (!$user->wasRecentlyCreated) {
                // User already had account - just send verification email
                try {
                    Mail::to($user->email)->send(new AnalystApprovedMail($user, null));
                } catch (\Exception $e) {
                    \Log::error('Failed to send approval email: ' . $e->getMessage());
                }
            } else {
                // New user - send password reset link
                $token = Password::createToken($user);
                try {
                    Mail::to($user->email)->send(new AnalystApprovedMail($user, $token));
                } catch (\Exception $e) {
                    \Log::error('Failed to send approval email: ' . $e->getMessage());
                }
            }
        });

        return redirect()->route('admin.analyst-applications.index')
            ->with('success', 'Analyst approved and account created successfully!');
    }

    /**
     * Reject application
     */
    public function reject(Request $request, AnalystApplication $application)
    {
        if ($application->status !== 'pending') {
            return back()->with('error', 'This application has already been processed.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $application->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        try {
            Mail::to($application->email)->send(new ApplicationRejectedMail($application));
        } catch (\Exception $e) {
            \Log::error('Failed to send rejection email: ' . $e->getMessage());
        }

        return redirect()->route('admin.analyst-applications.index')
            ->with('success', 'Application rejected.');
    }
}
