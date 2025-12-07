@extends('layouts.app')

@section('title', 'User Details')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">User Details</h1>
            <p class="text-slate-400">{{ $user->name }}'s profile and activity</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                ✏️ Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
                ← Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Info Card -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                <!-- Avatar -->
                <div class="flex justify-center mb-6">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-4xl font-bold">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                </div>

                <!-- User Info -->
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-white mb-2">{{ $user->name }}</h2>
                    <p class="text-slate-400 mb-4">{{ $user->email }}</p>
                    
                    <!-- Role Badge -->
                    <span class="inline-block px-4 py-2 text-sm font-medium rounded-full
                        {{ $user->roles->first()?->name === 'admin' ? 'bg-red-500/20 text-red-400' : '' }}
                        {{ $user->roles->first()?->name === 'analyst' ? 'bg-purple-500/20 text-purple-400' : '' }}
                        {{ $user->roles->first()?->name === 'trader' ? 'bg-emerald-500/20 text-emerald-400' : '' }}">
                        {{ ucfirst($user->roles->first()?->name ?? 'No Role') }}
                    </span>
                </div>

                <!-- Details -->
                <div class="space-y-3 border-t border-slate-700 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-400">User ID:</span>
                        <span class="text-sm font-medium text-white">{{ $user->id }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-400">Verification:</span>
                        <span class="text-sm px-2 py-1 rounded-full
                            {{ $user->verification_status === 'verified' ? 'bg-green-500/20 text-green-400' : '' }}
                            {{ $user->verification_status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                            {{ $user->verification_status === 'rejected' ? 'bg-red-500/20 text-red-400' : '' }}">
                            {{ ucfirst($user->verification_status ?? 'pending') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-400">Status:</span>
                        <span class="text-sm px-2 py-1 rounded-full {{ $user->is_active ? 'bg-green-500/20 text-green-400' : 'bg-slate-500/20 text-slate-400' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-400">Joined:</span>
                        <span class="text-sm font-medium text-white">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    @if($user->last_login_at)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-400">Last Login:</span>
                            <span class="text-sm font-medium text-white">{{ $user->last_login_at->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 space-y-2">
                    @if($user->verification_status === 'pending')
                        <form action="{{ route('admin.verifications.approve', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                ✓ Approve Verification
                            </button>
                        </form>
                    @endif
                    
                    @if($user->is_active)
                        <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST" onsubmit="return confirm('Deactivate this user?')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors">
                                Deactivate User
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.reactivate', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                Reactivate User
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" onsubmit="return confirm('Send password reset email?')">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Activity & Stats -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Role-Specific Info -->
            @if($user->hasRole('trader'))
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                    <h3 class="text-xl font-bold text-white mb-4">Trader Stats</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-emerald-400">{{ $user->trades->count() }}</p>
                            <p class="text-sm text-slate-400 mt-1">Total Trades</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-purple-400">{{ $user->feedbackReceived->count() }}</p>
                            <p class="text-sm text-slate-400 mt-1">Feedback Received</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-blue-400">
                                @if($user->analystAssignments->first())
                                    1
                                @else
                                    0
                                @endif
                            </p>
                            <p class="text-sm text-slate-400 mt-1">Assigned Analyst</p>
                        </div>
                    </div>
                    @if($user->analystAssignments->first())
                        <div class="mt-4 p-4 bg-white/5 rounded-lg">
                            <p class="text-sm text-slate-400 mb-2">Assigned Analyst:</p>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ substr($user->analystAssignments->first()->analyst->name, 0, 1) }}
                                </div>
                                <p class="font-medium text-white">{{ $user->analystAssignments->first()->analyst->name }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @elseif($user->hasRole('analyst'))
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                    <h3 class="text-xl font-bold text-white mb-4">Analyst Stats</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-purple-400">{{ $user->tradersAssigned->count() }}</p>
                            <p class="text-sm text-slate-400 mt-1">Assigned Traders</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-emerald-400">{{ $user->feedbackGiven->count() }}</p>
                            <p class="text-sm text-slate-400 mt-1">Feedback Given</p>
                        </div>
                    </div>
                    @if($user->tradersAssigned->count() > 0)
                        <div class="mt-4 p-4 bg-white/5 rounded-lg">
                            <p class="text-sm text-slate-400 mb-2">Assigned Traders:</p>
                            <div class="space-y-2">
                                @foreach($user->tradersAssigned as $assignment)
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                            {{ substr($assignment->trader->name, 0, 1) }}
                                        </div>
                                        <p class="text-white">{{ $assignment->trader->name }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Recent Activity -->
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                <h3 class="text-xl font-bold text-white mb-4">Recent Trades</h3>
                @if($user->hasRole('trader') && $user->trades->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->trades->take(5) as $trade)
                            <div class="p-3 bg-white/5 rounded-lg flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-white">{{ $trade->pair }}</p>
                                    <p class="text-xs text-slate-400">{{ $trade->entry_date->format('M d, Y') }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $trade->outcome->value === 'win' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                    {{ $trade->outcome->label() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center py-8 text-slate-400">No trades yet</p>
                @endif
            </div>
        </div>
    </div>
@endsection
