<?php

namespace App\Http\Controllers;

use App\Models\AnalystApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationReceivedMail;

class AnalystApplicationController extends Controller
{
    /**
     * Show the application form
     */
    public function create()
    {
        $user = auth()->user();
        // If user is logged in, we can pass their data to pre-fill
        // The view handles request('name') but let's be explicit or let the view handle auth() check if desired.
        // Actually, the view uses old('name', request('name')). 
        // Let's rely on view accessing auth() or passing it. 
        // Better:
        if ($user) {
            request()->merge([
                'name' => $user->name,
                'email' => $user->email,
                'country' => $user->country,
                'timezone' => $user->timezone,
            ]);
        }
        
        return view('analyst-application.create');
    }

    /**
     * Store the application
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Personal Information
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:analyst_applications,email',
                // Only check unique in users if NOT logged in OR if logged in user has different email
                function ($attribute, $value, $fail) {
                    $user = auth()->user();
                    if (!$user || $user->email !== $value) {
                        // Check if email exists for OTHER users
                        if (\App\Models\User::where('email', $value)->exists()) {
                            $fail('The email has already been taken.');
                        }
                    }
                },
            ],
            'country' => 'nullable|string|max:100',
            'timezone' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            
            // Professional Credentials
            'years_experience' => 'required|string',
            'certifications' => 'nullable|array',
            'certificate_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'methodology' => 'nullable|array',
            'specializations' => 'nullable|array',
            
            // Coaching Experience
            'coaching_experience' => 'required|string',
            'clients_coached' => 'required|string',
            'coaching_style' => 'nullable|string',
            
            // Social Proof
            'track_record_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_handle' => 'nullable|string|max:100',
            'youtube_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            
            // Application Statement
            'why_join' => 'required|string|min:200|max:500',
            'unique_value' => 'required|string|min:200|max:500',
            
            // Service Details
            'max_clients' => 'required|string',
            'communication_methods' => 'nullable|array',
        ]);

        // Handle file uploads
        if ($request->hasFile('certificate_files')) {
            $files = [];
            foreach ($request->file('certificate_files') as $file) {
                $path = $file->store('analyst-certificates', 'public');
                $files[] = $path;
            }
            $validated['certificate_files'] = $files;
        }

        // Create application
        $application = AnalystApplication::create($validated);

        // Link to user if logged in (or find by email)
        $user = auth()->user() ?? \App\Models\User::where('email', $application->email)->first();
        
        if ($user) {
            $user->application_id = $application->id;
            $user->analyst_verification_status = 'pending';
            $user->save();
        }

        // Send confirmation email
        try {
            Mail::to($application->email)->send(new ApplicationReceivedMail($application));
        } catch (\Exception $e) {
            // Log error but don't fail the application
            \Log::error('Failed to send application confirmation email: ' . $e->getMessage());
        }

        return redirect()->route('analyst-application.success')
            ->with('application_email', $application->email);
    }

    /**
     * Show success page
     */
    public function success()
    {
        if (!session('application_email')) {
            return redirect()->route('analyst-application.create');
        }
        
        return view('analyst-application.success');
    }
}
