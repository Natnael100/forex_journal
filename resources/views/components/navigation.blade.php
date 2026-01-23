<nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
    @php
        $user = auth()->user();
        $isAdmin = $user && $user->hasRole('admin');
        $isTrader = $user && $user->hasRole('trader');
        $isAnalyst = $user && $user->hasRole('analyst');
    @endphp

    {{-- Admin Navigation --}}
    @if($isAdmin)
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-blue-500/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span class="font-medium">User Management</span>
        </a>

        <a href="{{ route('admin.analyst-applications.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.analyst-applications.*') ? 'bg-purple-500/20 text-purple-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="font-medium">Analyst Applications</span>
        </a>

        <a href="{{ route('admin.subscriptions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.subscriptions.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="font-medium">Subscriptions</span>
        </a>

        <a href="{{ route('admin.disputes.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.disputes.*') ? 'bg-red-500/20 text-red-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span class="font-medium">Disputes</span>
        </a>

        <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.analytics.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span class="font-medium">Analytics</span>
        </a>

        <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.activity-logs.*') ? 'bg-orange-500/20 text-orange-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="font-medium">Activity Logs</span>
        </a>

        <a href="{{ route('admin.backups.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.backups.*') ? 'bg-cyan-500/20 text-cyan-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
            </svg>
            <span class="font-medium">Backups</span>
        </a>

        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-pink-500/20 text-pink-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="font-medium">Settings</span>
        </a>

        <a href="{{ route('profile.show', $user->username ?? $user->id) }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('profile.show') ? 'bg-indigo-500/20 text-indigo-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="font-medium">My Profile</span>
        </a>

        <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('notifications.*') ? 'bg-yellow-500/20 text-yellow-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200 relative">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="font-medium">Notifications</span>
            @php
                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
            @endphp
            @if($unreadCount > 0)
                <span class="notification-badge-count ml-auto min-w-[20px] h-5 px-1.5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-sm" style="background-color: #ef4444; color: white;">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </a>
    @endif

    {{-- Trader Navigation --}}
    @if($isTrader)
        <a href="{{ route('trader.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('trader.dashboard') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <a href="{{ route('trader.subscriptions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('trader.subscriptions.*') ? 'bg-blue-500/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="font-medium">My Subscriptions</span>
        </a>

        <a href="{{ route('trader.disputes.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('trader.disputes.*') ? 'bg-red-500/20 text-red-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span class="font-medium">My Disputes</span>
        </a>



        <a href="{{ route('trader.accounts.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('trader.accounts.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span class="font-medium">Accounts</span>
        </a>

        <a href="{{ route('trader.strategies.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('trader.strategies.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <span class="font-medium">Playbook</span>
        </a>

        <a href="{{ route('trader.tools.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('trader.tools.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="font-medium">Tools</span>
        </a>

        <a href="{{ route('trader.trades.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('trader.trades.create') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span class="font-medium">New Trade</span>
        </a>

        <a href="{{ route('trader.trades.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('trader.trades.index') || request()->routeIs('trader.trades.show') || request()->routeIs('trader.trades.edit') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="font-medium">Trade History</span>
        </a>

        <a href="{{ route('trader.analytics.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('trader.analytics.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span class="font-medium">Analytics</span>
        </a>

        <a href="{{ route('trader.feedback.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('trader.feedback.*') ? 'bg-purple-500/20 text-purple-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
            </svg>
            <span class="font-medium">Feedback</span>
        </a>

        {{-- Phase 2: Messaging --}}
        <a href="{{ route('conversations.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('conversations.*') ? 'bg-sky-500/20 text-sky-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
            <span class="font-medium">Messages</span>
            @php
                $messageUnreadCount = \App\Models\Conversation::where('analyst_id', auth()->id())
                    ->orWhere('trader_id', auth()->id())
                    ->get()
                    ->sum(function($conversation) {
                        return $conversation->messages()->where('sender_id', '!=', auth()->id())->where('is_read', false)->count();
                    });
            @endphp
            @if($messageUnreadCount > 0)
                <span class="ml-auto min-w-[20px] h-5 px-1.5 bg-blue-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-sm">
                    {{ $messageUnreadCount > 9 ? '9+' : $messageUnreadCount }}
                </span>
            @endif
        </a>

        <a href="{{ route('trader.achievements.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('trader.achievements.*') ? 'bg-amber-500/20 text-amber-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
            </svg>
            <span class="font-medium">Achievements</span>
        </a>

        <a href="{{ route('trader.leaderboard.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('trader.leaderboard.*') ? 'bg-yellow-500/20 text-yellow-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <span class="font-medium">Leaderboard</span>
        </a>

        {{-- Phase 1: Browse Analysts --}}
        <a href="{{ route('analysts.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('analysts.*') || request()->routeIs('subscription.*') ? 'bg-indigo-500/20 text-indigo-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span class="font-medium">Find Analyst</span>
        </a>

        <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('notifications.*') ? 'bg-yellow-500/20 text-yellow-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="font-medium">Notifications</span>
            @php
                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
            @endphp
            @if($unreadCount > 0)
                <span class="notification-badge-count ml-auto min-w-[20px] h-5 px-1.5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-sm" style="background-color: #ef4444; color: white;">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </a>

        <a href="{{ route('profile.show', $user->username ?? $user->id) }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('profile.show') ? 'bg-blue-500/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="font-medium">My Profile</span>
        </a>
    @endif

    {{-- Analyst Navigation --}}
    @if($isAnalyst)
        <a href="{{ route('analyst.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('analyst.dashboard') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        {{-- Phase 1: Revenue Dashboard --}}
        <a href="{{ route('analyst.revenue') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('analyst.revenue') ? 'bg-green-500/20 text-green-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">Revenue</span>
        </a>

        {{-- Phase 2: Messaging --}}
        <a href="{{ route('conversations.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('conversations.*') ? 'bg-sky-500/20 text-sky-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
            <span class="font-medium">Messages</span>
             @php
                $messageUnreadCount = \App\Models\Conversation::where('analyst_id', auth()->id())
                    ->orWhere('trader_id', auth()->id())
                    ->get()
                    ->sum(function($conversation) {
                        return $conversation->messages()->where('sender_id', '!=', auth()->id())->where('is_read', false)->count();
                    });
            @endphp
            @if($messageUnreadCount > 0)
                <span class="ml-auto min-w-[20px] h-5 px-1.5 bg-blue-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-sm">
                    {{ $messageUnreadCount > 9 ? '9+' : $messageUnreadCount }}
                </span>
            @endif
        </a>



        <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('notifications.*') ? 'bg-blue-500/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="font-medium">Notifications</span>
            @php
                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
            @endphp
            @if($unreadCount > 0)
                <span class="notification-badge-count ml-auto min-w-[20px] h-5 px-1.5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-sm" style="background-color: #ef4444; color: white;">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </a>

        <a href="{{ route('profile.show', $user->username ?? $user->id) }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('profile.show') ? 'bg-purple-500/20 text-purple-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }} transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="font-medium">My Profile</span>
        </a>
    @endif
</nav>
