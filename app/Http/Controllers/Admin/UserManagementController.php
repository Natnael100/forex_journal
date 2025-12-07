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
     * Delete user (soft delete)
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

        $user->delete();

        activity()
            ->performedOn($user)
            ->log('User deleted');

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }
}
