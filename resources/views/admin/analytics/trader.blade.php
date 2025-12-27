@extends('layouts.app')

@section('title', 'Trader Analytics: ' . $trader->name)

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center text-xl font-bold">
                {{ substr($trader->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $trader->name }}</h1>
                <p class="text-slate-400">Individual Performance Report</p>
            </div>
        </div>
        <a href="{{ route('admin.analytics.index') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
            ← Back to Analytics
        </a>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @include('components.stat-card', [
            'icon' => '📊',
            'value' => number_format($totalTrades),
            'label' => 'Total Trades',
            'accentColor' => 'blue'
        ])

        @include('components.stat-card', [
            'icon' => '🎯',
            'value' => number_format($winRate, 1) . '%',
            'label' => 'Win Rate',
            'accentColor' => $winRate >= 50 ? 'green' : 'red'
        ])

        @include('components.stat-card', [
            'icon' => '💰',
            'value' => ($totalPL >= 0 ? '+' : '') . '$' . number_format($totalPL, 2),
            'label' => 'Net Profit/Loss',
            'accentColor' => $totalPL >= 0 ? 'emerald' : 'red'
        ])

        @include('components.stat-card', [
            'icon' => '📈',
            'value' => number_format($profitFactor, 2),
            'label' => 'Profit Factor',
            'accentColor' => $profitFactor >= 1.5 ? 'purple' : 'yellow'
        ])
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Session Performance -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h2 class="text-xl font-semibold text-white mb-6">Session Performance</h2>
            <div class="grid grid-cols-2 gap-4">
                @foreach($sessionStats as $session)
                    <div class="p-4 bg-white/5 rounded-lg text-center">
                        <p class="text-2xl mb-2">
                            @php $sessionValue = $session->session instanceof \App\Enums\MarketSession ? $session->session->value : $session->session; @endphp
                            @if($sessionValue === 'london') 🇬🇧
                            @elseif($sessionValue === 'new_york') 🇺🇸
                            @elseif($sessionValue === 'tokyo') 🇯🇵
                            @else 🌏
                            @endif
                        </p>
                        <p class="text-slate-400 text-sm mb-1">{{ ucfirst(str_replace('_', ' ', $sessionValue)) }}</p>
                        <div class="flex justify-center gap-2 text-sm">
                            <span class="font-bold text-white">{{ $session->count }} trades</span>
                            <span class="{{ $session->pl >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $session->pl >= 0 ? '+' : '' }}${{ number_format($session->pl, 0) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Pairs -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h2 class="text-xl font-semibold text-white mb-6">Most Traded Pairs</h2>
            <div class="space-y-3">
                @foreach($topPairs as $pair)
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-500/20 rounded-full flex items-center justify-center text-indigo-400 font-bold">
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

    <!-- Recent Trades Table -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
        <h2 class="text-xl font-semibold text-white mb-6">Recent Trades</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-700 text-left text-slate-400 text-sm">
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Pair</th>
                        <th class="py-3 px-4 text-center">Outcome</th>
                        <th class="py-3 px-4 text-right">P/L</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach($trader->trades->sortByDesc('entry_date')->take(10) as $trade)
                        <tr class="border-b border-slate-800 hover:bg-white/5">
                            <td class="py-3 px-4 text-slate-300">{{ $trade->entry_date->format('M d, H:i') }}</td>
                            <td class="py-3 px-4 text-white font-medium">{{ $trade->pair }} <span class="text-xs text-slate-500 ml-1">{{ strtoupper($trade->direction->value ?? $trade->direction) }}</span></td>
                            <td class="py-3 px-4 text-center">
                                @php 
                                    $outcome = $trade->outcome instanceof \App\Enums\TradeOutcome ? $trade->outcome->value : $trade->outcome;
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-bold 
                                    {{ $outcome === 'win' ? 'bg-green-500/20 text-green-400' : 
                                      ($outcome === 'loss' ? 'bg-red-500/20 text-red-400' : 'bg-slate-500/20 text-slate-400') }}">
                                    {{ strtoupper($outcome) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-bold {{ $trade->profit_loss >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $trade->profit_loss >= 0 ? '+' : '' }}${{ number_format($trade->profit_loss, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
