@extends('layouts.app')

@section('title', 'Log New Trade')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">New Trade Journal Entry 📝</h1>
                <p class="text-slate-400">Log your trade with full details for comprehensive analysis.</p>
            </div>
            <a href="{{ route('trader.trades.index') }}" class="px-4 py-2 text-slate-400 hover:text-white transition-colors">
                Cancel
            </a>
        </div>

        <form action="{{ route('trader.trades.store') }}" method="POST" class="space-y-8">
            @csrf

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
                                <option value="{{ $pair }}" {{ old('pair') == $pair ? 'selected' : '' }}>{{ $pair }}</option>
                            @endforeach
                        </select>
                        @error('pair') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Trading Account -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Trading Account *</label>
                        <select name="trade_account_id" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-slate-600 transition-colors">
                            <option value="">Select account</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('trade_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->account_name }} ({{ $account->balance_formatted }})
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
                                <option value="{{ $strategy->id }}" {{ old('strategy_id') == $strategy->id ? 'selected' : '' }}>{{ $strategy->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Trade Type -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Trade Type</label>
                        <select name="trade_type" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-slate-600 transition-colors">
                            <option value="">Select type</option>
                            <option value="Scalp" {{ old('trade_type') == 'Scalp' ? 'selected' : '' }}>Scalp</option>
                            <option value="Day Trade" {{ old('trade_type') == 'Day Trade' ? 'selected' : '' }}>Day Trade</option>
                            <option value="Swing" {{ old('trade_type') == 'Swing' ? 'selected' : '' }}>Swing</option>
                            <option value="Position" {{ old('trade_type') == 'Position' ? 'selected' : '' }}>Position</option>
                        </select>
                    </div>

                    <!-- Direction -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Direction *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer" onclick="selectRadio('direction', 'buy')">
                                <input type="radio" name="direction" value="buy" class="hidden" {{ old('direction') == 'buy' ? 'checked' : '' }}>
                                <div id="btn-direction-buy" data-group="direction" class="text-center py-3 rounded-lg border border-slate-700 bg-slate-900/50 text-slate-400 transition-all hover:bg-slate-800">
                                    BUY (Long) 📈
                                </div>
                            </label>
                            <label class="cursor-pointer" onclick="selectRadio('direction', 'sell')">
                                <input type="radio" name="direction" value="sell" class="hidden" {{ old('direction') == 'sell' ? 'checked' : '' }}>
                                <div id="btn-direction-sell" data-group="direction" class="text-center py-3 rounded-lg border border-slate-700 bg-slate-900/50 text-slate-400 transition-all hover:bg-slate-800">
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
                        <input type="datetime-local" name="entry_date" value="{{ old('entry_date') }}" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 [color-scheme:dark]">
                        @error('entry_date') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Exit Date & Time</label>
                        <input type="datetime-local" name="exit_date" value="{{ old('exit_date') }}" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 [color-scheme:dark]">
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
                        <input type="number" step="0.00001" name="entry_price" value="{{ old('entry_price') }}" placeholder="1.0850" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('entry_price') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Exit Price *</label>
                        <input type="number" step="0.00001" name="exit_price" value="{{ old('exit_price') }}" placeholder="1.0900" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('exit_price') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Stop Loss</label>
                        <input type="number" step="0.00001" name="stop_loss" value="{{ old('stop_loss') }}" placeholder="1.0800" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Take Profit</label>
                        <input type="number" step="0.00001" name="take_profit" value="{{ old('take_profit') }}" placeholder="1.0950" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Lot Size *</label>
                        <input type="number" step="0.01" name="lot_size" value="{{ old('lot_size') }}" placeholder="0.5" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('lot_size') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Risk per trade (%)</label>
                        <div class="relative">
                            <input type="number" step="0.1" name="risk_percentage" value="{{ old('risk_percentage') }}" placeholder="1.0" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 pr-8">
                            <span class="absolute right-4 top-3 text-slate-400 font-medium">%</span>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Trading Session</label>
                        <select name="session" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-slate-600 transition-colors">
                            <option value="">Auto-detect from time</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->value }}" {{ old('session') == $session->value ? 'selected' : '' }}>{{ $session->label() }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Will be auto-detected from entry time if not selected</p>
                    </div>
                </div>
                </div>
            </div>

            <!-- Guided Journaling Focus Section (Phase 6) -->
            @if(isset($focusArea) && $focusArea !== 'standard')
                <div class="bg-indigo-600/10 rounded-xl border border-indigo-500/30 p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-400"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    </div>
                    
                    <h2 class="text-xl font-semibold text-indigo-400 mb-2 flex items-center gap-2 relative z-10">
                        <span>🎯</span> Analyst Focus Area: {{ ucfirst($focusArea) }}
                    </h2>
                    <p class="text-sm text-indigo-300 mb-6 relative z-10">Your analyst wants you to focus on these specific details for this period.</p>

                    <div class="grid grid-cols-1 gap-6 relative z-10">
                        <input type="hidden" name="focus_data[focus_type]" value="{{ $focusArea }}">
                        
                        @if($focusArea === 'psychology')
                            <div>
                                <label class="block text-sm font-medium text-indigo-200 mb-2">Stress Level (1-10)</label>
                                <input type="number" name="focus_data[stress_level]" min="1" max="10" class="w-full bg-slate-900/50 border border-indigo-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-indigo-200 mb-2">Primary Distraction (if any)</label>
                                <input type="text" name="focus_data[distraction]" placeholder="e.g., Phone, Noise, Tiredness" class="w-full bg-slate-900/50 border border-indigo-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-indigo-200 mb-2">Mental State Notes</label>
                                <textarea name="focus_data[mental_notes]" rows="2" placeholder="Describe your thought process..." class="w-full bg-slate-900/50 border border-indigo-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                            </div>
                        @elseif($focusArea === 'execution')
                            <div>
                                <label class="block text-sm font-medium text-indigo-200 mb-2">Slippage (Pips)</label>
                                <input type="number" step="0.1" name="focus_data[slippage]" placeholder="0.0" class="w-full bg-slate-900/50 border border-indigo-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-indigo-200 mb-2">Time Difference (Signal vs Entry)</label>
                                <input type="text" name="focus_data[time_diff]" placeholder="e.g., Immediate, 5 mins late" class="w-full bg-slate-900/50 border border-indigo-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-indigo-200 mb-2">Execution Grade (A-F)</label>
                                <select name="focus_data[execution_grade]" class="w-full bg-slate-900/50 border border-indigo-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Select Grade</option>
                                    <option value="A">A - Perfect</option>
                                    <option value="B">B - Good</option>
                                    <option value="C">C - Hesitated/Chased</option>
                                    <option value="D">D - Bad Entry</option>
                                    <option value="F">F - Panic Trade</option>
                                </select>
                            </div>
                        @elseif($focusArea === 'risk')
                            <div>
                                <label class="block text-sm font-medium text-indigo-200 mb-2">Confluence Check (List 3)</label>
                                <textarea name="focus_data[confluences]" rows="2" placeholder="1. ... 2. ... 3. ..." class="w-full bg-slate-900/50 border border-indigo-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-indigo-200 mb-2">Are you willing to lose this amount?</label>
                                <div class="flex gap-4">
                                     <label class="flex items-center gap-2 cursor-pointer" onclick="selectRadio('risk', 'yes')">
                                        <input type="radio" name="focus_data[risk_acceptance]" value="yes" class="hidden">
                                        <div id="btn-risk-yes" data-group="risk" class="px-4 py-2 rounded-lg border border-indigo-500/30 bg-slate-900/50 text-slate-400 transition-all">Yes</div>
                                     </label>
                                     <label class="flex items-center gap-2 cursor-pointer" onclick="selectRadio('risk', 'no')">
                                        <input type="radio" name="focus_data[risk_acceptance]" value="no" class="hidden">
                                        <div id="btn-risk-no" data-group="risk" class="px-4 py-2 rounded-lg border border-indigo-500/30 bg-slate-900/50 text-slate-400 transition-all">No</div>
                                     </label>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

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
                                <option value="{{ $emotion->value }}" {{ old('pre_trade_emotion') == $emotion->value ? 'selected' : '' }}>{{ $emotion->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Post-trade Emotion</label>
                        <select name="post_trade_emotion" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-slate-600 transition-colors">
                            <option value="">How did you feel after?</option>
                            @foreach($postEmotions as $emotion)
                                <option value="{{ $emotion->value }}" {{ old('post_trade_emotion') == $emotion->value ? 'selected' : '' }}>{{ $emotion->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                         <label class="block text-sm font-medium text-slate-300 mb-2">Followed your plan?</label>
                         <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer" onclick="selectRadio('followed_plan', '1')">
                                <input type="radio" name="followed_plan" value="1" class="hidden" {{ old('followed_plan') == '1' ? 'checked' : '' }}>
                                <div id="btn-followed_plan-1" data-group="followed_plan" class="px-4 py-2 rounded-lg border border-slate-700 bg-slate-900/50 text-slate-400 transition-all">
                                    Yes, followed plan
                                </div>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer" onclick="selectRadio('followed_plan', '0')">
                                <input type="radio" name="followed_plan" value="0" class="hidden" {{ old('followed_plan') === '0' ? 'checked' : '' }}>
                                <div id="btn-followed_plan-0" data-group="followed_plan" class="px-4 py-2 rounded-lg border border-slate-700 bg-slate-900/50 text-slate-400 transition-all">
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
                        <textarea name="mistakes_lessons" rows="3" placeholder="What did you learn? Any mistakes?" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('mistakes_lessons') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Setup / Reason for trade</label>
                        <textarea name="setup_notes" rows="3" placeholder="What was your setup? Why did you enter?" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('setup_notes') }}</textarea>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Chart Link</label>
                        <input type="url" name="chart_link" value="{{ old('chart_link') }}" placeholder="https://www.tradingview.com/x/..." class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-blue-400">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-lg rounded-xl transition-all shadow-lg shadow-emerald-900/20 transform hover:-translate-y-0.5">
                Log Trade Entry 🚀
            </button>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-fill Entry Date with current time if empty
            const entryDateInput = document.querySelector('input[name="entry_date"]');
            if (entryDateInput && !entryDateInput.value) {
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                entryDateInput.value = now.toISOString().slice(0, 16);
            }

            // Init Radios
            document.querySelectorAll('input[type="radio"]:checked').forEach(input => {
                let group = input.name;
                if(group === 'focus_data[risk_acceptance]') group = 'risk';
                selectRadio(group, input.value);
            });
        });

        function selectRadio(group, value) {
            // 1. Check Input
            const selectorName = group === 'risk' ? 'focus_data[risk_acceptance]' : group;
            const input = document.querySelector(`input[name="${selectorName}"][value="${value}"]`);
            if (input) input.checked = true;

            // 2. Reset Visuals
            const buttons = document.querySelectorAll(`[data-group="${group}"]`);
            buttons.forEach(btn => {
                // Remove Active Classes
                btn.classList.remove('bg-emerald-500/20', 'border-emerald-500', 'text-emerald-400', 'bg-red-500/20', 'border-red-500', 'text-red-400');
                // Add Inactive Classes
                btn.classList.add('bg-slate-900/50', 'border-slate-700', 'text-slate-400'); // Default styling
            });

            // 3. Set Active Visual
            const activeBtn = document.getElementById(`btn-${group}-${value}`);
            if (activeBtn) {
                 activeBtn.classList.remove('bg-slate-900/50', 'border-slate-700', 'text-slate-400');
                 
                 // Apply Color based on value/type
                 if (value === 'sell' || value === 'no' || value === '0') {
                     activeBtn.classList.add('bg-red-500/20', 'border-red-500', 'text-red-400');
                 } else {
                     activeBtn.classList.add('bg-emerald-500/20', 'border-emerald-500', 'text-emerald-400');
                 }
            }
        }
    </script>
@endsection
