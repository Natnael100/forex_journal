@extends('layouts.app')

@section('title', 'All Trades')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">All Trades 📊</h1>
            <p class="text-slate-400">Complete trade history across all traders</p>
        </div>
        <a href="{{ route('admin.analytics.index') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
            ← Back to Analytics
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-6">
        <form method="GET" action="{{ route('admin.analytics.trades') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Search Trader</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name or email..." 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Trader Filter -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Trader</label>
                    <select name="trader_id" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Traders</option>
                        @foreach($traders as $trader)
                            <option value="{{ $trader->id }}" {{ request('trader_id') == $trader->id ? 'selected' : '' }}>
                                {{ $trader->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pair Filter -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Pair</label>
                    <select name="pair" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Pairs</option>
                        @foreach($pairs as $pair)
                            <option value="{{ $pair }}" {{ request('pair') == $pair ? 'selected' : '' }}>
                                {{ $pair }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Outcome Filter -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Outcome</label>
                    <select name="outcome" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Outcomes</option>
                        <option value="win" {{ request('outcome') == 'win' ? 'selected' : '' }}>Win</option>
                        <option value="loss" {{ request('outcome') == 'loss' ? 'selected' : '' }}>Loss</option>
                        <option value="breakeven" {{ request('outcome') == 'breakeven' ? 'selected' : '' }}>Breakeven</option>
                    </select>
                </div>

                <!-- Session Filter -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Session</label>
                    <select name="session" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Sessions</option>
                        <option value="london" {{ request('session') == 'london' ? 'selected' : '' }}>London</option>
                        <option value="new_york" {{ request('session') == 'new_york' ? 'selected' : '' }}>New York</option>
                        <option value="tokyo" {{ request('session') == 'tokyo' ? 'selected' : '' }}>Tokyo</option>
                        <option value="sydney" {{ request('session') == 'sydney' ? 'selected' : '' }}>Sydney</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Sort By -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Sort By</label>
                    <select name="sort_by" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="entry_date" {{ request('sort_by') == 'entry_date' ? 'selected' : '' }}>Entry Date</option>
                        <option value="profit_loss" {{ request('sort_by') == 'profit_loss' ? 'selected' : '' }}>P/L</option>
                        <option value="risk_reward" {{ request('sort_by') == 'risk_reward' ? 'selected' : '' }}>R:R</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('admin.analytics.trades') }}" class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">
                    Clear All
                </a>
                <span class="text-slate-400 text-sm ml-auto">
                    {{ $trades->total() }} trades found
                </span>
            </div>
        </form>
    </div>

    <!-- Trades Table -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50 overflow-hidden">
        @if($trades->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700 bg-slate-800/50">
                            <th class="text-left py-4 px-4 text-slate-300 font-semibold">Trader</th>
                            <th class="text-left py-4 px-4 text-slate-300 font-semibold">Pair</th>
                            <th class="text-center py-4 px-4 text-slate-300 font-semibold">Direction</th>
                            <th class="text-center py-4 px-4 text-slate-300 font-semibold">Entry Date</th>
                            <th class="text-center py-4 px-4 text-slate-300 font-semibold">Outcome</th>
                            <th class="text-center py-4 px-4 text-slate-300 font-semibold">R:R</th>
                            <th class="text-right py-4 px-4 text-slate-300 font-semibold">P/L</th>
                            <th class="text-center py-4 px-4 text-slate-300 font-semibold">Session</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trades as $trade)
                            <tr class="border-b border-slate-800 hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4">
                                    <div>
                                        <p class="text-white font-medium">{{ $trade->user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $trade->user->email }}</p>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-white font-medium">{{ $trade->pair }}</td>
                                <td class="py-4 px-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ $trade->direction === 'long' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ $trade->direction === 'long' ? '📈 Long' : '📉 Short' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center text-slate-300">
                                    {{ $trade->entry_date->format('M d, Y') }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ $trade->outcome === 'win' ? 'bg-green-500/20 text-green-400' : '' }}
                                        {{ $trade->outcome === 'loss' ? 'bg-red-500/20 text-red-400' : '' }}
                                        {{ $trade->outcome === 'breakeven' ? 'bg-slate-500/20 text-slate-400' : '' }}">
                                        {{ ucfirst($trade->outcome) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center text-slate-300">
                                    {{ $trade->risk_reward ? '1:' . round($trade->risk_reward, 2) : 'N/A' }}
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <span class="text-lg font-bold {{ $trade->profit_loss >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $trade->profit_loss >= 0 ? '+' : '' }}${{ number_format($trade->profit_loss, 2) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="text-sm text-slate-400">
                                        {{ $trade->session ? ucfirst(str_replace('_', ' ', $trade->session)) : 'N/A' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-6 border-t border-slate-700">
                {{ $trades->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div class="text-6xl mb-4">📊</div>
                <h3 class="text-xl font-semibold text-white mb-2">No Trades Found</h3>
                <p class="text-slate-400">Try adjusting your filters</p>
            </div>
        @endif
    </div>
@endsection
