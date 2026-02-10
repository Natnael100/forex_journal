<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AnalystManagementController extends Controller
{
    /**
     * List all analysts
     */
    public function index()
    {
        $analysts = User::role('analyst')
            ->withCount('subscriptionsAsAnalyst') // Assuming relationship exists
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.analysts.index', compact('analysts'));
    }

    /**
     * Grant verification badge
     */
    public function verify(User $user)
    {
        if (!$user->hasRole('analyst')) {
            return back()->with('error', 'User is not an analyst.');
        }

        $user->update([
            'analyst_verification_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return back()->with('success', 'Analyst verified successfully.');
    }

    /**
     * Revoke verification badge
     */
    public function unverify(User $user)
    {
        if (!$user->hasRole('analyst')) {
            return back()->with('error', 'User is not an analyst.');
        }

        $user->update([
            'analyst_verification_status' => 'unverified', // or null
            // Keep verified_at history or clear it? Clearing it for now.
            'verified_at' => null,
            'verified_by' => null,
        ]);

        return back()->with('success', 'Analyst verification revoked.');
    }
}
