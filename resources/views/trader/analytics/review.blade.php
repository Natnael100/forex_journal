@extends('layouts.app')

@section('title', 'Trade Review')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Trade Review & Patterns 🔍</h1>
        <p class="text-slate-400">Identify behavioral patterns and improve decision-making</p>
    </div>

    <!-- Streaks -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span>🔥</span> Current Streak
            </h3>
            <div class="text-center">
                <p class="text-5xl font-bold {{ $patterns['streaks']['current_type'] === 'win' ? 'text-green-400' : 'text-red-400' }} mb-2">
                    {{ $patterns['streaks']['current_streak'] }}
                </p>
                <p class="text-slate-300 text-lg">{{ ucfirst($patterns['streaks']['current_type'] ?? 'No') }} streak</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-800/20 to-emerald-900/20 backdrop-blur-xl rounded-xl p-6 border border-green-700/30">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span>✅</span> Best Win Streak
            </h3>
            <div class="text-center">
                <p class="text-5xl font-bold text-green-400 mb-2">{{ $patterns['streaks']['max_win_streak'] }}</p>
                <p class="text-slate-300 text-lg">Consecutive wins</p>
                <p class="text-xs text-slate-500 mt-2">Your best winning run</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-800/20 to-rose-900/20 backdrop-blur-xl rounded-xl p-6 border border-red-700/30">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span>⚠️</span> Max Loss Streak
            </h3>
            <div class="text-center">
                <p class="text-5xl font-bold text-red-400 mb-2">{{ $patterns['streaks']['max_loss_streak'] }}</p>
                <p class="text-slate-300 text-lg">Consecutive losses</p>
                <p class="text-xs text-slate-500 mt-2">Watch for this pattern</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Time of Day Performance -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">⏰ Time of Day Performance</h3>
            <canvas id="timeOfDayChart"></canvas>
            <div class="mt-4 p-3 bg-blue-500/10 rounded-lg border border-blue-500/20">
                <p class="text-sm text-blue-300">💡 <strong>Tip:</strong> Identify your most profitable trading hours</p>
            </div>
        </div>

        <!-- Day of Week Performance -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">📅 Day of Week Performance</h3>
            <canvas id="dayOfWeekChart"></canvas>
            <div class="mt-4 p-3 bg-purple-500/10 rounded-lg border border-purple-500/20">
                <p class="text-sm text-purple-300">💡 <strong>Tip:</strong> Some traders perform better on specific days</p>
            </div>
        </div>
    </div>

    <!-- Pattern Insights -->
    <div class="bg-gradient-to-br from-blue-800/20 to-indigo-900/20 backdrop-blur-xl rounded-xl p-6 border border-blue-700/30 mb-8">
        <h3 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
            <span>📖</span> Pattern Insights
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-white/5 rounded-lg">
                <h4 class="font-semibold text-white mb-2">🎯 Best Practices Detected</h4>
                <ul class="space-y-2 text-sm text-slate-300">
                    @if($patterns['streaks']['max_win_streak'] >= 3)
                        <li>✅ Strong discipline during winning streaks</li>
                    @endif
                    @if(count($patterns['time_of_day']) > 0)
                        <li>✅ Consistent trading during peak hours</li>
                    @endif
                    <li>✅ Maintaining trade journal for review</li>
                </ul>
            </div>

            <div class="p-4 bg-white/5 rounded-lg">
                <h4 class="font-semibold text-white mb-2">⚠️ Areas for Improvement</h4>
                <ul class="space-y-2 text-sm text-slate-300">
                    @if($patterns['streaks']['max_loss_streak'] >= 3)
                        <li>⚠️ Consider taking a break after {{ $patterns['streaks']['max_loss_streak'] }} consecutive losses</li>
                    @endif
                    <li>💡 Review trades during low-performing time slots</li>
                    <li>💡 Analyze emotional patterns in losing trades</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Behavioral Patterns -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
        <h3 class="text-xl font-semibold text-white mb-6">🧠 Behavioral Pattern Checklist</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-emerald-400 mb-3">✅ Positive Patterns</h4>
                <ul class="space-y-2 text-slate-300">
                    <li class="flex items-start gap-2">
                        <span class="text-green-400 mt-1">▪</span>
                        <span><strong>Following Plan:</strong> Trades match documented strategies</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-400 mt-1">▪</span>
                        <span><strong>Risk Management:</strong> Consistent position sizing</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-400 mt-1">▪</span>
                        <span><strong>Emotional Control:</strong> Trading with confidence</span>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="font-medium text-red-400 mb-3">⚠️ Patterns to Watch</h4>
                <ul class="space-y-2 text-slate-300">
                    <li class="flex items-start gap-2">
                        <span class="text-red-400 mt-1">▪</span>
                        <span><strong>Revenge Trading:</strong> Trading to recover losses quickly</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-400 mt-1">▪</span>
                        <span><strong>Overtrading:</strong> Too many trades in short time</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-400 mt-1">▪</span>
                        <span><strong>Holding Losers:</strong> Keeping losing positions too long</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if Chart is available
            if (typeof window.Chart === 'undefined') {
                console.error('Chart.js is not loaded!');
                return;
            }

            // Time of Day Performance Chart
            const timeData = {!! json_encode($patterns['time_of_day']) !!};
            const timeCtx = document.getElementById('timeOfDayChart').getContext('2d');
            new Chart(timeCtx, {
                type: 'bar',
                data: {
                    labels: timeData.map(t => `${t.hour}:00`),
                    datasets: [{
                        label: 'Profit',
                        data: timeData.map(t => t.profit),
                        backgroundColor: timeData.map(t => t.profit >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(239, 68, 68, 0.8)')
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `$${context.parsed.y.toFixed(2)} (${timeData[context.dataIndex].trades} trades)`
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: '#94a3b8' },
                            grid: { color: 'rgba(148, 163, 184, 0.1)' }
                        },
                        x: {
                            ticks: { color: '#94a3b8' },
                            grid: { display: false }
                        }
                    }
                }
            });

            // Day of Week Performance Chart
            const dayData = {!! json_encode($patterns['day_of_week']) !!};
            const dayCtx = document.getElementById('dayOfWeekChart').getContext('2d');
            new Chart(dayCtx, {
                type: 'bar',
                data: {
                    labels: dayData.map(d => d.day),
                    datasets: [{
                        label: 'Profit',
                        data: dayData.map(d => d.profit),
                        backgroundColor: dayData.map(d => d.profit >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(239, 68, 68, 0.8)')
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `$${context.parsed.y.toFixed(2)} (${dayData[context.dataIndex].trades} trades)`
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: '#94a3b8' },
                            grid: { color: 'rgba(148, 163, 184, 0.1)' }
                        },
                        x: {
                            ticks: { color: '#94a3b8' },
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
@endsection
