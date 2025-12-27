@extends('layouts.app')

@section('title', 'System Analytics')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">System Analytics 📊</h1>
            <p class="text-slate-400">Platform-wide performance overview</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @include('components.stat-card', [
            'icon' => '📈',
            'value' => number_format($totalTrades),
            'label' => 'Total Trades',
            'accentColor' => 'blue'
        ])

        @include('components.stat-card', [
            'icon' => '🎯',
            'value' => round($winRate, 1) . '%',
            'label' => 'Overall Win Rate',
            'accentColor' => $winRate >= 50 ? 'green' : 'red'
        ])

        @include('components.stat-card', [
            'icon' => '💰',
            'value' => ($totalProfitLoss >= 0 ? '+' : '') . '$' . number_format($totalProfitLoss, 2),
            'label' => 'Total P/L',
            'accentColor' => $totalProfitLoss >= 0 ? 'emerald' : 'red'
        ])

        @include('components.stat-card', [
            'icon' => '⚖️',
            'value' => round($avgRiskReward ?? 0, 2),
            'label' => 'Avg Risk:Reward',
            'accentColor' => 'purple'
        ])
    </div>

    <!-- P/L Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h2 class="text-xl font-semibold text-white mb-6">P/L Breakdown</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-green-900/20 border border-green-700/50 rounded-lg">
                    <span class="text-slate-300">Total Profit</span>
                    <span class="text-xl font-bold text-green-400">${{ number_format($totalProfit, 2) }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-red-900/20 border border-red-700/50 rounded-lg">
                    <span class="text-slate-300">Total Loss</span>
                    <span class="text-xl font-bold text-red-400">${{ number_format(abs($totalLoss), 2) }}</span>
                </div>
                <div class="pt-4 border-t border-slate-700">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-300">Avg Win Size</span>
                        <span class="text-lg font-semibold text-green-400">${{ number_format($avgWinSize, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-slate-300">Avg Loss Size</span>
                        <span class="text-lg font-semibold text-red-400">${{ number_format(abs($avgLossSize), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Traded Pairs -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h2 class="text-xl font-semibold text-white mb-6">Top Traded Pairs</h2>
            <div class="space-y-3">
                @foreach($topPairs as $pair)
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center text-blue-400 font-bold">
                                {{ substr($pair->pair, 0, 2) }}
                            </div>
                            <span class="text-white font-medium">{{ $pair->pair }}</span>
                        </div>
                        <span class="px-3 py-1 bg-slate-700 text-slate-300 rounded-full text-sm font-medium">
                            {{ $pair->count }} trades
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Session Performance -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-8">
        <h2 class="text-xl font-semibold text-white mb-6">Session Performance</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach($sessionStats as $session)
                <div class="p-4 bg-white/5 rounded-lg text-center">
                    <p class="text-2xl mb-2">
                        @php 
                            $rawSession = $session->session;
                            $enum = null;
                            if ($rawSession instanceof \App\Enums\MarketSession) {
                                $enum = $rawSession;
                            } elseif (is_string($rawSession)) {
                                $enum = \App\Enums\MarketSession::tryFrom($rawSession);
                            }
                            
                            $sessionValue = $enum?->value ?? (is_scalar($rawSession) ? $rawSession : '');
                        @endphp
                        
                        @if($enum === \App\Enums\MarketSession::LONDON || $sessionValue === 'london') 🇬🇧
                        @elseif($enum === \App\Enums\MarketSession::NEWYORK || $sessionValue === 'newyork') 🇺🇸
                        @elseif($enum === \App\Enums\MarketSession::ASIA || $sessionValue === 'asia') 🇯🇵
                        @elseif($enum === \App\Enums\MarketSession::SYDNEY || $sessionValue === 'sydney') 🇦🇺
                        @else 🌏
                        @endif
                    </p>
                    <p class="text-slate-400 text-sm mb-1">
                        {{ $enum?->label() ?? ucfirst(str_replace('_', ' ', $sessionValue)) }}
                    </p>
                    <p class="text-2xl font-bold text-white">{{ $session->count }}</p>
                    <p class="text-xs text-slate-500">trades</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Top Performing Traders -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-white">Top Performing Traders</h2>
            <a href="{{ route('admin.analytics.trades') }}" class="text-sm text-blue-400 hover:text-blue-300">
                View All Trades →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-700">
                        <th class="text-left py-3 px-4 text-slate-300 font-semibold">Rank</th>
                        <th class="text-left py-3 px-4 text-slate-300 font-semibold">Trader</th>
                        <th class="text-center py-3 px-4 text-slate-300 font-semibold">Trades</th>
                        <th class="text-center py-3 px-4 text-slate-300 font-semibold">Win Rate</th>
                        <th class="text-right py-3 px-4 text-slate-300 font-semibold">Total P/L</th>
                        <th class="text-right py-3 px-4 text-slate-300 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topTraders as $index => $trader)
                        <tr class="border-b border-slate-800 hover:bg-white/5 transition-colors">
                            <td class="py-4 px-4">
                                <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold
                                    {{ $index === 0 ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                    {{ $index === 1 ? 'bg-slate-400/20 text-slate-300' : '' }}
                                    {{ $index === 2 ? 'bg-orange-700/20 text-orange-400' : '' }}
                                    {{ $index > 2 ? 'text-slate-500' : '' }}">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-white font-medium">{{ $trader['name'] }}</td>
                            <td class="py-4 px-4 text-center text-slate-300">{{ $trader['trades_count'] }}</td>
                            <td class="py-4 px-4 text-center">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    {{ $trader['win_rate'] >= 55 ? 'bg-green-500/20 text-green-400' : '' }}
                                    {{ $trader['win_rate'] >= 40 && $trader['win_rate'] < 55 ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                    {{ $trader['win_rate'] < 40 ? 'bg-red-500/20 text-red-400' : '' }}">
                                    {{ round($trader['win_rate'], 1) }}%
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <span class="text-lg font-bold {{ $trader['total_pl'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $trader['total_pl'] >= 0 ? '+' : '' }}${{ number_format($trader['total_pl'], 2) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <a href="{{ route('admin.analytics.trader', $trader['id']) }}" 
                                   class="text-sm text-blue-400 hover:text-blue-300">
                                    View Details →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
