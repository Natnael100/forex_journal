@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Notifications</h1>
            <p class="text-slate-400">Stay updated with your activity</p>
        </div>
        @if($unreadCount > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="space-y-3">
        @forelse($notifications as $notification)
            <div id="notification-card-{{ $notification->id }}" 
                 onclick="toggleNotification('{{ $notification->id }}')"
                 class="group bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border transition-all duration-200 cursor-pointer hover:bg-slate-800/80
                 {{ $notification->isUnread() ? 'border-blue-500/30 shadow-[0_0_15px_-3px_rgba(59,130,246,0.1)]' : 'border-slate-700/50' }}"
                 data-unread="{{ $notification->isUnread() ? 'true' : 'false' }}">
                
                <div class="flex items-start gap-4">
                    <!-- Icon -->
                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-2xl
                        {{ $notification->type === 'feedback' ? 'bg-purple-500/20' : '' }}
                        {{ $notification->type === 'assignment' ? 'bg-blue-500/20' : '' }}
                        {{ $notification->type === 'verification' ? 'bg-green-500/20' : '' }}">
                        @if($notification->type === 'feedback')
                            💬
                        @elseif($notification->type === 'assignment')
                            🔗
                        @else
                            ✅
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-white group-hover:text-blue-400 transition-colors">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </h3>
                            
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-slate-400">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                                @if($notification->isUnread())
                                    <span id="badge-{{ $notification->id }}" class="px-2 py-1 text-xs font-medium bg-blue-500/20 text-blue-400 rounded-full animate-pulse">
                                        New
                                    </span>
                                @endif
                                <!-- Chevron -->
                                <svg id="chevron-{{ $notification->id }}" class="w-5 h-5 text-slate-500 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Collapsible Message -->
                        <div id="message-{{ $notification->id }}" class="hidden">
                            <p class="text-slate-300 mb-4 leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                            
                            @if(isset($notification->data['url']))
                                <a href="{{ $notification->data['url'] }}" 
                                   onclick="event.stopPropagation()" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium rounded-lg transition-colors">
                                    View Details →
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <div class="text-6xl mb-4">🔔</div>
                <h3 class="text-xl font-semibold text-white mb-2">No Notifications</h3>
                <p class="text-slate-400">You're all caught up!</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif

<script>
    async function toggleNotification(id) {
        const message = document.getElementById('message-' + id);
        const chevron = document.getElementById('chevron-' + id);
        const card = document.getElementById('notification-card-' + id);
        const isUnread = card.getAttribute('data-unread') === 'true';

        // Toggle Visibility
        if (message.classList.contains('hidden')) {
            message.classList.remove('hidden');
            chevron.classList.add('rotate-180');
            
            // Mark as Read Logic
            if (isUnread) {
                try {
                    // Optimistic UI Updates
                    updateUIAsRead(id);

                    // Send Request
                    const response = await fetch(`/notifications/${id}/mark-as-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                    // Revert UI if needed (optional, but keep it read for UX usually)
                }
            }
        } else {
            message.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }

    function updateUIAsRead(id) {
        const card = document.getElementById('notification-card-' + id);
        const badge = document.getElementById('badge-' + id);
        
        // Update Card Styles
        if (card.getAttribute('data-unread') === 'true') {
            card.classList.remove('border-blue-500/30', 'shadow-[0_0_15px_-3px_rgba(59,130,246,0.1)]');
            card.classList.add('border-slate-700/50');
            card.setAttribute('data-unread', 'false');
            
            // Remove "New" Badge
            if (badge) badge.remove();

            // Decrement Global Badge
            const globalBadges = document.querySelectorAll('.notification-badge-count');
            globalBadges.forEach(badge => {
                let count = parseInt(badge.textContent);
                if (count > 0) {
                    count--;
                    if (count === 0) {
                        badge.style.display = 'none'; // Or remove it
                    } else {
                        badge.textContent = count > 9 ? '9+' : count;
                    }
                }
            });
        }
    }
</script>
@endsection
