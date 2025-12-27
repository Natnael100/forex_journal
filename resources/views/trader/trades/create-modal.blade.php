<!-- Trade Creation Modal -->
<div id="tradeModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity opacity-0" id="tradeModalBackdrop"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-slate-900 border border-slate-700 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="tradeModalPanel">
                
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-700 bg-slate-800/50 flex justify-between items-center sticky top-0 z-20 backdrop-blur-md">
                    <div>
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <span>📝</span> Log New Trade
                        </h3>
                        <p class="text-xs text-slate-400">Record trade details</p>
                    </div>
                    <button onclick="closeTradeModal()" class="text-slate-400 hover:text-white transition-colors bg-slate-800 p-2 rounded-lg hover:bg-slate-700">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Form -->
                <form action="{{ route('trader.trades.store') }}" method="POST" class="p-6">
                    @csrf
                    
                    <div class="max-h-[calc(100vh-200px)] overflow-y-auto space-y-8 pr-2 custom-scrollbar">
                        
                        <!-- Trade Basics -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Instrument -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Instrument *</label>
                                <select name="pair" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select instrument</option>
                                    @foreach($pairs as $pair)
                                        <option value="{{ $pair }}">{{ $pair }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Account -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Account *</label>
                                <select name="trade_account_id" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">
                                            {{ $account->account_name }} ({{ $account->balance_formatted }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Strategy -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Strategy</label>
                                <select name="strategy_id" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">No Strategy</option>
                                    @foreach($strategies as $strategy)
                                        <option value="{{ $strategy->id }}">{{ $strategy->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                             <!-- Trade Type -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Trade Type</label>
                                <select name="trade_type" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select type</option>
                                    <option value="Scalp">Scalp</option>
                                    <option value="Day Trade">Day Trade</option>
                                    <option value="Swing">Swing</option>
                                    <option value="Position">Position</option>
                                </select>
                            </div>
                        </div>

                        <!-- Direction -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Direction *</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer" onclick="selectRadioModal('direction', 'buy')">
                                    <input type="radio" name="direction" value="buy" class="hidden">
                                    <div id="modal-btn-direction-buy" data-group="direction" class="text-center py-3 rounded-lg border border-slate-700 bg-slate-900/50 text-slate-400 transition-all hover:bg-slate-800">
                                        BUY (Long) 📈
                                    </div>
                                </label>
                                <label class="cursor-pointer" onclick="selectRadioModal('direction', 'sell')">
                                    <input type="radio" name="direction" value="sell" class="hidden">
                                    <div id="modal-btn-direction-sell" data-group="direction" class="text-center py-3 rounded-lg border border-slate-700 bg-slate-900/50 text-slate-400 transition-all hover:bg-slate-800">
                                        SELL (Short) 📉
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="h-px bg-slate-700/50"></div>

                        <!-- Price & Risk -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                             <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Entry Price *</label>
                                <input type="number" step="0.00001" name="entry_price" placeholder="1.0850" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Exit Price *</label>
                                <input type="number" step="0.00001" name="exit_price" placeholder="1.0900" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Stop Loss</label>
                                <input type="number" step="0.00001" name="stop_loss" placeholder="1.0800" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Take Profit</label>
                                <input type="number" step="0.00001" name="take_profit" placeholder="1.0950" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Lot Size *</label>
                                <input type="number" step="0.01" name="lot_size" placeholder="1.0" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Entry Date</label>
                                <input type="datetime-local" name="entry_date" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 [color-scheme:dark]">
                            </div>
                        </div>

                        <div class="h-px bg-slate-700/50"></div>

                        <!-- Psychology & Notes (Collapsed/ simplified for modal) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Emotions</label>
                                <div class="grid grid-cols-2 gap-2">
                                     <select name="pre_trade_emotion" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Pre-trade</option>
                                        @foreach($emotions as $emotion)
                                            <option value="{{ $emotion->value }}">{{ $emotion->label() }}</option>
                                        @endforeach
                                    </select>
                                     <select name="post_trade_emotion" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Post-trade</option>
                                        @foreach($postEmotions as $emotion)
                                            <option value="{{ $emotion->value }}">{{ $emotion->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Notes</label>
                                <textarea name="setup_notes" rows="2" placeholder="Quick setup notes..." class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="pt-6 border-t border-slate-700 mt-6 flex justify-end gap-3 sticky bottom-0 bg-slate-900 z-10">
                        <button type="button" onclick="closeTradeModal()" class="px-6 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-lg shadow-emerald-900/20 transition-all hover:scale-[1.02]">
                            Save Trade 💾
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const tradeModal = document.getElementById('tradeModal');
    const tradeBackdrop = document.getElementById('tradeModalBackdrop');
    const tradePanel = document.getElementById('tradeModalPanel');

    function openTradeModal() {
        tradeModal.classList.remove('hidden');
        // Set default date to now if empty
        const dateInput = tradeModal.querySelector('input[name="entry_date"]');
        if (!dateInput.value) {
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            dateInput.value = now.toISOString().slice(0, 16);
        }

        setTimeout(() => {
            tradeBackdrop.classList.remove('opacity-0');
            tradePanel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            tradePanel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    }

    function closeTradeModal() {
        tradeBackdrop.classList.add('opacity-0');
        tradePanel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        tradePanel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
        setTimeout(() => {
            tradeModal.classList.add('hidden');
        }, 300);
    }

    function selectRadioModal(group, value) {
        // 1. Check Input (Scoped to modal)
        const modal = document.getElementById('tradeModalPanel');
        const input = modal.querySelector(`input[name="${group}"][value="${value}"]`);
        if (input) input.checked = true;

        // 2. Reset Visuals
        const buttons = modal.querySelectorAll(`[data-group="${group}"]`);
        buttons.forEach(btn => {
            btn.classList.remove('bg-emerald-500/20', 'border-emerald-500', 'text-emerald-400', 'bg-red-500/20', 'border-red-500', 'text-red-400', 'bg-slate-900/50', 'text-slate-400');
            btn.classList.add('bg-slate-900/50', 'border-slate-700', 'text-slate-400');
        });

        // 3. Set Active Visual
        const activeBtn = document.getElementById(`modal-btn-${group}-${value}`);
        if (activeBtn) {
             activeBtn.classList.remove('bg-slate-900/50', 'border-slate-700', 'text-slate-400');
             if (value === 'sell') {
                 activeBtn.classList.add('bg-red-500/20', 'border-red-500', 'text-red-400');
             } else {
                 activeBtn.classList.add('bg-emerald-500/20', 'border-emerald-500', 'text-emerald-400');
             }
        }
    }

    // Auto-open if validation errors exist
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            openTradeModal();
            // Re-init selection if old exists
            const oldDir = "{{ old('direction') }}";
            if(oldDir) selectRadioModal('direction', oldDir);
        });
    @endif
</script>
