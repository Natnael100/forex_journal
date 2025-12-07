@extends('layouts.app')

@section('title', 'Trader Dashboard')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Welcome Back, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-slate-400">Track and improve your trading performance</p>
        </div>
        <a href="{{ route('trader.trades.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-lg shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 transition-all duration-200 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>New Trade</span>
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @include('components.stat-card', [
            'icon' => '📊',
            'value' => $stats['total_trades'],
            'label' => 'Total Trades',
            'accentColor' => 'blue'
        ])

        @include('components.stat-card', [
            'icon' => '🎯',
            'value' => $stats['win_rate'] . '%',
            'label' => 'Win Rate',
            'subtitle' => $stats['win_rate'] >= 50 ? 'Above average' : 'Room for improvement',
            'accentColor' => 'emerald'
        ])

        @include('components.stat-card', [
            'icon' => '⚖️',
            'value' => $stats['avg_rr'],
            'label' => 'Avg R:R',
            'subtitle' => 'Risk:Reward Ratio',
            'accentColor' => 'purple'
        ])

        @include('components.stat-card', [
            'icon' => '💰',
            'value' => '$' . number_format($stats['total_profit'], 2),
            'label' => 'Total P&L',
            'subtitle' => 'All-Time',
            'accentColor' => $stats['total_profit'] >= 0 ? 'green' : 'red'
        ])

        @include('components.stat-card', [
            'icon' => '🔢',
            'value' => $stats['profit_factor'],
            'label' => 'Profit Factor',
            'subtitle' => $stats['profit_factor'] >= 2 ? 'Excellent' : ($stats['profit_factor'] >= 1.5 ? 'Good' : 'Needs work'),
            'accentColor' => 'cyan'
        ])

        @include('components.stat-card', [
            'icon' => '📈',
            'value' => '$' . number_format($stats['expectancy'], 2),
            'label' => 'Expectancy',
            'subtitle' => 'Per Trade Average',
            'accentColor' => 'indigo'
        ])

        @include('components.stat-card', [
            'icon' => '📉',
            'value' => '$' . number_format($stats['max_drawdown'], 2),
            'label' => 'Max Drawdown',
            'subtitle' => 'Largest peak-to-trough',
            'accentColor' => 'orange'
        ])

        @include('components.stat-card', [
            'icon' => '📅',
            'value' => $stats['this_month_trades'],
            'label' => 'This Month',
            'subtitle' => date('F Y'),
            'accentColor' => 'pink'
        ])
    </div>

    <!-- Streaks & Quick Stats -->
    @if($stats['total_trades'] > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Current Streak -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span>🔥</span> Current Streak
            </h3>
            <div class="text-center">
                <p class="text-4xl font-bold {{ $streaks['current_type'] === 'win' ? 'text-green-400' : 'text-red-400' }} mb-2">
                    {{ $streaks['current_streak'] }}
                </p>
                <p class="text-slate-400">{{ ucfirst($streaks['current_type'] ?? 'No') }} {{ $streaks['current_streak'] === 1 ? 'trade' : 'trades' }}</p>
            </div>
        </div>

        <!-- Max Win Streak -->
        <div class="bg-gradient-to-br from-green-800/20 to-emerald-900/20 backdrop-blur-xl rounded-xl p-6 border border-green-700/30">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span>✅</span> Best Win Streak
            </h3>
            <div class="text-center">
                <p class="text-4xl font-bold text-green-400 mb-2">{{ $streaks['max_win_streak'] }}</p>
                <p class="text-slate-400">Consecutive wins</p>
            </div>
        </div>

        <!-- Max Loss Streak -->
        <div class="bg-gradient-to-br from-red-800/20 to-rose-900/20 backdrop-blur-xl rounded-xl p-6 border border-red-700/30">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span>⚠️</span> Max Loss Streak
            </h3>
            <div class="text-center">
                <p class="text-4xl font-bold text-red-400 mb-2">{{ $streaks['max_loss_streak'] }}</p>
                <p class="text-slate-400">Consecutive losses</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Trades -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50">
            <div class="p-6 border-b border-slate-700/50 flex items-center justify-between">
                <h2 class="text-xl font-bold text-white">Recent Trades</h2>
                <a href="{{ route('trader.trades.index') }}" class="text-sm text-emerald-400 hover:text-emerald-300 transition-colors">View All →</a>
            </div>
            <div class="p-6">
                @forelse($recentTrades as $trade)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-slate-700/30' : '' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">{{ $trade->direction->icon() }}</span>
                            <div>
                                <p class="font-medium text-white">{{ $trade->pair }}</p>
                                <p class="text-xs text-slate-500">{{ $trade->entry_date->format('M d, H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold {{ $trade->profit_loss >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $trade->profit_loss >= 0 ? '+' : '' }}${{ number_format($trade->profit_loss, 2) }}
                            </p>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $trade->outcome->colorClass() }}">
                                {{ $trade->outcome->label() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-slate-500 py-8">No trades yet. Start journaling!</p>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h2 class="text-xl font-bold text-white mb-4">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('trader.trades.create') }}" class="flex items-center gap-4 p-4 bg-slate-800/50 rounded-lg hover:bg-slate-700/50 transition-colors group">
                    <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-white">Log New Trade</p>
                        <p class="text-sm text-slate-400">Record your latest trade</p>
                    </div>
                </a>
                <a href="{{ route('trader.analytics.index') }}" class="flex items-center gap-4 p-4 bg-slate-800/50 rounded-lg hover:bg-slate-700/50 transition-colors group">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-white">View Analytics</p>
                        <p class="text-sm text-slate-400">Deep dive into your performance</p>
                    </div>
                </a>
                <a href="{{ route('trader.trades.index') }}" class="flex items-center gap-4 p-4 bg-slate-800/50 rounded-lg hover:bg-slate-700/50 transition-colors group">
                    <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-white">Trade History</p>
                        <p class="text-sm text-slate-400">Review all your trades</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
