@extends('layouts.app')

@section('title', 'User Verifications')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">User Verifications</h1>
            <p class="text-slate-400">Review and approve user registrations</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Status Tabs -->
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.verifications.index', ['status' => 'pending']) }}" 
           class="px-6 py-3 rounded-lg font-medium transition-colors {{ $status === 'pending' ? 'bg-yellow-600 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}">
            ⏳ Pending ({{ $pendingCount }})
        </a>
        <a href="{{ route('admin.verifications.index', ['status' => 'verified']) }}" 
           class="px-6 py-3 rounded-lg font-medium transition-colors {{ $status === 'verified' ? 'bg-green-600 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}">
            ✓ Verified ({{ $verifiedCount }})
        </a>
        <a href="{{ route('admin.verifications.index', ['status' => 'rejected']) }}" 
           class="px-6 py-3 rounded-lg font-medium transition-colors {{ $status === 'rejected' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}">
            ✗ Rejected ({{ $rejectedCount }})
        </a>
    </div>

    <!-- Users Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($users as $user)
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 hover:border-blue-500/30 transition-all">
                <!-- User Info -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-white">{{ $user->name }}</p>
                            <p class="text-sm text-slate-400">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- User Details -->
                <div class="space-y-2 mb-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Role:</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            {{ $user->roles->first()?->name === 'analyst' ? 'bg-purple-500/20 text-purple-400' : 'bg-emerald-500/20 text-emerald-400' }}">
                            {{ $user->roles->first()?->name ?? 'User' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Registered:</span>
                        <span class="text-white">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    @if($user->verification_status === 'rejected' && $user->rejection_reason)
                        <div class="mt-3 p-3 bg-red-900/20 border border-red-700/50 rounded-lg">
                            <p class="text-xs text-red-400 font-medium mb-1">Rejection Reason:</p>
                            <p class="text-xs text-slate-300">{{ $user->rejection_reason }}</p>
                        </div>
                    @endif
                    @if($user->verification_status === 'verified')
                        <div class="mt-3 p-3 bg-green-900/20 border border-green-700/50 rounded-lg">
                            <p class="text-xs text-green-400">
                                Verified {{ $user->verified_at?->diffForHumans() }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    @if($status === 'pending')
                        <form action="{{ route('admin.verifications.approve', $user->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                ✓ Approve
                            </button>
                        </form>
                        <button onclick="showRejectModal({{ $user->id }}, '{{ $user->name }}')" 
                                class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                            ✗ Reject
                        </button>
                    @else
                        <a href="{{ route('admin.users.show', $user->id) }}" 
                           class="flex-1 text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            View Details
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-6xl mb-4">
                    @if($status === 'pending') ⏳
                    @elseif($status === 'verified') ✅
                    @else ❌
                    @endif
                </div>
                <p class="text-xl text-white mb-2">No {{ $status }} users</p>
                <p class="text-slate-400">
                    @if($status === 'pending')
                        All users have been verified or rejected
                    @elseif($status === 'verified')
                        No verified users yet
                    @else
                        No rejected users
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif

    <!-- Reject Modal -->
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

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRejectModal();
            }
        });
    </script>
    @endpush
@endsection
