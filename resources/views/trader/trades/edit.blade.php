@extends('layouts.app')

@section('title', 'Edit Trade')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Edit Trade</h1>
                <p class="text-slate-400">Update your trade details.</p>
            </div>
            <a href="{{ route('trader.trades.show', $trade) }}" class="px-4 py-2 text-slate-400 hover:text-white transition-colors">
                Cancel
            </a>
        </div>

        <form action="{{ route('trader.trades.update', $trade) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Trade Basics -->
            <div class="bg-slate-800/50 rounded-xl border border-slate-700/50 p-6">
                <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                    <span>📊</span> Trade Basics
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Instrument -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Instrument *</label>
                        <select name="pair" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-slate-600 transition-colors">
                            <option value="">Select instrument</option>
                            @foreach($pairs as $pair)
                                <option value="{{ $pair }}" {{ old('pair', $trade->pair) == $pair ? 'selected' : '' }}>{{ $pair }}</option>
                            @endforeach
                        </select>
                        @error('pair') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Trading Account -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Trading Account *</label>
                        <select name="trade_account_id" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-slate-600 transition-colors">
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('trade_account_id', $trade->trade_account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->account_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('trade_account_id') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Strategy -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Strategy</label>
                        <select name="strategy_id" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-slate-600 transition-colors">
                            <option value="">Select strategy (optional)</option>
                            @foreach($strategies as $strategy)
                                <option value="{{ $strategy->id }}" {{ old('strategy_id', $trade->strategy_id) == $strategy->id ? 'selected' : '' }}>{{ $strategy->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Trade Type -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Trade Type</label>
                        <select name="trade_type" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-slate-600 transition-colors">
                            <option value="">Select type</option>
                            @foreach(['Scalp', 'Day Trade', 'Swing', 'Position'] as $type)
                                <option value="{{ $type }}" {{ old('trade_type', $trade->trade_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Direction -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Direction *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="direction" value="buy" class="peer sr-only" {{ old('direction', $trade->direction?->value) == 'buy' ? 'checked' : '' }}>
                                <div class="text-center py-3 rounded-lg border border-slate-700 bg-slate-900/50 peer-checked:bg-emerald-500/20 peer-checked:border-emerald-500 peer-checked:text-emerald-400 transition-all hover:bg-slate-800">
                                    BUY (Long) 📈
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="direction" value="sell" class="peer sr-only" {{ old('direction', $trade->direction?->value) == 'sell' ? 'checked' : '' }}>
                                <div class="text-center py-3 rounded-lg border border-slate-700 bg-slate-900/50 peer-checked:bg-red-500/20 peer-checked:border-red-500 peer-checked:text-red-400 transition-all hover:bg-slate-800">
                                    SELL (Short) 📉
                                </div>
                            </label>
                        </div>
                        @error('direction') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Timing -->
            <div class="bg-slate-800/50 rounded-xl border border-slate-700/50 p-6">
                <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                    <span>⏱️</span> Timing
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Entry Date & Time *</label>
                        <input type="datetime-local" name="entry_date" value="{{ old('entry_date', $trade->entry_date ? $trade->entry_date->format('Y-m-d\TH:i') : '') }}" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 [color-scheme:dark]">
                        @error('entry_date') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Exit Date & Time</label>
                        <input type="datetime-local" name="exit_date" value="{{ old('exit_date', $trade->exit_date ? $trade->exit_date->format('Y-m-d\TH:i') : '') }}" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 [color-scheme:dark]">
                    </div>
                </div>
            </div>

            <!-- Price & Risk -->
            <div class="bg-slate-800/50 rounded-xl border border-slate-700/50 p-6">
                <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                    <span>💰</span> Price & Risk
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Entry Price *</label>
                        <input type="number" step="0.00001" name="entry_price" value="{{ old('entry_price', $trade->entry_price) }}" placeholder="1.0850" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('entry_price') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Exit Price *</label>
                        <input type="number" step="0.00001" name="exit_price" value="{{ old('exit_price', $trade->exit_price) }}" placeholder="1.0900" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('exit_price') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Stop Loss</label>
                        <input type="number" step="0.00001" name="stop_loss" value="{{ old('stop_loss', $trade->stop_loss) }}" placeholder="1.0800" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Take Profit</label>
                        <input type="number" step="0.00001" name="take_profit" value="{{ old('take_profit', $trade->take_profit) }}" placeholder="1.0950" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Lot Size *</label>
                        <input type="number" step="0.01" name="lot_size" value="{{ old('lot_size', $trade->lot_size) }}" placeholder="0.5" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('lot_size') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Risk per trade (%)</label>
                        <div class="relative">
                            <input type="number" step="0.1" name="risk_percentage" value="{{ old('risk_percentage', $trade->risk_percentage) }}" placeholder="1.0" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 pr-8">
                            <span class="absolute right-4 top-3 text-slate-400 font-medium">%</span>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Trading Session</label>
                        <select name="session" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-slate-600 transition-colors">
                            <option value="">Auto-detect from time</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->value }}" {{ old('session', $trade->session?->value) == $session->value ? 'selected' : '' }}>{{ $session->label() }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Leave blank to re-run auto-detection on save</p>
                    </div>
                </div>
            </div>

            <!-- Psychology -->
            <div class="bg-slate-800/50 rounded-xl border border-slate-700/50 p-6">
                <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                    <span>🧠</span> Psychology
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Pre-trade Emotion</label>
                        <select name="pre_trade_emotion" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-slate-600 transition-colors">
                            <option value="">How did you feel before?</option>
                            @foreach($emotions as $emotion)
                                <option value="{{ $emotion->value }}" {{ old('pre_trade_emotion', $trade->pre_trade_emotion) == $emotion->value ? 'selected' : '' }}>{{ $emotion->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Post-trade Emotion</label>
                        <select name="post_trade_emotion" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-slate-600 transition-colors">
                            <option value="">How did you feel after?</option>
                            @foreach($postEmotions as $emotion)
                                <option value="{{ $emotion->value }}" {{ old('post_trade_emotion', $trade->post_trade_emotion) == $emotion->value ? 'selected' : '' }}>{{ $emotion->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                         <label class="block text-sm font-medium text-slate-300 mb-2">Followed your plan?</label>
                         <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="followed_plan" value="1" class="peer sr-only" {{ old('followed_plan', $trade->followed_plan === true ? '1' : ($trade->followed_plan === false ? '0' : '')) == '1' ? 'checked' : '' }}>
                                <div class="px-4 py-2 rounded-lg border border-slate-700 bg-slate-900/50 text-slate-400 peer-checked:bg-emerald-500/20 peer-checked:border-emerald-500 peer-checked:text-emerald-400 transition-all">
                                    Yes, followed plan
                                </div>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="followed_plan" value="0" class="peer sr-only" {{ old('followed_plan', $trade->followed_plan === true ? '1' : ($trade->followed_plan === false ? '0' : '')) === '0' ? 'checked' : '' }}>
                                <div class="px-4 py-2 rounded-lg border border-slate-700 bg-slate-900/50 text-slate-400 peer-checked:bg-red-500/20 peer-checked:border-red-500 peer-checked:text-red-400 transition-all">
                                    No, broke rules
                                </div>
                            </label>
                         </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-slate-800/50 rounded-xl border border-slate-700/50 p-6">
                <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                    <span>📓</span> Notes & Attachments
                </h2>
                <div class="space-y-6">
                     <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Mistakes / Lessons</label>
                        <textarea name="mistakes_lessons" rows="3" placeholder="What did you learn? Any mistakes?" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('mistakes_lessons', $trade->mistakes_lessons) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Setup / Reason for trade</label>
                        <textarea name="setup_notes" rows="3" placeholder="What was your setup? Why did you enter?" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('setup_notes', $trade->setup_notes) }}</textarea>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Chart Link</label>
                        <input type="url" name="chart_link" value="{{ old('chart_link', $trade->chart_link) }}" placeholder="https://www.tradingview.com/x/..." class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-blue-400">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-lg rounded-xl transition-all shadow-lg shadow-emerald-900/20 transform hover:-translate-y-0.5">
                Update Trade Entry 🚀
            </button>
        </form>
    </div>
@endsection
