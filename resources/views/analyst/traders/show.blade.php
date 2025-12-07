@extends('layouts.app')

@section('title', $trader->name . ' - Trader Profile')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">{{ $trader->name }}</h1>
            <p class="text-slate-400">Performance Analysis & Feedback</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('analyst.feedback.create', ['trader' => $trader->id]) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                💬 Provide Feedback
            </a>
            <a href="{{ route('analyst.dashboard') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
                ← Back
            </a>
        </div>
    </div>

    <!-- Key Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <p class="text-sm text-slate-400 mb-1">Total Trades</p>
            <p class="text-3xl font-bold text-white">{{ $stats['total_trades'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-emerald-900/20 to-teal-900/20 backdrop-blur-xl rounded-xl p-6 border border-emerald-700/50">
            <p class="text-sm text-slate-400 mb-1">Win Rate</p>
            <p class="text-3xl font-bold text-emerald-400">{{ number_format($stats['win_rate'], 1) }}%</p>
        </div>
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <p class="text-sm text-slate-400 mb-1">Avg R:R Ratio</p>
            <p class="text-3xl font-bold text-white">{{ number_format($stats['avg_rr'], 2) }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-900/20 to-pink-900/20 backdrop-blur-xl rounded-xl p-6 border border-purple-700/50">
            <p class="text-sm text-slate-400 mb-1">Feedback Given</p>
            <p class="text-3xl font-bold text-purple-400">{{ $feedbackCount }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Performance Metrics -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Chart Placeholder -->
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                <h3 class="text-xl font-bold text-white mb-4">Equity Curve</h3>
                <div class="h-64 flex items-center justify-center text-slate-400">
                    <canvas id="equityChart"></canvas>
                </div>
            </div>

            <!-- Additional Metrics -->
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                <h3 class="text-xl font-bold text-white mb-4">Performance Metrics</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-white/5 rounded-lg">
                        <p class="text-sm text-slate-400 mb-1">Profit Factor</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($stats['profit_factor'] ?? 0, 2) }}</p>
                    </div>
                    <div class="p-4 bg-white/5 rounded-lg">
                        <p class="text-sm text-slate-400 mb-1">Expectancy</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($stats['expectancy'] ?? 0, 2) }}</p>
                    </div>
                    <div class="p-4 bg-white/5 rounded-lg">
                        <p class="text-sm text-slate-400 mb-1">Max Drawdown</p>
                        <p class="text-2xl font-bold text-red-400">{{ number_format($stats['max_drawdown'] ?? 0, 1) }}%</p>
                    </div>
                    <div class="p-4 bg-white/5 rounded-lg">
                        <p class="text-sm text-slate-400 mb-1">Recovery Factor</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($stats['recovery_factor'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Trades -->
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                <h3 class="text-xl font-bold text-white mb-4">Recent Trades</h3>
                <div class="space-y-3">
                    @forelse($recentTrades as $trade)
                        <div class="p-4 bg-white/5 rounded-lg flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-white">{{ $trade->pair }}</p>
                                <p class="text-sm text-slate-400">{{ $trade->entry_date->format('M d, Y') }} • {{ $trade->direction->label() }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $trade->outcome->value === 'win' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                    {{ $trade->outcome->label() }}
                                </span>
                                <p class="text-sm mt-1 {{ $trade->profit_loss >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    ${{ number_format($trade->profit_loss, 2) }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-8 text-slate-400">No recent trades</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Trader Info -->
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                <div class="flex justify-center mb-4">
                    <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white text-3xl font-bold">
                        {{ substr($trader->name, 0, 1) }}
                    </div>
                </div>
                <div class="text-center mb-4">
                    <h3 class="text-lg font-semibold text-white">{{ $trader->name }}</h3>
                    <p class="text-sm text-slate-400">{{ $trader->email }}</p>
                </div>
                <div class="space-y-2 text-sm border-t border-slate-700 pt-4">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Member since:</span>
                        <span class="text-white">{{ $trader->created_at->format('M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Total Trades:</span>
                        <span class="text-white">{{ $stats['total_trades'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Previous Feedback -->
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                <h3 class="text-lg font-bold text-white mb-4">Previous Feedback</h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($previousFeedback as $feedback)
                        <div class="p-3 bg-white/5 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $feedback->status === 'locked' ? 'bg-slate-500/20 text-slate-400' : 'bg-blue-500/20 text-blue-400' }}">
                                    {{ ucfirst($feedback->status) }}
                                </span>
                                <span class="text-xs text-slate-400">{{ $feedback->created_at->format('M d, Y') }}</span>
                            </div>
                            <p class="text-sm text-slate-300 line-clamp-3">{{ $feedback->content }}</p>
                            @if($feedback->isEditable())
                                <a href="{{ route('analyst.feedback.edit', $feedback->id) }}" class="mt-2 inline-block text-xs text-blue-400 hover:text-blue-300">
                                    Edit →
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="text-center py-4 text-slate-400 text-sm">No previous feedback</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Chart !== 'undefined') {
                const ctx = document.getElementById('equityChart');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($equityCurve->pluck('date')),
                        datasets: [{
                            label: 'Equity',
                            data: @json($equityCurve->pluck('equity')),
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { 
                                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                                ticks: { color: 'rgba(255, 255, 255, 0.7)' }
                            },
                            x: { 
                                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                                ticks: { color: 'rgba(255, 255, 255, 0.7)' }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
@endsection
