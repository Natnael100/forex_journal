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
            // Create the user account
            // Note: We generate a random password. The user will reset it via the link sent in email.
            // Alternatively, we could just send them a temporary password.
            // Using Password Broker to generate a reset token is a clean way.
            
            // Check if user already exists with this email (should be caught by validation, but safety first)
            if (User::where('email', $application->email)->exists()) {
                throw new \Exception('A user with this email already exists.');
            }

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
                
                // Set these from application if they map directly, or user can fill them later
                'specializations' => $application->specializations,
                'certifications' => $application->certifications,
            ]);

            $user->assignRole('analyst');

            // Update application status
            $application->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // Send approval email with password reset link
            // We'll generate a token manually to send in the email
            $token = Password::createToken($user);
            
            try {
                Mail::to($user->email)->send(new AnalystApprovedMail($user, $token));
            } catch (\Exception $e) {
                \Log::error('Failed to send approval email: ' . $e->getMessage());
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
