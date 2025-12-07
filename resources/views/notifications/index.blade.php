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
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 {{ $notification->isUnread() ? 'border-blue-500/30' : '' }}">
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
                            <h3 class="text-lg font-semibold text-white">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </h3>
                            @if($notification->isUnread())
                                <span class="px-2 py-1 text-xs font-medium bg-blue-500/20 text-blue-400 rounded-full">
                                    New
                                </span>
                            @endif
                        </div>
                        
                        <p class="text-slate-300 mb-3">{{ $notification->data['message'] ?? '' }}</p>
                        
                        <div class="flex items-center gap-4">
                            <p class="text-sm text-slate-400">
                                {{ $notification->created_at->format('M d, Y h:i A') }}
                                ({{ $notification->created_at->diffForHumans() }})
                            </p>
                            
                            @if(isset($notification->data['url']))
                                <a href="{{ $notification->data['url'] }}" class="text-sm text-blue-400 hover:text-blue-300">
                                    View →
                                </a>
                            @endif
                            
                            @if($notification->isUnread())
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm text-slate-400 hover:text-white">
                                        Mark as Read
                                    </button>
                                </form>
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
@endsection
