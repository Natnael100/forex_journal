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
                <a href="{{ $trader->getProfileUrl() }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                    View Full Profile →
                </a>
            </x-slot>
        </x-profile-card>
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

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Equity Curve -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">Equity Curve</h3>
            <canvas id="equityChart" height="250"></canvas>
        </div>

        <!-- Monthly P/L -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">Monthly P/L ({{ now()->year }})</h3>
            <canvas id="monthlyChart" height="250"></canvas>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Session Performance -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">Session Performance</h3>
            <canvas id="sessionChart" height="250"></canvas>
        </div>

        <!-- Win/Loss Distribution -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">Win/Loss Distribution</h3>
            <canvas id="winLossChart" height="250"></canvas>
        </div>
    </div>

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
            <div class="space-y-4">
                @foreach($feedbackHistory as $feedback)
                    <div class="bg-white/5 rounded-lg p-4 border border-slate-700/50">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-white">{{ $feedback->analyst->name }}</span>
                                <span class="text-slate-400 text-sm">{{ $feedback->created_at->diffForHumans() }}</span>
                            </div>
                            @if($feedback->rating)
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-slate-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                            @endif
                        </div>
                        <p class="text-slate-300">{{ $feedback->message }}</p>
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
