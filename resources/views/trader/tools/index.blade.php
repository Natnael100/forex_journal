@extends('layouts.app')

@section('title', 'Trading Tools')

@section('content')
    <!-- Header -->
    <div class="mb-8 pt-6">
        <h1 class="text-3xl font-bold text-white mb-2">Trading Tools 🛠️</h1>
        <p class="text-slate-400">Professional-grade calculators for risk management and position sizing.</p>
    </div>

    <!-- Global Settings (Shared Context) -->
    <div class="mb-6 bg-slate-800/50 rounded-xl p-4 border border-slate-700/50 flex flex-wrap gap-4 items-center">
        <div class="text-slate-300 font-medium text-sm">Account Settings:</div>
        <div>
            <select id="global-currency" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-white text-sm focus:ring-2 focus:ring-blue-500">
                <option value="USD">USD ($)</option>
                <option value="EUR">EUR (€)</option>
                <option value="GBP">GBP (£)</option>
                <option value="JPY">JPY (¥)</option>
            </select>
        </div>
        <div class="text-xs text-slate-500 ml-auto">
            Calculations assume Standard Lot (100,000 units).
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">

        <!-- Position Size Calculator -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="text-6xl">📐</span>
            </div>
            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2 relative z-10">
                Position Size Calculator
            </h2>
            
            <div class="space-y-4 relative z-10">
                <!-- Row 1: Balance & Risk -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Account Balance</label>
                        <input type="number" id="ps-balance" value="10000" step="100" min="1" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Risk (%)</label>
                        <input type="number" id="ps-risk" value="1.0" step="0.1" min="0.1" max="100" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <!-- Row 2: Pair Selection -->
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Currency Pair</label>
                    <select id="ps-pair" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="EUR/USD">EUR/USD</option>
                        <option value="GBP/USD">GBP/USD</option>
                        <option value="USD/JPY">USD/JPY</option>
                        <option value="USD/CHF">USD/CHF</option>
                        <option value="USD/CAD">USD/CAD</option>
                        <option value="AUD/USD">AUD/USD</option>
                        <option value="NZD/USD">NZD/USD</option>
                        <option value="EUR/JPY">EUR/JPY</option>
                        <option value="GBP/JPY">GBP/JPY</option>
                        <option value="XAU/USD">XAU/USD (Gold)</option>
                    </select>
                </div>

                <!-- Row 3: Price Input (Dynamic) -->
                <div id="ps-price-container" class="">
                    <label class="block text-xs font-medium text-slate-400 mb-1" id="ps-price-label">Current Price (for conversion)</label>
                    <input type="number" id="ps-price" placeholder="Exchange Rate" step="0.0001" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <p class="text-[10px] text-slate-500 mt-1" id="ps-price-hint">Required to convert pip value to account currency.</p>
                </div>

                <!-- Row 4: Stop Loss -->
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Stop Loss (Pips)</label>
                    <input type="number" id="ps-sl" value="20" step="0.1" min="0.1" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <button onclick="calculatePositionSize()" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors shadow-lg shadow-blue-900/20 text-sm">
                    Calculate Position
                </button>

                <!-- Results -->
                <div id="ps-result" class="hidden p-4 bg-slate-900 rounded-lg border-l-4 border-blue-500">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-slate-400 text-sm">Lot Size:</span>
                        <span class="text-2xl font-bold text-white" id="ps-result-lots">0.00</span>
                    </div>
                    <div class="border-t border-slate-800 pt-2 space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Risk Amount:</span>
                            <span class="text-slate-300" id="ps-result-risk">$0.00</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Pip Value (Standard):</span>
                            <span class="text-slate-300" id="ps-result-pipval">$0.00</span>
                        </div>
                    </div>
                </div>
                <div id="ps-error" class="hidden p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-xs text-center"></div>
            </div>
        </div>

        <!-- Risk/Reward Calculator -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="text-6xl">⚖️</span>
            </div>
            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2 relative z-10">
                Risk/Reward Calculator
            </h2>
            
            <div class="space-y-4 relative z-10">
                <!-- Direction -->
                <div class="bg-slate-900/50 p-1 rounded-lg flex border border-slate-700">
                    <button onclick="setRRDirection('buy')" id="btn-buy" class="flex-1 py-1.5 rounded text-sm font-bold transition-all bg-emerald-600 text-white shadow-lg">BUY</button>
                    <button onclick="setRRDirection('sell')" id="btn-sell" class="flex-1 py-1.5 rounded text-sm font-bold transition-all text-slate-400 hover:text-white">SELL</button>
                </div>
                <input type="hidden" id="rr-direction" value="buy">

                <!-- Prices -->
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Entry Price</label>
                        <input type="number" id="rr-entry" step="0.0001" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Stop Loss</label>
                        <input type="number" id="rr-sl" step="0.0001" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Take Profit</label>
                        <input type="number" id="rr-tp" step="0.0001" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm">
                    </div>
                </div>

                <button onclick="calculateRiskReward()" class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-lg transition-colors shadow-lg shadow-purple-900/20 text-sm">
                    Calculate R:R
                </button>

                <!-- Results -->
                <div id="rr-result" class="hidden p-4 bg-slate-900 rounded-lg border-l-4 border-purple-500">
                    <div class="text-center mb-3">
                        <span class="text-3xl font-bold text-white" id="rr-ratio">1 : 0.0</span>
                        <div class="text-xs text-slate-500 mt-1">Risk to Reward Ratio</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-center border-t border-slate-800 pt-3">
                        <div>
                            <div class="text-xs text-slate-500">Risk</div>
                            <div class="text-red-400 font-bold" id="rr-risk">-</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">Reward</div>
                            <div class="text-emerald-400 font-bold" id="rr-reward">-</div>
                        </div>
                    </div>
                </div>
                <div id="rr-error" class="hidden p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-xs text-center"></div>
            </div>
        </div>

        <!-- Quick Reference -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h2 class="text-xl font-bold text-white mb-4">Formulas Used</h2>
            <div class="space-y-4 text-xs text-slate-400">
                <div class="p-3 bg-black/20 rounded border border-slate-700/50">
                    <strong class="text-emerald-400 block mb-1">Position Size</strong>
                    Risk Amount / (SL Pips × Pip Value)
                </div>
                <div class="p-3 bg-black/20 rounded border border-slate-700/50">
                    <strong class="text-purple-400 block mb-1">Risk To Reward</strong>
                    Reward Distance / Risk Distance
                </div>
                <div class="p-3 bg-black/20 rounded border border-slate-700/50">
                    <strong class="text-blue-400 block mb-1">Pip Value</strong>
                    <ul>
                        <li><span class="text-white">USD/JPY</span>: (0.01 × 100k) / Rate</li>
                        <li><span class="text-white">EUR/USD</span>: 0.0001 × 100k</li>
                        <li><span class="text-white">Gold</span>: 0.01 × 100k</li>
                    </ul>
                </div>
                <p class="italic mt-2">*Calculations assume Standard Lots (100,000 units).</p>
            </div>
        </div>

    </div>

    <script>
        // --- State Management ---
        const STANDARD_LOT_UNITS = 100000;
        
        // --- Helper Functions ---
        function getPipSize(pair) {
            if (pair.includes('JPY') && !pair.includes('XAU')) return 0.01;
            if (pair === 'XAU/USD') return 0.01;
            return 0.0001; // Standard for EURUSD, etc.
        }

        function formatCurrency(amount, currency) {
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency }).format(amount);
        }

        function validateInput(value, name) {
            if (isNaN(value) || value <= 0) {
                throw new Error(`${name} must be a positive number.`);
            }
            return value;
        }

        // --- Core Logic: Position Size ---
        function updatePricingInputs() {
            const pair = document.getElementById('ps-pair').value;
            const accountCurrency = document.getElementById('global-currency').value;
            const [base, quote] = pair.split('/');
            
            const priceContainer = document.getElementById('ps-price-container');
            const priceLabel = document.getElementById('ps-price-label');
            const priceInput = document.getElementById('ps-price');

            // Logic: Do we need a conversion rate?
            // Case 1: Quote == Account (e.g. EUR/USD with USD account) -> Pip Value is fixed. No rate needed.
            if (quote === accountCurrency) {
                priceContainer.classList.add('opacity-50', 'pointer-events-none'); // Disable visual
                priceInput.value = ''; 
                priceInput.setAttribute('disabled', 'true');
                document.getElementById('ps-price-hint').innerText = "Direct quote. Pip value is fixed.";
            } 
            // Case 2: Base == Account (e.g. USD/JPY with USD account) -> Divide by Rate.
            else if (base === accountCurrency) {
                priceContainer.classList.remove('opacity-50', 'pointer-events-none');
                priceInput.removeAttribute('disabled');
                priceContainer.classList.remove('hidden');
                priceLabel.innerText = `Current ${pair} Price`;
                document.getElementById('ps-price-hint').innerText = `Required: Pip Value = (Unit × Pip) / Price`;
            }
            // Case 3: Neither (Cross pair) -> e.g. EUR/JPY with USD account.
            // Complex. For simplicity in this version, we request the "AccountCurrency/QuoteCurrency" rate or similar.
            // But usually, standard calculator asks for the "Pair Price" AND "Account/Quote" rate if needed.
            // To keep robust but simple: We strictly ask for the conversion rate to Account Currency.
            else {
                // Determine conversion pair needed.
                // We have Pip Value in Quote Currency (e.g. JPY). We need it in Account Currency (e.g. USD).
                // We need USD/JPY rate (if Account is Base) or JPY/USD (if Account is Quote).
                
                // Let's simplify: Ask user for the rate of [QuoteCurrency][AccountCurrency] or [AccountCurrency][QuoteCurrency]
                // Actually, most simple calc logic: "Exchange Rate for XXX/YYY"
                priceContainer.classList.remove('opacity-50', 'pointer-events-none');
                priceInput.removeAttribute('disabled');
                priceLabel.innerText = `${pair} Price / Conversion`;
                document.getElementById('ps-price-hint').innerText = `Enter exchange rate to convert ${quote} to ${accountCurrency}`;
            }
        }

        function calculatePositionSize() {
            try {
                // Clear errors
                document.getElementById('ps-error').classList.add('hidden');
                document.getElementById('ps-result').classList.add('hidden');

                // Inputs
                const balance = validateInput(parseFloat(document.getElementById('ps-balance').value), 'Balance');
                const riskPercent = validateInput(parseFloat(document.getElementById('ps-risk').value), 'Risk %');
                const stopLoss = validateInput(parseFloat(document.getElementById('ps-sl').value), 'Stop Loss');
                const pair = document.getElementById('ps-pair').value;
                const accountCurrency = document.getElementById('global-currency').value;
                
                const [base, quote] = pair.split('/');
                const pipSize = getPipSize(pair);
                
                // 1. Calculate Risk Amount
                const riskAmount = balance * (riskPercent / 100);

                // 2. Calculate Pip Value per Standard Lot (in Quote Currency)
                // e.g. 100,000 * 0.01 = 1000 JPY
                // e.g. 100,000 * 0.0001 = 10 USD
                let pipValueQuote = STANDARD_LOT_UNITS * pipSize;

                // 3. Convert Pip Value to Account Currency
                let pipValueAccount = 0;
                let conversionPrice = parseFloat(document.getElementById('ps-price').value);

                if (quote === accountCurrency) {
                    // Direct (e.g. EUR/USD -> USD). Pip Value is already in Account Currency.
                    pipValueAccount = pipValueQuote;
                } else if (base === accountCurrency) {
                    // Inverse (e.g. USD/JPY -> USD). Pip Value (JPY) / Rate = USD.
                    if (!conversionPrice || conversionPrice <= 0) throw new Error(`Please enter valid ${pair} price.`);
                    pipValueAccount = pipValueQuote / conversionPrice;
                } else {
                    // Cross. We simply take the input as the conversion factor provided by user manually for now
                    // Or we could implement complex logic. 
                    // Robust fix: For now, if we cannot determine, we error or ask for specific rate.
                    // Let's rely on the user providing the rate that converts Quote -> Account.
                    // If user enters 1.0, it treats 1:1.
                    if (!conversionPrice || conversionPrice <= 0) throw new Error("Please enter conversion rate.");
                     
                    // Heuristic: If trading EUR/GBP (Quote GBP) and Account USD. User enters GBP/USD rate.
                    // PipVal(GBP) * Rate(GBP/USD) = PipVal(USD).
                    // We assume the user inputs the multiplier.
                    pipValueAccount = pipValueQuote * conversionPrice; 
                    // Note: This is an area where UI needs to be very specific "Enter Rate X/Y". 
                    // For this iteration, we stick to Base/Quote handling which covers 90% cases involving USD.
                }

                // 4. Calculate Lots
                // Risk = Lots * SL * PipValue
                // Lots = Risk / (SL * PipValue)
                const lots = riskAmount / (stopLoss * pipValueAccount);

                // Output
                document.getElementById('ps-result').classList.remove('hidden');
                document.getElementById('ps-result-lots').innerText = lots.toFixed(2) + " Lots";
                document.getElementById('ps-result-risk').innerText = formatCurrency(riskAmount, accountCurrency);
                document.getElementById('ps-result-pipval').innerText = formatCurrency(pipValueAccount, accountCurrency);

            } catch (error) {
                const errEl = document.getElementById('ps-error');
                errEl.innerText = error.message;
                errEl.classList.remove('hidden');
            }
        }

        // --- Core Logic: Risk Reward ---
        function setRRDirection(dir) {
            document.getElementById('rr-direction').value = dir;
            const btnBuy = document.getElementById('btn-buy');
            const btnSell = document.getElementById('btn-sell');

            if (dir === 'buy') {
                btnBuy.className = "flex-1 py-1.5 rounded text-sm font-bold transition-all bg-emerald-600 text-white shadow-lg";
                btnSell.className = "flex-1 py-1.5 rounded text-sm font-bold transition-all text-slate-400 hover:text-white";
            } else {
                btnSell.className = "flex-1 py-1.5 rounded text-sm font-bold transition-all bg-red-600 text-white shadow-lg";
                btnBuy.className = "flex-1 py-1.5 rounded text-sm font-bold transition-all text-slate-400 hover:text-white";
            }
            calculateRiskReward(); // Auto recalc
        }

        function calculateRiskReward() {
            try {
                document.getElementById('rr-error').classList.add('hidden');
                document.getElementById('rr-result').classList.add('hidden');

                const entry = parseFloat(document.getElementById('rr-entry').value);
                const sl = parseFloat(document.getElementById('rr-sl').value);
                const tp = parseFloat(document.getElementById('rr-tp').value);
                const direction = document.getElementById('rr-direction').value;

                if (!entry || !sl || !tp) return; // Wait for inputs

                let risk, reward;

                if (direction === 'buy') {
                    if (sl >= entry) throw new Error("For BUY, Stop Loss must be below Entry.");
                    if (tp <= entry) throw new Error("For BUY, Take Profit must be above Entry.");
                    risk = entry - sl;
                    reward = tp - entry;
                } else {
                    if (sl <= entry) throw new Error("For SELL, Stop Loss must be above Entry.");
                    if (tp >= entry) throw new Error("For SELL, Take Profit must be below Entry.");
                    risk = sl - entry;
                    reward = entry - tp;
                }

                const ratio = reward / risk;

                // Format Logic (handling pips vs price diff)
                // We use raw price diff for ratio, which is unit-agnostic.
                const decimalPlaces = entry < 100 ? 4 : 2; // Rough heuristic for pips display
                const multiplier = entry < 100 ? 10000 : 100;
                
                const riskPips = risk * multiplier;
                const rewardPips = reward * multiplier;

                document.getElementById('rr-result').classList.remove('hidden');
                document.getElementById('rr-ratio').innerText = `1 : ${ratio.toFixed(2)}`;
                document.getElementById('rr-risk').innerText = `${riskPips.toFixed(1)} pips`;
                document.getElementById('rr-reward').innerText = `${rewardPips.toFixed(1)} pips`;

            } catch (error) {
                const errEl = document.getElementById('rr-error');
                errEl.innerText = error.message;
                errEl.classList.remove('hidden');
            }
        }

        // --- Event Listeners ---
        document.addEventListener('DOMContentLoaded', () => {
            // Init
            updatePricingInputs();

            // Listeners
            document.getElementById('ps-pair').addEventListener('change', updatePricingInputs);
            document.getElementById('global-currency').addEventListener('change', updatePricingInputs);
        });
    </script>
@endsection
