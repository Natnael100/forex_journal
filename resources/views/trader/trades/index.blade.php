@extends('layouts.app')

@section('title', 'Trade History')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Trade History</h1>
            <p class="text-slate-400">View and manage all your trades</p>
        </div>
        <a href="{{ route('trader.trades.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-lg shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 transition-all duration-200 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Log New Trade</span>
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 px-6 py-4 bg-emerald-500/20 border border-emerald-500/30 rounded-lg text-emerald-400">
            {{ session('success') }}
        </div>
    @endif

    <!-- Trades Table -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="trades-table" class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-slate-400 border-b border-slate-700/50">
                            <th class="px-4 py-3 font-medium">Date</th>
                            <th class="px-4 py-3 font-medium">Pair</th>
                            <th class="px-4 py-3 font-medium">Direction</th>
                            <th class="px-4 py-3 font-medium">Session</th>
                            <th class="px-4 py-3 font-medium">Strategy</th>
                            <th class="px-4 py-3 font-medium">Outcome</th>
                            <th class="px-4 py-3 font-medium">P/L</th>
                            <th class="px-4 py-3 font-medium">Pips</th>
                            <th class="px-4 py-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse(auth()->user()->trades()->latest()->get() as $trade)
                            <tr class="text-slate-300 hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-4 text-sm">
                                    {{ $trade->entry_date->format('M d, Y H:i') }}
                                </td>
                                <td class="px-4 py-4 font-medium text-white">{{ $trade->pair }}</td>
                                <td class="px-4 py-4">
                                    <span class="flex items-center gap-2 {{ $trade->direction->colorClass() }}">
                                        <span>{{ $trade->direction->icon() }}</span>
                                        <span class="text-sm">{{ $trade->direction->label() }}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $trade->session->colorClass() }}">
                                        {{ $trade->session->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    @if($trade->strategy)
                                        <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs">{{ $trade->strategy }}</span>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full {{ $trade->outcome->colorClass() }}">
                                        <span>{{ $trade->outcome->icon() }}</span>
                                        <span>{{ $trade->outcome->label() }}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-4 font-semibold {{ $trade->profit_loss >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $trade->profit_loss >= 0 ? '+' : '' }}${{ number_format($trade->profit_loss, 2) }}
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    @if($trade->pips)
                                        <span class="{{ $trade->pips >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                            {{ $trade->pips >= 0 ? '+' : '' }}{{ number_format($trade->pips, 1) }}
                                        </span>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('trader.trades.show', $trade) }}" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors" title="View">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('trader.trades.edit', $trade) }}" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('trader.trades.destroy', $trade) }}" onsubmit="return confirm('Are you sure you want to delete this trade?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 hover:bg-slate-700/50 rounded-lg transition-colors" title="Delete">
                                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center">
                                    <div class="text-slate-500">
                                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-lg font-medium mb-2">No trades yet</p>
                                        <p class="text-sm mb-4">Start journaling your trades to track your performance</p>
                                        <a href="{{ route('trader.trades.create') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Log Your First Trade
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
