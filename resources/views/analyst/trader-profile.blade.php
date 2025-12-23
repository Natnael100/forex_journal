@extends('layouts.app')

@section('title', $trader->name . ' - Profile')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('analyst.dashboard') }}" class="inline-flex items-center text-slate-400 hover:text-white mb-4 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Dashboard
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">{{ $trader->name }}'s Profile</h1>
                <p class="text-slate-400">{{ $trader->email }}</p>
            </div>
            <a href="{{ route('analyst.feedback.create', $trader->id) }}" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
                Provide Feedback
            </a>
        </div>
    </div>

    <!-- Trader Profile Card -->
    <div class="mb-8">
        <x-profile-card :user="$trader" :showBio="true" :showStats="true">
            <x-slot name="action">
                <a href="{{ route('profile.show', $trader->username ?? $trader->id) }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                    View Full Profile →
                </a>
            </x-slot>
        </x-profile-card>
    </div>

    <!-- Filters -->
    <div class="mb-8 p-4 bg-slate-800/50 rounded-xl border border-slate-700/50">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-300 mb-2">Account</label>
                <select name="trade_account_id" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ request('trade_account_id') == $account->id ? 'selected' : '' }}>
                            {{ $account->account_name }} ({{ $account->currency }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-300 mb-2">Strategy</label>
                <select name="strategy_id" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Strategies</option>
                    @foreach($strategies as $strategy)
                        <option value="{{ $strategy->id }}" {{ request('strategy_id') == $strategy->id ? 'selected' : '' }}>
                            {{ $strategy->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-300 mb-2">Period</label>
                <select name="period" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Custom</option>
                    <option value="this_week" {{ request('period') == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="last_week" {{ request('period') == 'last_week' ? 'selected' : '' }}>Last Week</option>
                    <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-lg shadow-blue-900/20">
                    Filter View
                </button>
                <a href="{{ request()->url() }}" class="ml-2 px-4 py-2 text-slate-400 hover:text-white transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @include('components.stat-card', [
            'icon' => '📊',
            'value' => number_format($metrics['total_trades']),
            'label' => 'Total Trades',
            'accentColor' => 'blue'
        ])
        
        @include('components.stat-card', [
            'icon' => '🎯',
            'value' => number_format($metrics['win_rate'], 1) . '%',
            'label' => 'Win Rate',
            'accentColor' => $metrics['win_rate'] >= 50 ? 'green' : 'red'
        ])
        
        @include('components.stat-card', [
            'icon' => '⚖️',
            'value' => number_format($metrics['avg_rr'], 2),
            'label' => 'Avg R:R',
            'accentColor' => $metrics['avg_rr'] >= 1.5 ? 'green' : 'yellow'
        ])
        
        @include('components.stat-card', [
            'icon' => '💰',
            'value' => number_format($metrics['profit_factor'], 2),
            'label' => 'Profit Factor',
            'accentColor' => $metrics['profit_factor'] >= 1.5 ? 'green' : 'red'
        ])
    </div>

    @if(isset($comparisonMetrics) && $comparisonMetrics)
    <div class="mb-8 bg-gradient-to-br from-indigo-900/20 to-blue-900/20 backdrop-blur-xl rounded-xl p-6 border border-indigo-500/30">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <span>🚀</span> Impact of Last Feedback
                <span class="text-sm font-normal text-slate-400">({{ $comparisonMetrics['feedback_date']->format('M d, Y') }})</span>
            </h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Win Rate Comparison -->
             <div class="p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                <p class="text-sm text-slate-400 mb-1">Win Rate</p>
                <div class="flex items-end gap-2">
                    <span class="text-2xl font-bold text-white">{{ $comparisonMetrics['after']['win_rate'] }}%</span>
                    @php $diff = $comparisonMetrics['after']['win_rate'] - $comparisonMetrics['before']['win_rate']; @endphp
                    <span class="text-sm font-medium {{ $diff >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 1) }}%
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Prev: {{ $comparisonMetrics['before']['win_rate'] }}%</p>
             </div>
             
             <!-- Profit Factor Comparison -->
             <div class="p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                <p class="text-sm text-slate-400 mb-1">Profit Factor</p>
                <div class="flex items-end gap-2">
                    <span class="text-2xl font-bold text-white">{{ $comparisonMetrics['after']['profit_factor'] }}</span>
                     @php $diff = $comparisonMetrics['after']['profit_factor'] - $comparisonMetrics['before']['profit_factor']; @endphp
                    <span class="text-sm font-medium {{ $diff >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 2) }}
                    </span>
                </div>
                 <p class="text-xs text-slate-500 mt-1">Prev: {{ $comparisonMetrics['before']['profit_factor'] }}</p>
             </div>
             
             <!-- Avg R:R Comparison -->
             <div class="p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                <p class="text-sm text-slate-400 mb-1">Avg R:R</p>
                <div class="flex items-end gap-2">
                    <span class="text-2xl font-bold text-white">{{ $comparisonMetrics['after']['avg_rr'] }}</span>
                     @php $diff = $comparisonMetrics['after']['avg_rr'] - $comparisonMetrics['before']['avg_rr']; @endphp
                    <span class="text-sm font-medium {{ $diff >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 2) }}
                    </span>
                </div>
                 <p class="text-xs text-slate-500 mt-1">Prev: {{ $comparisonMetrics['before']['avg_rr'] }}</p>
             </div>

             <!-- Trade Volume -->
             <div class="p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                <p class="text-sm text-slate-400 mb-1">Trades Since Feedback</p>
                <div class="flex items-end gap-2">
                    <span class="text-2xl font-bold text-white">{{ $comparisonMetrics['after']['total_trades'] }}</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Analysing new data</p>
             </div>
        </div>
    </div>
    @endif

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Equity Curve -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">Equity Curve</h3>
            <div class="relative h-80 w-full">
                <canvas id="equityChart"></canvas>
            </div>
        </div>

        <!-- Monthly P/L -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">Monthly P/L ({{ now()->year }})</h3>
            <div class="relative h-80 w-full">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Session Performance -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">Session Performance</h3>
            <div class="relative h-80 w-full">
                <canvas id="sessionChart"></canvas>
            </div>
        </div>

        <!-- Win/Loss Distribution -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">Win/Loss Distribution</h3>
            <div class="relative h-80 w-full">
                <canvas id="winLossChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Strategy Performance -->
    @if(count($strategyPerformance) > 0)
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <span>📘</span> Strategy Breakdown
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-700">
                        <th class="text-left py-3 px-4 text-slate-300 font-semibold">Strategy</th>
                        <th class="text-center py-3 px-4 text-slate-300 font-semibold">Trades</th>
                        <th class="text-center py-3 px-4 text-slate-300 font-semibold">Win Rate</th>
                        <th class="text-right py-3 px-4 text-slate-300 font-semibold">P/L</th>
                        <th class="text-right py-3 px-4 text-slate-300 font-semibold">Avg P/L</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($strategyPerformance as $strat)
                        <tr class="border-b border-slate-800 hover:bg-white/5">
                            <td class="py-3 px-4 text-white font-medium">{{ $strat['name'] }}</td>
                            <td class="py-3 px-4 text-center text-slate-300">{{ $strat['trades'] }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-1 rounded text-sm {{ $strat['win_rate'] >= 50 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                                    {{ $strat['win_rate'] }}%
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-semibold {{ $strat['profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                ${{ number_format($strat['profit'], 2) }}
                            </td>
                            <td class="py-3 px-4 text-right text-slate-300">
                                ${{ number_format($strat['avg_profit'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Recent Trades -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4">Recent Trades</h3>
        
        @if($trades->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700">
                            <th class="text-left py-3 px-4 text-slate-300 font-semibold">Date</th>
                            <th class="text-left py-3 px-4 text-slate-300 font-semibold">Pair</th>
                            <th class="text-left py-3 px-4 text-slate-300 font-semibold">Direction</th>
                            <th class="text-right py-3 px-4 text-slate-300 font-semibold">P/L</th>
                            <th class="text-center py-3 px-4 text-slate-300 font-semibold">Outcome</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trades as $trade)
                            <tr class="border-b border-slate-800 hover:bg-white/5">
                                <td class="py-3 px-4 text-slate-300">{{ $trade->entry_date->format('M d, Y') }}</td>
                                <td class="py-3 px-4 text-white font-medium">{{ $trade->pair }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded text-sm {{ $trade->direction->value === 'long' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ $trade->direction->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-semibold {{ $trade->profit_loss >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                    ${{ number_format($trade->profit_loss, 2) }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($trade->outcome === 'win')
                                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-full text-sm font-medium">Win</span>
                                    @elseif($trade->outcome === 'loss')
                                        <span class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-sm font-medium">Loss</span>
                                    @else
                                        <span class="px-3 py-1 bg-slate-500/20 text-slate-400 rounded-full text-sm font-medium">BE</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $trades->links() }}
            </div>
        @else
            <div class="text-center py-8 text-slate-400">
                No trades found
            </div>
        @endif
    </div>

    <!-- Feedback History -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
        <h3 class="text-lg font-semibold text-white mb-4">Feedback History</h3>
        
        @if($feedbackHistory->count() > 0)
            <div class="space-y-6">
                @foreach($feedbackHistory as $feedback)
                    <div class="bg-white/5 rounded-lg p-6 border border-slate-700/50">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                                    {{ substr($feedback->analyst->name, 0, 1) }}
                                </div>
                                <div>
                                    <span class="font-semibold text-white block">{{ $feedback->analyst->name }}</span>
                                    <span class="text-slate-400 text-sm">{{ $feedback->submitted_at ? $feedback->submitted_at->format('M d, Y h:i A') : $feedback->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            
                            @if($feedback->confidence_rating)
                                <div class="flex flex-col items-end">
                                    <span class="text-xs text-slate-400">Confidence</span>
                                    <div class="flex items-center gap-1">
                                        <span class="text-xl font-bold text-blue-400">{{ $feedback->confidence_rating }}</span>
                                        <span class="text-sm text-slate-500">/10</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Structured Data -->
                        @if($feedback->strengths || $feedback->weaknesses || $feedback->recommendations)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <!-- Strengths -->
                                @if(is_array($feedback->strengths) && count($feedback->strengths) > 0)
                                    <div class="p-4 bg-green-500/5 rounded-lg border border-green-500/10">
                                        <h4 class="text-green-400 font-semibold mb-2 flex items-center gap-2">
                                            <span>💪</span> Strengths
                                        </h4>
                                        <ul class="space-y-1">
                                            @foreach($feedback->strengths as $item)
                                                <li class="text-sm text-slate-300 flex items-start gap-2">
                                                    <span class="text-green-500/50 mt-1">•</span>
                                                    <span>{{ $item }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Weaknesses -->
                                @if(is_array($feedback->weaknesses) && count($feedback->weaknesses) > 0)
                                    <div class="p-4 bg-red-500/5 rounded-lg border border-red-500/10">
                                        <h4 class="text-red-400 font-semibold mb-2 flex items-center gap-2">
                                            <span>⚠️</span> Weaknesses
                                        </h4>
                                        <ul class="space-y-1">
                                            @foreach($feedback->weaknesses as $item)
                                                <li class="text-sm text-slate-300 flex items-start gap-2">
                                                    <span class="text-red-500/50 mt-1">•</span>
                                                    <span>{{ $item }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Recommendations -->
                                @if(is_array($feedback->recommendations) && count($feedback->recommendations) > 0)
                                    <div class="p-4 bg-blue-500/5 rounded-lg border border-blue-500/10">
                                        <h4 class="text-blue-400 font-semibold mb-2 flex items-center gap-2">
                                            <span>💡</span> Recommendations
                                        </h4>
                                        <ul class="space-y-1">
                                            @foreach($feedback->recommendations as $item)
                                                <li class="text-sm text-slate-300 flex items-start gap-2">
                                                    <span class="text-blue-500/50 mt-1">•</span>
                                                    <span>{{ $item }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Main Content -->
                        <div class="text-slate-300 whitespace-pre-wrap pl-1 border-l-2 border-slate-700">
                            {{ $feedback->content }}
                        </div>

                        <!-- Actions -->
                        @if(auth()->id() === $feedback->analyst_id && $feedback->isEditable())
                            <div class="mt-4 flex justify-end">
                                <a href="{{ route('analyst.feedback.edit', $feedback->id) }}" class="text-sm text-slate-400 hover:text-white flex items-center gap-1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Feedback
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-slate-400">
                No feedback given yet
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Equity Curve Chart
        const equityData = @json($equityCurve);
        new Chart(document.getElementById('equityChart'), {
            type: 'line',
            data: {
                labels: equityData.map(d => d.date),
                datasets: [{
                    label: 'Equity',
                    data: equityData.map(d => d.equity),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { color: '#94a3b8' }, grid: { color: '#334155' } },
                    x: { ticks: { color: '#94a3b8' }, grid: { color: '#334155' } }
                }
            }
        });

        // Monthly P/L Chart
        const monthlyData = @json($monthlyPL);
        new Chart(document.getElementById('monthlyChart'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'P/L',
                    data: monthlyData,
                    backgroundColor: monthlyData.map(v => v >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(239, 68, 68, 0.8)')
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { color: '#94a3b8' }, grid: { color: '#334155' } },
                    x: { ticks: { color: '#94a3b8' }, grid: { color: '#334155' } }
                }
            }
        });

        // Session Performance Chart
        const sessionData = @json($sessionPerformance);
        new Chart(document.getElementById('sessionChart'), {
            type: 'bar',
            data: {
                labels: sessionData.map(d => d.session),
                datasets: [{
                    label: 'Profit',
                    data: sessionData.map(d => d.profit),
                    backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)', 'rgba(236, 72, 153, 0.8)']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { color: '#94a3b8' }, grid: { color: '#334155' } },
                    x: { ticks: { color: '#94a3b8' }, grid: { color: '#334155' } }
                }
            }
        });

        // Win/Loss Distribution Chart
        const winLossData = @json($winLossDistribution);
        new Chart(document.getElementById('winLossChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(winLossData),
                datasets: [{
                    data: Object.values(winLossData),
                    backgroundColor: ['rgba(16, 185, 129, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(100, 116, 139, 0.8)']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#94a3b8', padding: 15 }
                    }
                }
            }
        });
    </script>
    @endpush
@endsection
