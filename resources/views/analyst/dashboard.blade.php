@extends('layouts.app')

@section('title', 'Analyst Dashboard')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Analyst Dashboard 📊</h1>
        <p class="text-slate-400">Monitor and provide feedback to assigned traders</p>
    </div>

    <!--Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @include('components.stat-card', [
            'icon' => '👥',
            'value' => $stats['total_traders'],
            'label' => 'Assigned Traders',
            'accentColor' => 'blue'
        ])

        @include('components.stat-card', [
            'icon' => '💬',
            'value' => $stats['total_feedback'],
            'label' => 'Total Feedback Given',
            'accentColor' => 'purple'
        ])

        @include('components.stat-card', [
            'icon' => '📝',
            'value' => $stats['recent_feedback_count'],
            'label' => 'Feedback This Week',
            'accentColor' => 'emerald'
        ])
    </div>

    <!-- Traders Table -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-8">
        <h2 class="text-xl font-semibold text-white mb-6">Assigned Traders</h2>
        
        @if($traders->isEmpty())
            <div class="text-center py-12">
                <p class="text-slate-400 text-lg mb-4">No traders assigned yet</p>
                <p class="text-slate-500 text-sm">Contact admin to get trader assignments</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700">
                            <th class="text-left py-3 px-4 text-slate-300 font-semibold">Trader</th>
                            <th class="text-center py-3 px-4 text-slate-300 font-semibold">Total Trades</th>
                            <th class="text-center py-3 px-4 text-slate-300 font-semibold">Win Rate</th>
                            <th class="text-center py-3 px-4 text-slate-300 font-semibold">Profit Factor</th>
                            <th class="text-center py-3 px-4 text-slate-300 font-semibold">Total P/L</th>
                            <th class="text-center py-3 px-4 text-slate-300 font-semibold">Last Trade</th>
                            <th class="text-right py-3 px-4 text-slate-300 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($traders as $trader)
                            <tr class="border-b border-slate-800 hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4">
                                    <div>
                                        <p class="font-semibold text-white">{{ $trader['name'] }}</p>
                                        <p class="text-sm text-slate-400">{{ $trader['email'] }}</p>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center text-slate-300">{{ $trader['total_trades'] }}</td>
                                <td class="py-4 px-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ $trader['win_rate'] >= 55 ? 'bg-green-500/20 text-green-400' : ($trader['win_rate'] >= 40 ? 'bg-yellow-500/20 text-yellow-400' : 'bg-red-500/20 text-red-400') }}">
                                        {{ round($trader['win_rate'], 1) }}%
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="text-slate-300">{{ round($trader['profit_factor'], 2) }}</span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="font-semibold {{ $trader['total_pl'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $trader['total_pl'] >= 0 ? '+' : '' }}${{ number_format($trader['total_pl'], 2) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center text-slate-400 text-sm">
                                    {{ $trader['last_trade'] ? $trader['last_trade']->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <a href="{{ route('analyst.trader.profile', $trader['id']) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        View Profile
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Recent Feedback -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
        <h2 class="text-xl font-semibold text-white mb-6">Recent Feedback</h2>
        
        @if($recentFeedback->isEmpty())
            <p class="text-center text-slate-400 py-8">No feedback submitted yet</p>
        @else
            <div class="space-y-4">
                @foreach($recentFeedback as $feedback)
                    <div class="p-4 bg-white/5 rounded-lg border border-slate-700">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="font-semibold text-white">{{ $feedback->trader->name }}</p>
                                @if($feedback->trade)
                                    <p class="text-sm text-slate-400">Trade: {{ $feedback->trade->pair }} - {{ $feedback->trade->entry_date->format('M d, Y') }}</p>
                                @else
                                    <p class="text-sm text-slate-400">General feedback</p>
                                @endif
                            </div>
                            <span class="text-xs text-slate-400">{{ $feedback->submitted_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-slate-300 line-clamp-2">{{ $feedback->content }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
