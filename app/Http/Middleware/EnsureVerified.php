<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Illuminate\Support\Facades\Log::info('EnsureVerified Middleware Hit', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->roles->pluck('name'),
            'status' => auth()->user()->verification_status,
            'is_admin' => auth()->user()->hasRole('admin')
        ]);

        if (auth()->check() && 
            !auth()->user()->hasRole('admin') && 
            auth()->user()->verification_status !== 'verified') {
            
            \Illuminate\Support\Facades\Log::info('Redirecting to pending verification');
            
            return redirect()->route('verification.pending')
                ->with('error', 'Your account is pending verification. Please wait for admin approval.');
        }

        return $next($request);
    }
}
