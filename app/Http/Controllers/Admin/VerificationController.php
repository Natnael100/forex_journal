<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Show all users by verification status
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $users = User::where('verification_status', $status)
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $pendingCount = User::where('verification_status', 'pending')->count();
        $verifiedCount = User::where('verification_status', 'verified')->count();
        $rejectedCount = User::where('verification_status', 'rejected')->count();

        return view('admin.verifications.index', compact('users', 'status', 'pendingCount', 'verifiedCount', 'rejectedCount'));
    }

    /**
     * Show user detail for verification review
     */
    public function show($userId)
    {
        $user = User::with('roles')->findOrFail($userId);

        return view('admin.verifications.show', compact('user'));
    }

    /**
     * Approve user verification
     */
    public function approve($userId)
    {
        $user = User::findOrFail($userId);
        
        $user->verification_status = 'verified';
        $user->save();

        // Send notification
        $this->notificationService->notifyVerificationApproved($user);

        activity()
            ->performedOn($user)
            ->log('User verified');

        return redirect()
            ->route('admin.verifications.index')
            ->with('success', "{$user->name} has been verified successfully!");
    }

    /**
     * Reject user verification
     */
    public function reject(Request $request, $userId)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $user = User::findOrFail($userId);

        $user->verification_status = 'rejected';
        $user->rejection_reason = $validated['rejection_reason'];
        $user->save();

        // Send notification
        $this->notificationService->notifyVerificationRejected($user, $validated['rejection_reason']);

        activity()
            ->performedOn($user)
            ->log('User verification rejected: ' . $validated['rejection_reason']);

        return redirect()
            ->route('admin.verifications.index')
            ->with('success', "{$user->name}'s verification has been rejected.");
    }
}
