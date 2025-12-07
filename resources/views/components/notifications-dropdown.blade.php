{{-- Notification Dropdown Component --}}
<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <!-- Notification Bell Button -->
    <button @click="open = !open" class="relative p-2 text-slate-400 hover:text-white transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Unread Badge -->
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-slate-800 rounded-lg shadow-xl border border-slate-700 z-50"
         style="display: none;">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-white">Notifications</h3>
            @if($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-blue-400 hover:text-blue-300">Mark all as read</button>
                </form>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <a href="{{ $notification->data['url'] ?? '#' }}" 
                   class="block px-4 py-3 hover:bg-slate-700/50 transition-colors border-b border-slate-700/50 {{ $notification->isUnread() ? 'bg-blue-900/10' : '' }}"
                   onclick="markAsRead({{ $notification->id }})">
                    <div class="flex items-start gap-3">
                        <!-- Icon based on type -->
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                            {{ $notification->type === 'feedback' ? 'bg-purple-500/20 text-purple-400' : '' }}
                            {{ $notification->type === 'assignment' ? 'bg-blue-500/20 text-blue-400' : '' }}
                            {{ $notification->type === 'verification' ? 'bg-green-500/20 text-green-400' : '' }}">
                            @if($notification->type === 'feedback')
                                💬
                            @elseif($notification->type === 'assignment')
                                🔗
                            @else
                                ✅
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-white {{ $notification->isUnread() ? 'font-medium' : '' }}">
                                {{ $notification->data['title'] ?? 'New notification' }}
                            </p>
                            <p class="text-xs text-slate-400 mt-1 line-clamp-2">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <!-- Unread Indicator -->
                        @if($notification->isUnread())
                            <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="px-4 py-8 text-center">
                    <div class="text-4xl mb-2">🔔</div>
                    <p class="text-sm text-slate-400">No notifications yet</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if($notifications->count() > 0)
            <div class="px-4 py-3 border-t border-slate-700 text-center">
                <a href="{{ route('notifications.index') }}" class="text-xs text-blue-400 hover:text-blue-300">
                    View All Notifications
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function markAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
    }
</script>
@endpush
