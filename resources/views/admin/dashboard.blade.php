@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Admin Dashboard</h1>
        <p class="text-slate-400">Complete system overview and management</p>
    </div>

    <!-- Stats Grid - Expanded for Phase 6/7 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        @include('components.stat-card', [
            'icon' => '👥',
            'value' => $stats['total_users'],
            'label' => 'Total Users',
            'accentColor' => 'blue'
        ])

        <!-- Pending Verifications -->
        @include('components.stat-card', [
            'icon' => '⏳',
            'value' => $stats['pending_verifications'],
            'label' => 'Pending Verifications',
            'accentColor' => $stats['pending_verifications'] > 0 ? 'yellow' : 'green',
            'linkRoute' => 'admin.verifications.index'
        ])

        <!-- Assigned Traders -->
        @include('components.stat-card', [
            'icon' => '🔗',
            'value' => $stats['assigned_traders'],
            'label' => 'Assigned Traders',
            'accentColor' => 'purple'
        ])

        <!-- Unassigned Traders -->
        @include('components.stat-card', [
            'icon' => '⚠️',
            'value' => $stats['unassigned_traders'],
            'label' => 'Unassigned Traders',
            'accentColor' => $stats['unassigned_traders'] > 0 ? 'red' : 'green',
            'linkRoute' => 'admin.assignments.index'
        ])

        <!-- Total Trades -->
        @include('components.stat-card', [
            'icon' => '📈',
            'value' => $stats['total_trades'],
            'label' => 'Total Trades',
            'accentColor' => 'emerald'
        ])

        <!-- Total Feedback -->
        @include('components.stat-card', [
            'icon' => '💬',
            'value' => $stats['total_feedback'],
            'label' => 'Total Feedback',
            'accentColor' => 'indigo'
        ])

        <!-- Traders -->
        @include('components.stat-card', [
            'icon' => '📊',
            'value' => $stats['total_traders'],
            'label' => 'Traders',
            'accentColor' => 'teal'
        ])

        <!-- Analysts -->
        @include('components.stat-card', [
            'icon' => '🔍',
            'value' => $stats['total_analysts'],
            'label' => 'Analysts',
            'accentColor' => 'cyan'
        ])
    </div>

    <!-- Quick Actions & Alerts -->
    @if($stats['pending_verifications'] > 0 || $stats['unassigned_traders'] > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @if($stats['pending_verifications'] > 0)
                <div class="bg-gradient-to-br from-yellow-900/20 to-orange-900/20 backdrop-blur-xl rounded-xl p-6 border border-yellow-700/50">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-400 mb-1">⏳ Pending Verifications</h3>
                            <p class="text-sm text-slate-400">{{ $stats['pending_verifications'] }} user(s) waiting for approval</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.verifications.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Review Now
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            @endif

            @if($stats['unassigned_traders'] > 0)
                <div class="bg-gradient-to-br from-red-900/20 to-pink-900/20 backdrop-blur-xl rounded-xl p-6 border border-red-700/50">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-red-400 mb-1">⚠️ Unassigned Traders</h3>
                            <p class="text-sm text-slate-400">{{ $stats['unassigned_traders'] }} trader(s) need analyst assignment</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.assignments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Assign Now
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Pending Verifications Panel -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50">
            <div class="p-6 border-b border-slate-700/50 flex items-center justify-between">
                <h2 class="text-xl font-bold text-white">Pending Verifications</h2>
                <a href="{{ route('admin.verifications.index') }}" class="text-sm text-blue-400 hover:text-blue-300">View All →</a>
            </div>
            <div class="p-6">
                @forelse($pendingVerifications as $user)
                    <div class="mb-4 last:mb-0 p-4 bg-white/5 rounded-lg border border-slate-700 hover:border-blue-500/30 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-white">{{ $user->name }}</p>
                                    <p class="text-sm text-slate-400">{{ $user->email }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $user->roles->first()?->name === 'analyst' ? 'purple' : 'emerald' }}-500/20 text-{{ $user->roles->first()?->name === 'analyst' ? 'purple' : 'emerald' }}-400">
                                {{ $user->roles->first()?->name ?? 'User' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <form action="{{ route('admin.verifications.approve', $user->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded transition-colors">
                                    ✓ Approve
                                </button>
                            </form>
                            <button onclick="showRejectModal({{ $user->id }}, '{{ $user->name }}')" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded transition-colors">
                                ✗ Reject
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-slate-400">✅ No pending verifications</p>
                        <p class="text-sm text-slate-500 mt-1">All users are verified</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50">
            <div class="p-6 border-b border-slate-700/50">
                <h2 class="text-xl font-bold text-white">Recent Activity</h2>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                @forelse($recentActivity as $activity)
                    <div class="mb-4 last:mb-0 flex items-start gap-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="text-sm text-white">
                                <span class="font-semibold">{{ $activity->causer?->name ?? 'System' }}</span>
                                {{ $activity->description }}
                            </p>
                            <p class="text-xs text-slate-400 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-slate-400">No recent activity</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50">
        <div class="p-6 border-b border-slate-700/50 flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">Recent Users</h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-400 hover:text-blue-300">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-slate-400 border-b border-slate-700/50">
                        <th class="px-6 py-3 font-medium">Name</th>
                        <th class="px-6 py-3 font-medium">Email</th>
                        <th class="px-6 py-3 font-medium">Role</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($recentUsers as $user)
                        <tr class="text-slate-300 hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="font-medium">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-emerald-500/20 text-emerald-400">
                                    {{ $user->roles->first()?->name ?? 'None' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->email_verified_at)
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-400">Verified</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-500/20 text-yellow-400">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">No users found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reject Modal (Simple version - can be enhanced) -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4 border border-slate-700">
            <h3 class="text-xl font-bold text-white mb-4">Reject Verification</h3>
            <p class="text-slate-300 mb-4">Are you sure you want to reject <span id="rejectUserName" class="font-semibold"></span>?</p>
            
            <form id="rejectForm" method="POST">
                @csrf
                <label class="block text-sm font-medium text-slate-300 mb-2">Reason for rejection:</label>
                <textarea name="rejection_reason" rows="3" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Enter reason..." required></textarea>
                
                <div class="flex items-center gap-3 mt-4">
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        Reject User
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function showRejectModal(userId, userName) {
            document.getElementById('rejectUserName').textContent = userName;
            document.getElementById('rejectForm').action = `/admin/verifications/${userId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRejectModal();
            }
        });
    </script>
    @endpush
@endsection
