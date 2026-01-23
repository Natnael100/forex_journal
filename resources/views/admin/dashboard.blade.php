@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Admin Dashboard</h1>
        <p class="text-slate-400">Complete system overview and management</p>
    </div>

    <!-- Stats Grid - Governance Focused -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        @include('components.stat-card', [
            'icon' => '👥',
            'value' => $stats['total_users'],
            'label' => 'Total Users',
            'accentColor' => 'blue'
        ])

        <!-- Analyst Applications -->
        @include('components.stat-card', [
            'icon' => '📝',
            'value' => $stats['pending_analyst_applications'] ?? 0,
            'label' => 'Pending Applications',
            'accentColor' => ($stats['pending_analyst_applications'] ?? 0) > 0 ? 'yellow' : 'green',
            'linkRoute' => 'admin.analyst-applications.index'
        ])

        <!-- Verified Analysts -->
        @include('components.stat-card', [
            'icon' => '✅',
            'value' => $stats['verified_analysts'] ?? 0,
            'label' => 'Verified Analysts',
            'accentColor' => 'purple'
        ])

        <!-- Active Subscriptions -->
        @include('components.stat-card', [
            'icon' => '💼',
            'value' => $stats['active_subscriptions'] ?? 0,
            'label' => 'Active Subscriptions',
            'accentColor' => 'emerald'
        ])

        <!-- Total Trades -->
        @include('components.stat-card', [
            'icon' => '📈',
            'value' => $stats['total_trades'],
            'label' => 'Total Trades',
            'accentColor' => 'cyan'
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
            'accentColor' => 'indigo'
        ])

        <!-- Banned Users -->
        @include('components.stat-card', [
            'icon' => '🚫',
            'value' => $stats['banned_users'] ?? 0,
            'label' => 'Banned Users',
            'accentColor' => $stats['banned_users'] > 0 ? 'red' : 'slate'
        ])
    </div>

    <!-- Quick Actions & Alerts -->
    @if(($stats['pending_analyst_applications'] ?? 0) > 0)
        <div class="mb-8">
            <div class="bg-gradient-to-br from-purple-900/20 to-indigo-900/20 backdrop-blur-xl rounded-xl p-6 border border-purple-700/50">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-purple-400 mb-1">📝 Analyst Applications</h3>
                        <p class="text-sm text-slate-400">{{ $stats['pending_analyst_applications'] }} application(s) waiting for review</p>
                    </div>
                </div>
                <a href="{{ route('admin.analyst-applications.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Review Applications
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Pending Analyst Applications Panel -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50">
            <div class="p-6 border-b border-slate-700/50 flex items-center justify-between">
                <h2 class="text-xl font-bold text-white">Pending Analyst Applications</h2>
                <a href="{{ route('admin.analyst-applications.index') }}" class="text-sm text-blue-400 hover:text-blue-300">View All →</a>
            </div>
            <div class="p-6">
                @forelse($pendingApplications as $application)
                    <div class="mb-4 last:mb-0 p-4 bg-white/5 rounded-lg border border-slate-700 hover:border-purple-500/30 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="font-semibold text-white">{{ $application->name }}</p>
                                <p class="text-sm text-slate-400">{{ $application->email }}</p>
                                <p class="text-xs text-slate-500 mt-1">Applied {{ $application->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-500/20 text-yellow-400">
                                Pending
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.analyst-applications.show', $application->id) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition-colors">
                                Review
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-slate-400">✅ No pending applications</p>
                        <p class="text-sm text-slate-500 mt-1">All applications have been processed</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- System Health Panel -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50">
            <div class="p-6 border-b border-slate-700/50">
                <h2 class="text-xl font-bold text-white">System Health</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-500/20 rounded-full flex items-center justify-center">
                                <span class="text-green-400">✓</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">Active Subscriptions</p>
                                <p class="text-xs text-slate-400">Marketplace operational</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-green-400">{{ $stats['active_subscriptions'] ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-500/20 rounded-full flex items-center justify-center">
                                <span class="text-purple-400">✓</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">Verified Analysts</p>
                                <p class="text-xs text-slate-400">Providing coaching</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-purple-400">{{ $stats['verified_analysts'] ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-{{ $stats['banned_users'] > 0 ? 'red' : 'slate' }}-500/20 rounded-full flex items-center justify-center">
                                <span class="text-{{ $stats['banned_users'] > 0 ? 'red' : 'slate' }}-400">{{ $stats['banned_users'] > 0 ? '!' : '✓' }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">Banned Users</p>
                                <p class="text-xs text-slate-400">Moderation status</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-{{ $stats['banned_users'] > 0 ? 'red' : 'slate' }}-400">{{ $stats['banned_users'] ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center">
                                <span class="text-blue-400">📈</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">New Users (7 days)</p>
                                <p class="text-xs text-slate-400">Growth rate</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-blue-400">{{ $stats['new_users_this_week'] ?? 0 }}</span>
                    </div>
                </div>
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
