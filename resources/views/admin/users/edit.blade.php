@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Edit User</h1>
            <p class="text-slate-400">Update {{ $user->name }}'s information</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.show', $user->id) }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
                Cancel
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                @csrf
                @method('PUT')

                <!-- Basic Info -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Basic Information</h3>
                    
                    <div class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('name')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('email')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status & Verification -->
                <div class="mb-6 border-t border-slate-700 pt-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Status & Verification</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Verification Status -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Verification Status</label>
                            <select name="verification_status" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="pending" {{ $user->verification_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="verified" {{ $user->verification_status === 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="rejected" {{ $user->verification_status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>

                        <!-- Active Status -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Account Status</label>
                            <select name="is_active" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        💾 Save Changes
                    </button>
                    <a href="{{ route('admin.users.show', $user->id) }}" class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </form>

            <!-- Danger Zone -->
            <div class="mt-6 bg-gradient-to-br from-red-900/20 to-pink-900/20 backdrop-blur-xl rounded-xl p-6 border border-red-700/50">
                <h3 class="text-lg font-semibold text-red-400 mb-4">⚠️ Danger Zone</h3>
                
                <div class="space-y-3">
                    <!-- Delete User -->
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                        <div>
                            <p class="text-white font-medium">Delete User</p>
                            <p class="text-sm text-slate-400">Permanently delete this user account</p>
                        </div>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure? This action cannot be undone!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Role Card -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                <h3 class="text-lg font-semibold text-white mb-4">Change Role</h3>
                
                <form action="{{ route('admin.users.change-role', $user->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Current Role</label>
                        <div class="p-3 bg-white/5 rounded-lg">
                            <span class="px-3 py-1 text-sm font-medium rounded-full
                                {{ $user->roles->first()?->name === 'admin' ? 'bg-red-500/20 text-red-400' : '' }}
                                {{ $user->roles->first()?->name === 'analyst' ? 'bg-purple-500/20 text-purple-400' : '' }}
                                {{ $user->roles->first()?->name === 'trader' ? 'bg-emerald-500/20 text-emerald-400' : '' }}">
                                {{ ucfirst($user->roles->first()?->name ?? 'No Role') }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-300 mb-2">New Role</label>
                        <select name="role" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->roles->first()?->name === $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors"
                            onclick="return confirm('Change this user\'s role?')">
                        Change Role
                    </button>
                </form>

                <div class="mt-6 p-4 bg-blue-900/20 border border-blue-700/50 rounded-lg">
                    <p class="text-xs text-blue-400 font-medium mb-2">ℹ️ Role Information:</p>
                    <ul class="text-xs text-slate-300 space-y-1">
                        <li>• <strong>Admin:</strong> Full system access</li>
                        <li>• <strong>Analyst:</strong> Review trader performance</li>
                        <li>• <strong>Trader:</strong> Manage personal trades</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
