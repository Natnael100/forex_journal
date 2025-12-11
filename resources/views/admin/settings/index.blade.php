@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">System Settings ⚙️</h1>
            <p class="text-slate-400">Configure platform preferences and thresholds</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
            ← Back to Dashboard
        </a>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Platform Settings -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-6">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                </svg>
                Platform Settings
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Platform Name</label>
                    <input type="text" name="platform_name" value="{{ $settings['platform_name'] }}" 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Items Per Page</label>
                    <input type="number" name="items_per_page" value="{{ $settings['items_per_page'] }}" min="5" max="100"
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-6">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                Notification Settings
            </h2>
            
            <div class="space-y-4">
                <label class="flex items-center justify-between p-4 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <div>
                            <p class="text-white font-medium">Enable Notifications</p>
                            <p class="text-xs text-slate-400">Master switch for all notifications</p>
                        </div>
                    </div>
                    <input type="hidden" name="notifications_enabled" value="0">
                    <input type="checkbox" name="notifications_enabled" value="1" {{ $settings['notifications_enabled'] ? 'checked' : '' }}
                           class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500">
                </label>

                <label class="flex items-center justify-between p-4 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-white font-medium">Email Notifications</p>
                            <p class="text-xs text-slate-400">Send email notifications to users</p>
                        </div>
                    </div>
                    <input type="hidden" name="email_notifications" value="0">
                    <input type="checkbox" name="email_notifications" value="1" {{ $settings['email_notifications'] ? 'checked' : '' }}
                           class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500">
                </label>

                <label class="flex items-center justify-between p-4 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        <div>
                            <p class="text-white font-medium">Feedback Notifications</p>
                            <p class="text-xs text-slate-400">Notify traders when they receive feedback</p>
                        </div>
                    </div>
                    <input type="hidden" name="feedback_notifications" value="0">
                    <input type="checkbox" name="feedback_notifications" value="1" {{ $settings['feedback_notifications'] ? 'checked' : '' }}
                           class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500">
                </label>

                <label class="flex items-center justify-between p-4 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <div>
                            <p class="text-white font-medium">Assignment Notifications</p>
                            <p class="text-xs text-slate-400">Notify analysts when assigned to traders</p>
                        </div>
                    </div>
                    <input type="hidden" name="assignment_notifications" value="0">
                    <input type="checkbox" name="assignment_notifications" value="1" {{ $settings['assignment_notifications'] ? 'checked' : '' }}
                           class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500">
                </label>
            </div>
        </div>

        <!-- AI Analysis Thresholds -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-6">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                AI Analysis Thresholds
            </h2>
            <p class="text-sm text-slate-400 mb-6">Configure minimum standards for performance warnings</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Minimum Win Rate (%)
                        <span class="text-xs text-slate-500">Trigger warnings below this</span>
                    </label>
                    <input type="number" name="min_win_rate" value="{{ $settings['min_win_rate'] }}" 
                           min="0" max="100" step="0.1"
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Minimum Risk:Reward Ratio
                        <span class="text-xs text-slate-500">Expected R:R minimum</span>
                    </label>
                    <input type="number" name="min_risk_reward" value="{{ $settings['min_risk_reward'] }}" 
                           min="0" step="0.1"
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Maximum Drawdown (%)
                        <span class="text-xs text-slate-500">Red alert above this</span>
                    </label>
                    <input type="number" name="max_drawdown" value="{{ $settings['max_drawdown'] }}" 
                           min="0" max="100" step="0.1"
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Minimum Trades for Analysis
                        <span class="text-xs text-slate-500">Skip analysis with fewer trades</span>
                    </label>
                    <input type="number" name="min_trades_for_analysis" value="{{ $settings['min_trades_for_analysis'] }}" 
                           min="1"
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Save Settings
            </button>
        </div>
    </form>
@endsection
