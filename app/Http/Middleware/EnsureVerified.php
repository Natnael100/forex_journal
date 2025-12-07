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
        if (auth()->check() && 
            !auth()->user()->hasRole('admin') && 
            auth()->user()->verification_status !== 'verified') {
            
            return redirect()->route('verification.pending')
                ->with('error', 'Your account is pending verification. Please wait for admin approval.');
        }

        return $next($request);
    }
}
