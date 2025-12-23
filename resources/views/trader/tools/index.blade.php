@extends('layouts.app')

@section('title', 'Trading Tools')

@section('content')
    <!-- Header -->
    <div class="mb-8 pt-6">
        <h1 class="text-3xl font-bold text-white mb-2">Trading Tools 🛠️</h1>
        <p class="text-slate-400">Calculate position sizes, risk, and pip values before entering trades.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">

        <!-- Position Size Calculator -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <span class="text-2xl">📐</span> Position Size Calculator
            </h2>
            <p class="text-sm text-slate-400 mb-6">Calculate the optimal lot size based on your account balance and risk tolerance.</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Account Balance ($)</label>
                    <input type="number" id="ps-balance" value="10000" step="100" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Risk per Trade (%)</label>
                    <input type="number" id="ps-risk" value="1" step="0.1" min="0.1" max="10" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Stop Loss (Pips)</label>
                    <input type="number" id="ps-sl" value="20" step="1" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Pip Value ($)</label>
                    <input type="number" id="ps-pipvalue" value="10" step="0.01" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-slate-500 mt-1">Standard lot = $10/pip for most pairs</p>
                </div>
                <button onclick="calculatePositionSize()" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                    Calculate
                </button>
                <div id="ps-result" class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-lg hidden">
                    <p class="text-sm text-slate-400">Recommended Position Size:</p>
                    <p class="text-2xl font-bold text-emerald-400" id="ps-result-value">0.50 Lots</p>
                    <p class="text-xs text-slate-500 mt-1" id="ps-result-detail"></p>
                </div>
            </div>
        </div>

        <!-- Risk/Reward Calculator -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <span class="text-2xl">⚖️</span> Risk/Reward Calculator
            </h2>
            <p class="text-sm text-slate-400 mb-6">Evaluate your trade's risk-to-reward ratio before entering.</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Entry Price</label>
                    <input type="number" id="rr-entry" value="1.0850" step="0.0001" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Stop Loss Price</label>
                    <input type="number" id="rr-sl" value="1.0800" step="0.0001" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Take Profit Price</label>
                    <input type="number" id="rr-tp" value="1.0950" step="0.0001" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Direction</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="rr-direction" value="buy" class="peer sr-only" checked>
                            <div class="text-center py-2 rounded-lg border border-slate-700 bg-slate-900/50 peer-checked:bg-emerald-500/20 peer-checked:border-emerald-500 peer-checked:text-emerald-400 transition-all text-sm">
                                BUY 📈
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="rr-direction" value="sell" class="peer sr-only">
                            <div class="text-center py-2 rounded-lg border border-slate-700 bg-slate-900/50 peer-checked:bg-red-500/20 peer-checked:border-red-500 peer-checked:text-red-400 transition-all text-sm">
                                SELL 📉
                            </div>
                        </label>
                    </div>
                </div>
                <button onclick="calculateRiskReward()" class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors">
                    Calculate R:R
                </button>
                <div id="rr-result" class="p-4 bg-purple-500/10 border border-purple-500/20 rounded-lg hidden">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-slate-400">Risk:Reward Ratio</p>
                            <p class="text-2xl font-bold text-purple-400" id="rr-result-value">1:2.00</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500">Risk: <span id="rr-risk-pips" class="text-red-400">50 pips</span></p>
                            <p class="text-xs text-slate-500">Reward: <span id="rr-reward-pips" class="text-green-400">100 pips</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pip Value Calculator -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <span class="text-2xl">💰</span> Pip Value Calculator
            </h2>
            <p class="text-sm text-slate-400 mb-6">Calculate the value of a pip for different currency pairs and lot sizes.</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Currency Pair</label>
                    <select id="pv-pair" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="EUR/USD">EUR/USD</option>
                        <option value="GBP/USD">GBP/USD</option>
                        <option value="USD/JPY">USD/JPY</option>
                        <option value="USD/CHF">USD/CHF</option>
                        <option value="AUD/USD">AUD/USD</option>
                        <option value="USD/CAD">USD/CAD</option>
                        <option value="NZD/USD">NZD/USD</option>
                        <option value="XAU/USD">XAU/USD (Gold)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Lot Size</label>
                    <input type="number" id="pv-lots" value="1.00" step="0.01" min="0.01" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Account Currency</label>
                    <select id="pv-currency" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="GBP">GBP</option>
                    </select>
                </div>
                <button onclick="calculatePipValue()" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg transition-colors">
                    Calculate Pip Value
                </button>
                <div id="pv-result" class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-lg hidden">
                    <p class="text-sm text-slate-400">Pip Value:</p>
                    <p class="text-2xl font-bold text-amber-400" id="pv-result-value">$10.00</p>
                    <p class="text-xs text-slate-500 mt-1" id="pv-result-detail">Per pip movement</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Quick Reference -->
    <div class="mt-8 bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
        <h2 class="text-xl font-bold text-white mb-4">📚 Quick Reference</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            <div>
                <h3 class="font-semibold text-white mb-2">Standard Lot Sizes</h3>
                <ul class="space-y-1 text-slate-400">
                    <li>• <span class="text-white">1.00</span> = Standard Lot (100,000 units)</li>
                    <li>• <span class="text-white">0.10</span> = Mini Lot (10,000 units)</li>
                    <li>• <span class="text-white">0.01</span> = Micro Lot (1,000 units)</li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-white mb-2">Risk Management</h3>
                <ul class="space-y-1 text-slate-400">
                    <li>• Conservative: <span class="text-emerald-400">0.5-1%</span> per trade</li>
                    <li>• Moderate: <span class="text-yellow-400">1-2%</span> per trade</li>
                    <li>• Aggressive: <span class="text-red-400">2-5%</span> per trade</li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-white mb-2">R:R Guidelines</h3>
                <ul class="space-y-1 text-slate-400">
                    <li>• Minimum: <span class="text-white">1:1.5</span></li>
                    <li>• Recommended: <span class="text-emerald-400">1:2</span> or higher</li>
                    <li>• Scalping: <span class="text-yellow-400">1:1</span> acceptable</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function calculatePositionSize() {
            const balance = parseFloat(document.getElementById('ps-balance').value) || 0;
            const riskPercent = parseFloat(document.getElementById('ps-risk').value) || 0;
            const stopLossPips = parseFloat(document.getElementById('ps-sl').value) || 1;
            const pipValue = parseFloat(document.getElementById('ps-pipvalue').value) || 10;

            const riskAmount = balance * (riskPercent / 100);
            const positionSize = riskAmount / (stopLossPips * pipValue);

            document.getElementById('ps-result').classList.remove('hidden');
            document.getElementById('ps-result-value').textContent = positionSize.toFixed(2) + ' Lots';
            document.getElementById('ps-result-detail').textContent = 
                `Risk Amount: $${riskAmount.toFixed(2)} | Stop Loss: ${stopLossPips} pips`;
        }

        function calculateRiskReward() {
            const entry = parseFloat(document.getElementById('rr-entry').value) || 0;
            const sl = parseFloat(document.getElementById('rr-sl').value) || 0;
            const tp = parseFloat(document.getElementById('rr-tp').value) || 0;
            const direction = document.querySelector('input[name="rr-direction"]:checked').value;

            let riskPips, rewardPips;
            
            if (direction === 'buy') {
                riskPips = Math.abs(entry - sl) * 10000;
                rewardPips = Math.abs(tp - entry) * 10000;
            } else {
                riskPips = Math.abs(sl - entry) * 10000;
                rewardPips = Math.abs(entry - tp) * 10000;
            }

            const rrRatio = riskPips > 0 ? rewardPips / riskPips : 0;

            document.getElementById('rr-result').classList.remove('hidden');
            document.getElementById('rr-result-value').textContent = `1:${rrRatio.toFixed(2)}`;
            document.getElementById('rr-risk-pips').textContent = `${riskPips.toFixed(1)} pips`;
            document.getElementById('rr-reward-pips').textContent = `${rewardPips.toFixed(1)} pips`;
        }

        function calculatePipValue() {
            const pair = document.getElementById('pv-pair').value;
            const lots = parseFloat(document.getElementById('pv-lots').value) || 0;
            const accountCurrency = document.getElementById('pv-currency').value;

            // Standard pip values (simplified - normally would use live rates)
            let basePipValue = 10; // USD per pip for 1 standard lot
            
            // Adjust for different pairs (simplified)
            if (pair.includes('JPY')) {
                basePipValue = 9.10; // Approximate
            } else if (pair === 'XAU/USD') {
                basePipValue = 10; // Per 0.01 movement
            }

            const pipValue = basePipValue * lots;

            document.getElementById('pv-result').classList.remove('hidden');
            document.getElementById('pv-result-value').textContent = `$${pipValue.toFixed(2)}`;
            document.getElementById('pv-result-detail').textContent = 
                `${lots} lot(s) × $${basePipValue.toFixed(2)}/pip = $${pipValue.toFixed(2)} per pip`;
        }

        // Auto-calculate on input change
        document.querySelectorAll('#ps-balance, #ps-risk, #ps-sl, #ps-pipvalue').forEach(el => {
            el.addEventListener('input', calculatePositionSize);
        });
        document.querySelectorAll('#rr-entry, #rr-sl, #rr-tp').forEach(el => {
            el.addEventListener('input', calculateRiskReward);
        });
        document.querySelectorAll('input[name="rr-direction"]').forEach(el => {
            el.addEventListener('change', calculateRiskReward);
        });
        document.querySelectorAll('#pv-pair, #pv-lots, #pv-currency').forEach(el => {
            el.addEventListener('change', calculatePipValue);
            el.addEventListener('input', calculatePipValue);
        });
    </script>
@endsection
