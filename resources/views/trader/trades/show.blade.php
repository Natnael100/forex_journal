@extends('layouts.app')

@section('title', 'Trade Details - ' . $trade->pair)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('trader.trades.index') }}" class="text-blue-500 hover:text-blue-600 flex items-center gap-2 mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Trades
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Trade Details</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('trader.trades.edit', $trade) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Edit Trade
            </a>
            <form action="{{ route('trader.trades.destroy', $trade) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this trade?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Trade Info Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Pair -->
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Pair</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $trade->pair }}</p>
            </div>

            <!-- Direction -->
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Direction</h3>
                <p class="text-lg font-semibold {{ $trade->direction->value === 'buy' ? 'text-green-600' : 'text-red-600' }} uppercase">
                    {{ $trade->direction->label() }}
                </p>
            </div>

            <!-- Outcome -->
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Outcome</h3>
                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                    @if($trade->outcome->value === 'win') bg-green-100 text-green-800
                    @elseif($trade->outcome->value === 'loss') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $trade->outcome->label() }}
                </span>
            </div>

            <!-- P/L -->
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Profit/Loss</h3>
                <p class="text-2xl font-bold {{ $trade->profit_loss >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $trade->profit_loss >= 0 ? '+' : '' }}${{ number_format($trade->profit_loss, 2) }}
                </p>
            </div>

            <!-- Pips -->
            @if($trade->pips)
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Pips</h3>
                <p class="text-lg font-semibold text-gray-900">{{ number_format($trade->pips, 1) }}</p>
            </div>
            @endif

            <!-- R:R -->
            @if($trade->risk_reward_ratio)
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Risk:Reward</h3>
                <p class="text-lg font-semibold text-gray-900">1:{{ number_format($trade->risk_reward_ratio, 2) }}</p>
            </div>
            @endif

            <!-- Entry Date -->
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Entry Date</h3>
                <p class="text-gray-900">{{ $trade->entry_date->format('M d, Y H:i') }}</p>
            </div>

            <!-- Exit Date -->
            @if($trade->exit_date)
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Exit Date</h3>
                <p class="text-gray-900">{{ $trade->exit_date->format('M d, Y H:i') }}</p>
            </div>
            @endif

            <!-- Session -->
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Session</h3>
                <p class="text-gray-900 capitalize">{{ $trade->session->label() }}</p>
            </div>

            <!-- Strategy -->
            @if($trade->strategy)
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Strategy</h3>
                <p class="text-gray-900">{{ $trade->strategy }}</p>
            </div>
            @endif

            <!-- Emotion -->
            @if($trade->emotion)
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-1">Emotion</h3>
                <p class="text-gray-900 capitalize">{{ $trade->emotion }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Notes -->
    @if($trade->notes)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Notes</h3>
        <p class="text-gray-700 whitespace-pre-wrap">{{ $trade->notes }}</p>
    </div>
    @endif

    <!-- TradingView Chart -->
    @if($trade->tradingview_link)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Chart</h3>
        <a href="{{ $trade->tradingview_link }}" target="_blank" class="text-blue-600 hover:text-blue-700 underline">
            View on TradingView
        </a>
    </div>
    @endif

    <!-- Feedback Section -->
    @if($trade->has_feedback)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-blue-900">Analyst Feedback Available</h3>
        </div>
        <p class="text-blue-700">This trade has received feedback from your analyst.</p>
        <a href="{{ route('trader.feedback.index') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-700 font-medium">
            View Feedback →
        </a>
    </div>
    @endif
</div>
@endsection
