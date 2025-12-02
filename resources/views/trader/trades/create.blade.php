@extends('layouts.app')

@section('title', 'Log New Trade')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('trader.trades.index') }}" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-white">Log New Trade</h1>
                <p class="text-slate-400">Record your trading activity</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('trader.trades.store') }}" class="max-w-4xl">
        @csrf

        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-8 border border-slate-700/50 space-y-8">
            
            <!-- Trade Information -->
            <div>
                <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                        <span class="text-emerald-400">📊</span>
                    </span>
                    Trade Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pair -->
                    <div>
                        <label for="pair" class="block text-sm font-medium text-slate-300 mb-2">
                            Currency Pair *
                        </label>
                        <select 
                            id="pair" 
                            name="pair" 
                            required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 @error('pair') border-red-500 @enderror"
                        >
                            <option value="" class="bg-slate-800">Select pair...</option>
                            @foreach($pairs as $pair)
                                <option value="{{ $pair }}" class="bg-slate-800" {{ old('pair') == $pair ? 'selected' : '' }}>{{ $pair }}</option>
                            @endforeach
                        </select>
                        @error('pair')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Direction -->
                    <div class="md:ml-6">
                        <label class="block text-sm font-medium text-slate-300 mb-3">
                            Direction *
                        </label>
                        <div class="flex gap-4">
                            @foreach($directions as $direction)
                                <label class="flex-1 relative cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="direction" 
                                        value="{{ $direction->value }}" 
                                        required
                                        {{ old('direction') == $direction->value ? 'checked' : '' }}
                                        class="peer sr-only"
                                    >
                                    <div class="px-4 py-3 bg-white/5 border-2 border-white/10 rounded-lg text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-500/20 hover:bg-white/10">
                                        <span class="text-2xl">{{ $direction->icon() }}</span>
                                        <p class="text-sm font-medium text-white mt-1">{{ $direction->label() }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('direction')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Entry Date -->
                    <div>
                        <label for="entry_date" class="block text-sm font-medium text-slate-300 mb-2">
                            Entry Date & Time *
                        </label>
                        <input 
                            type="datetime-local" 
                            id="entry_date" 
                            name="entry_date" 
                            required
                            value="{{ old('entry_date') }}"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('entry_date') border-red-500 @enderror"
                        >
                        @error('entry_date')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Exit Date -->
                    <div>
                        <label for="exit_date" class="block text-sm font-medium text-slate-300 mb-2">
                            Exit Date & Time
                        </label>
                        <input 
                            type="datetime-local" 
                            id="exit_date" 
                            name="exit_date"
                            value="{{ old('exit_date') }}"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('exit_date') border-red-500 @enderror"
                        >
                        @error('exit_date')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Session -->
                    <div>
                        <label for="session" class="block text-sm font-medium text-slate-300 mb-2">
                            Market Session *
                        </label>
                        <select 
                            id="session" 
                            name="session" 
                            required
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('session') border-red-500 @enderror"
                        >
                            <option value="" class="bg-slate-800">Select session...</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->value }}" class="bg-slate-800" {{ old('session') == $session->value ? 'selected' : '' }}>
                                    {{ $session->label() }} ({{ $session->tradingHours() }})
                                </option>
                            @endforeach
                        </select>
                        @error('session')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Strategy -->
                    <div>
                        <label for="strategy" class="block text-sm font-medium text-slate-300 mb-2">
                            Strategy/Setup
                        </label>
                        <input 
                            type="text" 
                            id="strategy" 
                            name="strategy"
                            value="{{ old('strategy') }}"
                            placeholder="e.g., Breakout, Trend Following, Reversal"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('strategy') border-red-500 @enderror"
                        >
                        @error('strategy')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Trade Psychology & Risk -->
            <div>
                <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center">
                        <span class="text-purple-400">🧠</span>
                    </span>
                    Psychology & Risk Management
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Emotion -->
                    <div>
                        <label for="emotion" class="block text-sm font-medium text-slate-300 mb-2">
                            Emotional State
                        </label>
                        <input 
                            type="text" 
                            id="emotion" 
                            name="emotion"
                            value="{{ old('emotion') }}"
                            placeholder="e.g., Confident, Fearful, Calm, Impatient"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('emotion') border-red-500 @enderror"
                        >
                        @error('emotion')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Risk Reward Ratio -->
                    <div>
                        <label for="risk_reward_ratio" class="block text-sm font-medium text-slate-300 mb-2">
                            Risk:Reward Ratio
                        </label>
                        <input 
                            type="number" 
                            id="risk_reward_ratio" 
                            name="risk_reward_ratio"
                            step="0.01"
                            min="0"
                            value="{{ old('risk_reward_ratio') }}"
                            placeholder="e.g., 3.00 (for 1:3)"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('risk_reward_ratio') border-red-500 @enderror"
                        >
                        @error('risk_reward_ratio')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-slate-500">Enter the reward multiplier (1:3 = 3.00)</p>
                    </div>
                </div>
            </div>

            <!-- Trade Results -->
            <div>
                <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                        <span class="text-emerald-400">💰</span>
                    </span>
                    Trade Results
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Outcome -->
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-slate-300 mb-3">
                            Outcome *
                        </label>
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($outcomes as $outcome)
                                <label class="relative cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="outcome" 
                                        value="{{ $outcome->value }}" 
                                        required
                                        {{ old('outcome') == $outcome->value ? 'checked' : '' }}
                                        class="peer sr-only"
                                    >
                                    <div class="px-4 py-3 bg-white/5 border-2 border-white/10 rounded-lg text-center transition-all peer-checked:border-emerald-500 peer-checked:{{ $outcome->colorClass() }} hover:bg-white/10">
                                        <span class="text-2xl">{{ $outcome->icon() }}</span>
                                        <p class="text-sm font-medium text-white mt-1">{{ $outcome->label() }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('outcome')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pips -->
                    <div>
                        <label for="pips" class="block text-sm font-medium text-slate-300 mb-2">
                            Pips Gained/Lost
                        </label>
                        <input 
                            type="number" 
                            id="pips" 
                            name="pips"
                            step="0.01"
                            value="{{ old('pips') }}"
                            placeholder="e.g., 50.5"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('pips') border-red-500 @enderror"
                        >
                        @error('pips')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Profit/Loss -->
                    <div class="md:col-span-2">
                        <label for="profit_loss" class="block text-sm font-medium text-slate-300 mb-2">
                            Profit/Loss Amount ($) *
                        </label>
                        <input 
                            type="number" 
                            id="profit_loss" 
                            name="profit_loss"
                            step="0.01"
                            required
                            value="{{ old('profit_loss') }}"
                            placeholder="e.g., 150.00 or -75.50"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('profit_loss') border-red-500 @enderror"
                        >
                        @error('profit_loss')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-slate-500">Use negative value for losses</p>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div>
                <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <span class="text-blue-400">📝</span>
                    </span>
                    Additional Information
                </h2>

                <div class="space-y-6">
                    <!-- TradingView Link -->
                    <div>
                        <label for="tradingview_link" class="block text-sm font-medium text-slate-300 mb-2">
                            TradingView Chart Link
                        </label>
                        <input 
                            type="url" 
                            id="tradingview_link" 
                            name="tradingview_link"
                            value="{{ old('tradingview_link') }}"
                            placeholder="https://www.tradingview.com/chart/..."
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('tradingview_link') border-red-500 @enderror"
                        >
                        @error('tradingview_link')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-slate-300 mb-2">
                            Trade Notes
                        </label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            rows="4"
                            placeholder="Add any observations, lessons learned, or important details about this trade..."
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none @error('notes') border-red-500 @enderror"
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4 pt-6 border-t border-slate-700/50">
                <button 
                    type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-lg shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 transition-all duration-200 transform hover:-translate-y-0.5"
                >
                    Log Trade
                </button>
                <a 
                    href="{{ route('trader.trades.index') }}" 
                    class="px-8 py-3 bg-white/5 hover:bg-white/10 border border-white/10 text-slate-300 font-medium rounded-lg transition-all duration-200"
                >
                    Cancel
                </a>
            </div>
        </div>
    </form>
@endsection
