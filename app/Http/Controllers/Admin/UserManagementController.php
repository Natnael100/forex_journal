<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Display all users with search and filter
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter by verification status
        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'active');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show user details
     */
    public function show($userId)
    {
        $user = User::with(['roles', 'trades', 'feedbackGiven', 'feedbackReceived', 'tradersAssigned', 'analystAssignments'])
            ->findOrFail($userId);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show edit form
     */
    public function edit($userId)
    {
        $user = User::with('roles')->findOrFail($userId);
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user
    */
    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'verification_status' => 'required|in:pending,verified,rejected',
            'is_active' => 'boolean',
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'User updated successfully!');
    }

    /**
     * Change user role
     */
    public function changeRole(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$validated['role']]);

        activity()
            ->performedOn($user)
            ->log('Role changed to ' . $validated['role']);

        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'User role changed successfully!');
    }

    /**
     * Deactivate user
     */
    public function deactivate($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => false]);

        activity()
            ->performedOn($user)
            ->log('User deactivated');

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deactivated successfully!');
    }

    /**
     * Reactivate user
     */
    public function reactivate($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => true]);

        activity()
            ->performedOn($user)
            ->log('User reactivated');

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User reactivated successfully!');
    }

    /**
     * Ban a user
     */
    public function ban(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // Prevent banning yourself
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'You cannot ban yourself!']);
        }

        // Prevent banning admins
        if ($user->hasRole('admin')) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Admin accounts cannot be banned!']);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $user->update([
                'banned_at' => now(),
                'ban_reason' => $validated['reason'],
            ]);

            activity()
                ->performedOn($user)
                ->log('User banned. Reason: ' . $validated['reason']);

            return redirect()
                ->route('admin.users.show', $user->id)
                ->with('success', 'User has been banned successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Ban feature not available. Run migrations first.']);
        }
    }

    /**
     * Unban a user
     */
    public function unban($userId)
    {
        $user = User::findOrFail($userId);

        try {
            $user->update([
                'banned_at' => null,
                'ban_reason' => null,
            ]);

            activity()
                ->performedOn($user)
                ->log('User unbanned');

            return redirect()
                ->route('admin.users.show', $user->id)
                ->with('success', 'User has been unbanned successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Unban feature not available. Run migrations first.']);
        }
    }

    /**
     * Send password reset email
     */
    public function resetPassword($userId)
    {
        $user = User::findOrFail($userId);

        // Generate password reset token and send email
        $token = app('auth.password.broker')->createToken($user);
        $user->sendPasswordResetNotification($token);

        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'Password reset email sent successfully!');
    }

    /**
     * Delete user permanently (traders and analysts only)
     */
    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account!');
        }

        // Prevent deleting admins
        if ($user->hasRole('admin')) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Admin accounts cannot be deleted!');
        }

        // Only allow deleting traders and analysts
        if (!$user->hasAnyRole(['trader', 'analyst'])) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'This user cannot be deleted!');
        }

        $userName = $user->name;

        // Delete related data
        try {
            // Delete analyst assignments (both as analyst and as trader)
            \DB::table('analyst_assignments')
                ->where('analyst_id', $user->id)
                ->orWhere('trader_id', $user->id)
                ->delete();

            // Delete feedback (both given and received)
            \DB::table('feedback')
                ->where('analyst_id', $user->id)
                ->orWhere('trader_id', $user->id)
                ->delete();

            // Delete notifications
            \DB::table('notifications')
                ->where('user_id', $user->id)
                ->delete();

            // Delete trades (if trader)
            if ($user->hasRole('trader')) {
                \DB::table('trades')->where('user_id', $user->id)->delete();
            }

            // Finally delete the user
            $user->delete();

            activity()
                ->performedOn($user)
                ->log("User '{$userName}' permanently deleted");

            return redirect()
                ->route('admin.users.index')
                ->with('success', "User '{$userName}' has been permanently deleted!");

        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
            
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Admin: Reset user's profile photo
     */
    public function resetProfilePhoto(User $user)
    {
        $user->update(['profile_photo' => null]);
        
        activity()
            ->performedOn($user)
            ->log('Admin reset profile photo');
        
        return back()->with('success', 'Profile photo reset successfully!');
    }

    /**
     * Admin: Reset user's cover photo
     */
    public function resetCoverPhoto(User $user)
    {
        $user->update(['cover_photo' => null]);
        
        activity()
            ->performedOn($user)
            ->log('Admin reset cover photo');
        
        return back()->with('success', 'Cover photo reset successfully!');
    }

    /**
     * Admin: Update user's username
     */
    public function updateUsername(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:20|unique:users,username,' . $user->id,
        ]);
        
        $oldUsername = $user->username;
        $user->update(['username' => $request->username]);
        
        activity()
            ->performedOn($user)
            ->log("Admin changed username from '{$oldUsername}' to '{$request->username}'");
        
        return back()->with('success', 'Username updated successfully!');
    }

    /**
     * Admin: Hide/moderate user's bio
     */
    public function moderateBio(User $user)
    {
        $user->update(['bio' => null]);
        
        activity()
            ->performedOn($user)
            ->log('Admin removed inappropriate bio content');
        
        return back()->with('success', 'Bio content removed!');
    }

    /**
     * Admin: Toggle profile verification
     */
    public function toggleVerification(User $user)
    {
        $newStatus = !$user->is_profile_verified;
        $user->update(['is_profile_verified' => $newStatus]);
        
        activity()
            ->performedOn($user)
            ->log('Admin ' . ($newStatus ? 'verified' : 'unverified') . ' profile');
        
        return back()->with('success', 'Profile verification updated!');
    }
}
