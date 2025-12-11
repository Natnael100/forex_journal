@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Activity Logs 📋</h1>
            <p class="text-slate-400">Track all system actions and changes</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.activity-logs.export', request()->query()) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export CSV
            </a>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
                ← Back
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        @include('components.stat-card', [
            'icon' => '📝',
            'value' => number_format($stats['total_logs']),
            'label' => 'Total Logs',
            'accentColor' => 'blue'
        ])
        
        @include('components.stat-card', [
            'icon' => '📅',
            'value' => number_format($stats['today_logs']),
            'label' => 'Today',
            'accentColor' => 'green'
        ])
        
        @include('components.stat-card', [
            'icon' => '📊',
            'value' => number_format($stats['week_logs']),
            'label' => 'This Week',
            'accentColor' => 'purple'
        ])
        
        @include('components.stat-card', [
            'icon' => '👥',
            'value' => number_format($stats['unique_users']),
            'label' => 'Active Users',
            'accentColor' => 'yellow'
        ])
    </div>

    <!-- Filters -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-6">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search description..." 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- User Filter -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">User</label>
                    <select name="user_id" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Entity Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Entity Type</label>
                    <select name="subject_type" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        @foreach($subjectTypes as $type)
                            <option value="{{ $type['value'] }}" {{ request('subject_type') == $type['value'] ? 'selected' : '' }}>
                                {{ $type['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Event Filter -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Event</label>
                    <input type="text" name="event" value="{{ request('event') }}" 
                           placeholder="e.g. created, updated..." 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('admin.activity-logs.index') }}" class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">
                    Clear All
                </a>
                <span class="text-slate-400 text-sm ml-auto">
                    {{ $activities->total() }} logs found
                </span>
            </div>
        </form>
    </div>

    <!-- Activity Table -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50 overflow-hidden">
        @if($activities->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700 bg-slate-800/50">
                            <th class="text-left py-4 px-4 text-slate-300 font-semibold">User</th>
                            <th class="text-left py-4 px-4 text-slate-300 font-semibold">Action</th>
                            <th class="text-left py-4 px-4 text-slate-300 font-semibold">Entity</th>
                            <th class="text-left py-4 px-4 text-slate-300 font-semibold">Description</th>
                            <th class="text-right py-4 px-4 text-slate-300 font-semibold">Date/Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activities as $activity)
                            <tr class="border-b border-slate-800 hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                            {{ $activity->causer ? substr($activity->causer->name, 0, 1) : 'S' }}
                                        </div>
                                        <div>
                                            <p class="text-white font-medium">{{ $activity->causer ? $activity->causer->name : 'System' }}</p>
                                            <p class="text-xs text-slate-400">{{ $activity->causer ? $activity->causer->email : 'Automated' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ str_contains(strtolower($activity->description), 'created') ? 'bg-green-500/20 text-green-400' : '' }}
                                        {{ str_contains(strtolower($activity->description), 'updated') ? 'bg-blue-500/20 text-blue-400' : '' }}
                                        {{ str_contains(strtolower($activity->description), 'deleted') ? 'bg-red-500/20 text-red-400' : '' }}
                                        {{ !str_contains(strtolower($activity->description), 'created') && !str_contains(strtolower($activity->description), 'updated') && !str_contains(strtolower($activity->description), 'deleted') ? 'bg-purple-500/20 text-purple-400' : '' }}">
                                        {{ $activity->log_name ?? 'Action' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-slate-300">
                                    {{ $activity->subject_type ? class_basename($activity->subject_type) : 'N/A' }}
                                    @if($activity->subject_id)
                                        <span class="text-xs text-slate-500">#{{ $activity->subject_id }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-white">
                                    {{ $activity->description }}
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="text-white">{{ $activity->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-slate-400">{{ $activity->created_at->format('h:i A') }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-6 border-t border-slate-700">
                {{ $activities->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div class="text-6xl mb-4">📋</div>
                <h3 class="text-xl font-semibold text-white mb-2">No Activity Logs Found</h3>
                <p class="text-slate-400">Try adjusting your filters or check back later</p>
            </div>
        @endif
    </div>
@endsection
