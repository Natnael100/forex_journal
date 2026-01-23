@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Performance Analytics 📊</h1>
        <p class="text-slate-400">Deep dive into your trading performance</p>
    </div>

    <!-- Filters -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-7 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Account</label>
                <select name="trade_account_id" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option style="background-color: #0f172a; color: white;" value="">All Accounts</option>
                    @foreach($accounts as $account)
                        <option style="background-color: #0f172a; color: white;" value="{{ $account->id }}" {{ ($filters['trade_account_id'] ?? '') == $account->id ? 'selected' : '' }}>
                            {{ $account->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Period</label>
                <select name="period" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option style="background-color: #0f172a; color: white;" value="">Custom</option>
                    <option style="background-color: #0f172a; color: white;" value="this_week" {{ request('period') == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option style="background-color: #0f172a; color: white;" value="last_week" {{ request('period') == 'last_week' ? 'selected' : '' }}>Last Week</option>
                    <option style="background-color: #0f172a; color: white;" value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option style="background-color: #0f172a; color: white;" value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option style="background-color: #0f172a; color: white;" value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Pair</label>
                <select name="pair" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option style="background-color: #0f172a; color: white;" value="">All Pairs</option>
                    @foreach($pairs as $pair)
                        <option style="background-color: #0f172a; color: white;" value="{{ $pair }}" {{ ($filters['pair'] ?? '') == $pair ? 'selected' : '' }}>{{ $pair }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Session</label>
                <select name="session" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option style="background-color: #0f172a; color: white;" value="">All Sessions</option>
                    <option style="background-color: #0f172a; color: white;" value="london" {{ ($filters['session'] ?? '') == 'london' ? 'selected' : '' }}>London</option>
                    <option style="background-color: #0f172a; color: white;" value="newyork" {{ ($filters['session'] ?? '') == 'newyork' ? 'selected' : '' }}>New York</option>
                    <option style="background-color: #0f172a; color: white;" value="asia" {{ ($filters['session'] ?? '') == 'asia' ? 'selected' : '' }}>Asia</option>
                    <option style="background-color: #0f172a; color: white;" value="sydney" {{ ($filters['session'] ?? '') == 'sydney' ? 'selected' : '' }}>Sydney</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('trader.analytics.index') }}" class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @include('components.stat-card', [
            'icon' => '📊',
            'value' => $metrics['total_trades'],
            'label' => 'Total Trades',
            'accentColor' => 'blue'
        ])

        @include('components.stat-card', [
            'icon' => '🎯',
            'value' => $metrics['win_rate'] . '%',
            'label' => 'Win Rate',
            'accentColor' => 'emerald'
        ])

        @include('components.stat-card', [
            'icon' => '💰',
            'value' => $metrics['profit_factor'],
            'label' => 'Profit Factor',
            'accentColor' => 'purple'
        ])

        @include('components.stat-card', [
            'icon' => '📈',
            'value' => '$' . number_format($metrics['expectancy'], 2),
            'label' => 'Expectancy',
            'accentColor' => 'cyan'
        ])

        @include('components.stat-card', [
            'icon' => '⚖️',
            'value' => $metrics['avg_rr'],
            'label' => 'Avg R:R',
            'accentColor' => 'indigo'
        ])

        @include('components.stat-card', [
            'icon' => '📉',
            'value' => '$' . number_format($metrics['max_drawdown'], 2),
            'label' => 'Max Drawdown',
            'accentColor' => 'orange'
        ])

        @include('components.stat-card', [
            'icon' => '🔄',
            'value' => $metrics['recovery_factor'],
            'label' => 'Recovery Factor',
            'accentColor' => 'pink'
        ])

        @include('components.stat-card', [
            'icon' => '⏱️',
            'value' => round($metrics['avg_hold_time'], 1) . 'h',
            'label' => 'Avg Hold Time',
            'accentColor' => 'yellow'
        ])
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Equity Curve -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">Equity Curve</h3>
            <div class="relative h-80 w-full">
                <canvas id="equityCurveChart"></canvas>
            </div>
        </div>

        <!-- Monthly P&L -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">Monthly P&L ({{ now()->year }})</h3>
            <div class="relative h-80 w-full">
                <canvas id="monthlyPLChart"></canvas>
            </div>
        </div>

        <!-- Session Performance -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">Session Performance</h3>
            <div class="relative h-80 w-full">
                <canvas id="sessionChart"></canvas>
            </div>
        </div>

        <!-- Win/Loss Distribution -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">Outcome Distribution</h3>
            <div class="relative h-80 w-full">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Best/Worst Pairs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-800/20 to-emerald-900/20 backdrop-blur-xl rounded-xl p-6 border border-green-700/30">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span>✅</span> Best Performing Pairs
            </h3>
            <div class="space-y-3">
                @foreach($bestWorstPairs['best'] as $pair)
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div>
                            <p class="font-medium text-white">{{ $pair['pair'] }}</p>
                            <p class="text-xs text-slate-400">{{ $pair['trades'] }} trades</p>
                        </div>
                        <p class="font-semibold text-green-400">+${{ number_format($pair['profit'], 2) }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-800/20 to-rose-900/20 backdrop-blur-xl rounded-xl p-6 border border-red-700/30">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span>⚠️</span> Worst Performing Pairs
            </h3>
            <div class="space-y-3">
                @foreach($bestWorstPairs['worst'] as $pair)
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div>
                            <p class="font-medium text-white">{{ $pair['pair'] }}</p>
                            <p class="text-xs text-slate-400">{{ $pair['trades'] }} trades</p>
                        </div>
                        <p class="font-semibold text-red-400">${{ number_format($pair['profit'], 2) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Trade Review Link -->
    <div class="bg-gradient-to-br from-blue-800/20 to-indigo-900/20 backdrop-blur-xl rounded-xl p-6 border border-blue-700/30 text-center">
        <h3 class="text-xl font-semibold text-white mb-2">🔍 Pattern Recognition</h3>
        <p class="text-slate-300 mb-4">Discover behavioral patterns and improve your decision-making</p>
        <a href="{{ route('trader.analytics.review') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
            View Trade Review
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if Chart is available
            if (typeof window.Chart === 'undefined') {
                console.error('Chart.js is not loaded!');
                return;
            }

            // Equity Curve Chart
            const equityCtx = document.getElementById('equityCurveChart').getContext('2d');
            new Chart(equityCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_column($equityCurve, 'date')) !!},
                    datasets: [{
                        label: 'Equity',
                        data: {!! json_encode(array_column($equityCurve, 'equity')) !!},
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `$${context.parsed.y.toFixed(2)}`
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

            // Monthly P&L Chart
            const monthlyCtx = document.getElementById('monthlyPLChart').getContext('2d');
            const monthlyData = {!! json_encode($monthlyPL) !!};
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'P&L',
                        data: monthlyData,
                        backgroundColor: monthlyData.map(v => v >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(239, 68, 68, 0.8)')
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `$${context.parsed.y.toFixed(2)}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: '#94a3b8' },
                            grid: { color: 'rgba(148, 163,184, 0.1)' }
                        },
                        x: {
                            ticks: { color: '#94a3b8' },
                            grid: { display: false }
                        }
                    }
                }
            });

            // Session Performance Chart
            const sessionCtx = document.getElementById('sessionChart').getContext('2d');
            const sessionData = {!! json_encode($sessionPerformance) !!};
            new Chart(sessionCtx, {
                type: 'bar',
                data: {
                    labels: sessionData.map(s => s.session),
                    datasets: [{
                        label: 'Profit',
                        data: sessionData.map(s => s.profit),
                        backgroundColor: sessionData.map(s => s.profit >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(239, 68, 68, 0.8)')
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `$${context.parsed.x.toFixed(2)} (${sessionData[context.dataIndex].trades} trades)`
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#94a3b8' },
                            grid: { color: 'rgba(148, 163, 184, 0.1)' }
                        },
                        y: {
                            ticks: { color: '#94a3b8' },
                            grid: { display: false }
                        }
                    }
                }
            });

            // Win/Loss Distribution Chart
            const distCtx = document.getElementById('distributionChart').getContext('2d');
            const distribution = {!! json_encode($winLossDistribution) !!};
            new Chart(distCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(distribution),
                    datasets: [{
                        data: Object.values(distribution),
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(251, 191, 36, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#94a3b8' }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
@endsection
