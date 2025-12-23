@extends('layouts.app')

@section('title', 'Create Structured Feedback')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Provide Structured Feedback</h1>
                <p class="text-slate-400">
                    For: <span class="text-white font-semibold">{{ $trader->name }}</span>
                    @if($trade)
                        | Trade: {{ $trade->pair }} - {{ $trade->entry_date->format('M d, Y') }}
                    @endif
                </p>
            </div>
            <a href="{{ route('analyst.trader.profile', $trader->id) }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
                ← Back to Profile
            </a>
        </div>
    </div>

    <form action="{{ route('analyst.feedback.store') }}" method="POST" id="feedback-form">
        @csrf
        <input type="hidden" name="trader_id" value="{{ $trader->id }}">
        @if($trade)
            <input type="hidden" name="trade_id" value="{{ $trade->id }}">
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: The Form -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Confidence Rating -->
                <div class="bg-slate-800/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                    <label class="block text-lg font-semibold text-white mb-4">Confidence Rating (1-10)</label>
                    <div class="flex items-center gap-4">
                        <input type="range" name="confidence_rating" min="1" max="10" value="5" class="w-full h-2 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-blue-500" oninput="document.getElementById('rating-val').innerText = this.value">
                        <span id="rating-val" class="text-2xl font-bold text-blue-400">5</span>
                    </div>
                    <p class="text-sm text-slate-400 mt-2">How confident are you that this feedback addresses the trader's core issues?</p>
                </div>

                <!-- Strengths -->
                <div class="bg-slate-800/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-green-400">💪 Strengths</h2>
                        <button type="button" onclick="addField('strengths-container', 'strengths[]')" class="text-sm px-3 py-1.5 bg-green-500/10 text-green-400 hover:bg-green-500/20 rounded-lg transition-colors border border-green-500/30">
                            + Add Item
                        </button>
                    </div>
                    <div id="strengths-container" class="space-y-3">
                        <div class="flex gap-2">
                            <input type="text" name="strengths[]" class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-green-500" placeholder="e.g. Excellent discipline in waiting for setups" required>
                        </div>
                    </div>
                </div>

                <!-- Weaknesses -->
                <div class="bg-slate-800/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-red-400">⚠️ Weaknesses</h2>
                        <button type="button" onclick="addField('weaknesses-container', 'weaknesses[]')" class="text-sm px-3 py-1.5 bg-red-500/10 text-red-400 hover:bg-red-500/20 rounded-lg transition-colors border border-red-500/30">
                            + Add Item
                        </button>
                    </div>
                    <div id="weaknesses-container" class="space-y-3">
                        <div class="flex gap-2">
                            <input type="text" name="weaknesses[]" class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-red-500" placeholder="e.g. Moving stop loss to breakeven too early" required>
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="bg-slate-800/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-blue-400">💡 Actionable Recommendations</h2>
                        <button type="button" onclick="addField('recommendations-container', 'recommendations[]')" class="text-sm px-3 py-1.5 bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 rounded-lg transition-colors border border-blue-500/30">
                            + Add Item
                        </button>
                    </div>
                    <div id="recommendations-container" class="space-y-3">
                        <div class="flex gap-2">
                            <input type="text" name="recommendations[]" class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-blue-500" placeholder="e.g. Only move SL after price hits 1R" required>
                        </div>
                    </div>
                </div>

                <!-- Overall Summary -->
                <div class="bg-slate-800/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                    <h2 class="text-xl font-semibold text-white mb-4">Overall Summary / Additional Notes</h2>
                    <textarea 
                        name="content" 
                        rows="5"
                        class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Any additional context or summary..."
                        required
                    >{{ old('content') }}</textarea>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20">
                        Submit Structured Feedback
                    </button>
                </div>
            </div>

            <!-- Right Column: AI Suggestions Panel (Helper) -->
            <div class="space-y-6">
                <div class="bg-slate-800/30 backdrop-blur-xl rounded-xl p-6 border border-slate-700/30 sticky top-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                            <span>🤖</span> Analysis Insights
                        </h2>
                        <button type="button" id="generate-btn" onclick="generateDraft()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2 shadow-lg shadow-blue-600/20">
                            <span>✨</span> Generate AI Draft
                        </button>
                    </div>
                    
                    <div class="space-y-4 max-h-[80vh] overflow-y-auto pr-2 custom-scrollbar">
                         <!-- Rule-based AI suggestions -->
                         <!-- Win Rate -->
                        <div class="p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                             <div class="flex justify-between mb-1">
                                <span class="text-slate-400">Win Rate</span>
                                <span class="{{ $aiSuggestions['win_rate_analysis']['severity'] === 'critical' ? 'text-red-400' : 'text-green-400' }} font-bold">{{ $aiSuggestions['win_rate_analysis']['value'] }}</span>
                             </div>
                             <p class="text-xs text-slate-500">{{ $aiSuggestions['win_rate_analysis']['suggestion'] }}</p>
                        </div>

                        <!-- Risk Reward -->
                        <div class="p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                             <div class="flex justify-between mb-1">
                                <span class="text-slate-400">Risk:Reward</span>
                                <span class="text-white font-bold">{{ $aiSuggestions['risk_reward_analysis']['value'] }}</span>
                             </div>
                             <p class="text-xs text-slate-500">{{ $aiSuggestions['risk_reward_analysis']['suggestion'] }}</p>
                        </div>

                         <!-- Behavioral -->
                        @if(count($aiSuggestions['behavioral_pattern_analysis']) > 0)
                            <div class="p-4 bg-red-900/10 rounded-lg border border-red-500/20">
                                <span class="text-red-400 font-bold text-sm mb-2 block">Detection</span>
                                @foreach($aiSuggestions['behavioral_pattern_analysis'] as $pattern)
                                    <p class="text-xs text-slate-300 mb-2">• {{ $pattern['pattern'] }}</p>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-4 p-3 bg-blue-500/10 rounded border border-blue-500/20">
                            <p class="text-xs text-blue-300">Click "Generate AI Draft" to auto-fill the form based on these metrics.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        function addField(containerId, inputName, value = '') {
            const container = document.getElementById(containerId);
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `
                <input type="text" name="${inputName}" value="${value}" class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-blue-500" required>
                <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-500/10 text-red-400 hover:bg-red-500/20 rounded-lg border border-red-500/30">✕</button>
            `;
            container.appendChild(div);
        }

        async function generateDraft() {
            const btn = document.getElementById('generate-btn');
            const originalText = btn.innerHTML;
            
            // Set Loading State
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Generating...`;

            try {
                const response = await fetch("{{ route('analyst.feedback.generate-draft', $trader->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        @if($trade) trade_id: {{ $trade->id }} @endif
                    })
                });

                if (!response.ok) throw new Error('Failed to generate draft');

                const data = await response.json();

                // Clear existing lists
                document.getElementById('strengths-container').innerHTML = '';
                document.getElementById('weaknesses-container').innerHTML = '';
                document.getElementById('recommendations-container').innerHTML = '';

                // Populate arrays
                if (data.strengths) data.strengths.forEach(s => addField('strengths-container', 'strengths[]', s));
                if (data.weaknesses) data.weaknesses.forEach(w => addField('weaknesses-container', 'weaknesses[]', w));
                if (data.recommendations) data.recommendations.forEach(r => addField('recommendations-container', 'recommendations[]', r));

                // Populate scalar fields
                if (data.content) document.querySelector('textarea[name="content"]').value = data.content;
                if (data.confidence_rating) {
                    const slider = document.querySelector('input[name="confidence_rating"]');
                    slider.value = data.confidence_rating;
                    document.getElementById('rating-val').innerText = data.confidence_rating;
                }

                // Source Validation Indicator
                if (data.source) {
                    const btn = document.getElementById('generate-btn');
                    // Create temporary toast/badge next to button
                    const badge = document.createElement('span');
                    badge.className = 'ml-3 text-xs font-medium text-green-400 bg-green-900/30 px-2 py-1 rounded border border-green-500/30 animate-fade-in-up';
                    badge.innerHTML = `✓ Generated by ${data.source}`;
                    btn.parentNode.appendChild(badge);
                    
                    // Remove after 5 seconds
                    setTimeout(() => badge.remove(), 5000);
                }

            } catch (error) {
                console.error('Error:', error);
                alert('Failed to generate draft. Please try again.');
            } finally {
                // Reset Button
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
    </script>
@endsection
