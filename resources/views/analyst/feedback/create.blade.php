@extends('layouts.app')

@section('title', 'Create Feedback')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Provide Feedback</h1>
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

    <form action="{{ route('analyst.feedback.store') }}" method="POST">
        @csrf
        <input type="hidden" name="trader_id" value="{{ $trader->id }}">
        @if($trade)
            <input type="hidden" name="trade_id" value="{{ $trade->id }}">
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column: Manual Feedback Form -->
            <div class="space-y-6">
                <!-- Feedback Textarea -->
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                    <h2 class="text-xl font-semibold text-white mb-4">Your Feedback</h2>
                    
                    <textarea 
                        id="feedback-content" 
                        name="content" 
                        rows="15"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Write your detailed feedback here...&#10;&#10;Consider including:&#10;- What they're doing well&#10;- Areas for improvement&#10;- Specific actionable recommendations&#10;- Risk management observations"
                        required
                    >{{ old('content') }}</textarea>
                    
                    @error('content')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                   @enderror

                    <div class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-slate-400">
                            <span id="char-count">0</span> characters
                        </p>
                        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                            Submit Feedback
                        </button>
                    </div>
                </div>

                <!-- Recent Feedback History -->
                @if($recentFeedback->count() > 0)
                    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                        <h3 class="text-lg font-semibold text-white mb-4">Recent Feedback History</h3>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach($recentFeedback->take(5) as $feedback)
                                <div class="p-3 bg-white/5 rounded-lg border border-slate-700">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-slate-300">{{ $feedback->analyst->name }}</span>
                                        <span class="text-xs text-slate-400">{{ $feedback->submitted_at->format('M d, Y') }}</span>
                                    </div>
                                    <p class="text-sm text-slate-400 line-clamp-3">{{ $feedback->content }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: AI Suggestions Panel -->
            <div class="space-y-6">
                <div class="bg-gradient-to-br from-blue-800/20 to-indigo-900/20 backdrop-blur-xl rounded-xl p-6 border border-blue-700/30 sticky top-4">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                            <span>🤖</span> AI-Generated Insights
                        </h2>
                        <button type="button" onclick="insertAISuggestions()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Insert All
                        </button>
                    </div>

                    <div class="space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto pr-2">
                        <!-- Performance Summary -->
                        <div class="p-4 bg-white/5 rounded-lg border border-blue-500/30">
                            <h3 class="font-semibold text-blue-300 mb-2 flex items-center gap-2">
                                📊 Overall Assessment
                            </h3>
                            <p class="text-sm text-slate-300 mb-2">{{ $aiSuggestions['summary']['overall_assessment'] }}</p>
                            @if(count($aiSuggestions['summary']['strengths']) > 0)
                                <p class="text-sm text-green-400 font-medium mt-2">Strengths:</p>
                                <ul class="list-disc list-inside text-sm text-slate-300 space-y-1">
                                    @foreach($aiSuggestions['summary']['strengths'] as $strength)
                                        <li>{{ $strength }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if(count($aiSuggestions['summary']['weaknesses']) > 0)
                                <p class="text-sm text-red-400 font-medium mt-2">Weaknesses:</p>
                                <ul class="list-disc list-inside text-sm text-slate-300 space-y-1">
                                    @foreach($aiSuggestions['summary']['weaknesses'] as $weakness)
                                        <li>{{ $weakness }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <!-- Win Rate Analysis -->
                        <div class="p-4 bg-white/5 rounded-lg border border-{{ $aiSuggestions['win_rate_analysis']['severity'] === 'critical' ? 'red' : ($aiSuggestions['win_rate_analysis']['severity'] === 'warning' ? 'yellow' : 'green') }}-500/30">
                            <h3 class="font-semibold text-blue-300 mb-2 flex items-center gap-2">
                                {{ $aiSuggestions['win_rate_analysis']['icon'] }} {{ $aiSuggestions['win_rate_analysis']['metric'] }}
                            </h3>
                            <p class="text-lg font-bold text-white mb-1">{{ $aiSuggestions['win_rate_analysis']['value'] }}</p>
                            <p class="text-sm text-slate-300">{{ $aiSuggestions['win_rate_analysis']['suggestion'] }}</p>
                        </div>

                        <!-- Risk-Reward Analysis -->
                        <div class="p-4 bg-white/5 rounded-lg border border-{{ $aiSuggestions['risk_reward_analysis']['severity'] === 'critical' ? 'red' : ($aiSuggestions['risk_reward_analysis']['severity'] === 'warning' ? 'yellow' : 'green') }}-500/30">
                            <h3 class="font-semibold text-blue-300 mb-2 flex items-center gap-2">
                                {{ $aiSuggestions['risk_reward_analysis']['icon'] }} {{ $aiSuggestions['risk_reward_analysis']['metric'] }}
                            </h3>
                            <p class="text-lg font-bold text-white mb-1">{{ $aiSuggestions['risk_reward_analysis']['value'] }}</p>
                            <p class="text-sm text-slate-300">{{ $aiSuggestions['risk_reward_analysis']['suggestion'] }}</p>
                        </div>

                        <!-- Drawdown Analysis -->
                        <div class="p-4 bg-white/5 rounded-lg border border-{{ $aiSuggestions['drawdown_analysis']['severity'] === 'critical' ? 'red' : ($aiSuggestions['drawdown_analysis']['severity'] === 'warning' ? 'yellow' : 'green') }}-500/30">
                            <h3 class="font-semibold text-blue-300 mb-2 flex items-center gap-2">
                                {{ $aiSuggestions['drawdown_analysis']['icon'] }} {{ $aiSuggestions['drawdown_analysis']['metric'] }}
                            </h3>
                            <p class="text-lg font-bold text-white mb-1">{{ $aiSuggestions['drawdown_analysis']['value'] }}</p>
                            <p class="text-sm text-slate-300">{{ $aiSuggestions['drawdown_analysis']['suggestion'] }}</p>
                        </div>

                        <!-- Behavioral Patterns -->
                        @if(count($aiSuggestions['behavioral_pattern_analysis']) > 0)
                            <div class="p-4 bg-red-900/20 rounded-lg border border-red-500/30">
                                <h3 class="font-semibold text-red-300 mb-3">⚠️ Behavioral Warnings</h3>
                                @foreach($aiSuggestions['behavioral_pattern_analysis'] as $pattern)
                                    <div class="mb-3 last:mb-0">
                                        <p class="text-sm font-medium text-red-400">
                                            {{ $pattern['pattern'] }}
                                            @if(isset($pattern['occurrences']))
                                                ({{ $pattern['occurrences'] }} occurrences)
                                            @endif
                                        </p>
                                        <p class="text-sm text-slate-300">{{ $pattern['suggestion'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Strategy Analysis -->
                        @if(count($aiSuggestions['strategy_analysis']) > 0)
                            <div class="p-4 bg-white/5 rounded-lg border border-purple-500/30">
                                <h3 class="font-semibold text-purple-300 mb-3">📋 Strategy Performance</h3>
                                @foreach($aiSuggestions['strategy_analysis'] as $strategy)
                                    <div class="mb-3 last:mb-0 p-3 bg-white/5 rounded">
                                        <p class="text-sm font-medium text-white">{{ $strategy['strategy'] }}</p>
                                        <p class="text-xs text-slate-400 mb-1">{{ $strategy['trades'] }} trades | Win Rate: {{ $strategy['win_rate'] }} | P/L: {{ $strategy['profit_loss'] }}</p>
                                        <p class="text-sm text-slate-300">{{ $strategy['suggestion'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Session Analysis -->
                        @if(count($aiSuggestions['session_analysis']) > 0)
                            <div class="p-4 bg-white/5 rounded-lg border border-yellow-500/30">
                                <h3 class="font-semibold text-yellow-300 mb-3">🕐 Session Insights</h3>
                                @foreach($aiSuggestions['session_analysis'] as $session)
                                    <div class="mb-2 last:mb-0">
                                        <p class="text-sm text-slate-300">{{ $session['suggestion'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 p-4 bg-blue-500/10 rounded-lg border border-blue-500/30">
                        <p class="text-sm text-blue-300">
                            <strong>💡 Note:</strong> These are AI-generated suggestions based on rule-based analysis. Use them as guidance and apply your professional judgment when writing final feedback.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        // Character counter
        const textarea = document.getElementById('feedback-content');
        const charCount = document.getElementById('char-count');
        
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Initialize count
        charCount.textContent = textarea.value.length;

        // Insert AI Suggestions
        function insertAISuggestions() {
            const suggestions = {!! json_encode($aiSuggestions) !!};
            
            let feedbackText = `Performance Feedback for ${!! json_encode($trader->name) !!}\n\n`;
            
            feedbackText += `OVERALL ASSESSMENT:\n${suggestions.summary.overall_assessment}\n\n`;
            
            if (suggestions.summary.strengths.length > 0) {
                feedbackText += `STRENGTHS:\n`;
                suggestions.summary.strengths.forEach(s => feedbackText += `- ${s}\n`);
                feedbackText += `\n`;
            }
            
            if (suggestions.summary.weaknesses.length > 0) {
                feedbackText += `AREAS FOR IMPROVEMENT:\n`;
                suggestions.summary.weaknesses.forEach(w => feedbackText += `- ${w}\n`);
                feedbackText += `\n`;
            }
            
            feedbackText += `KEY METRICS:\n`;
            feedbackText += `- ${suggestions.win_rate_analysis.metric}: ${suggestions.win_rate_analysis.value}\n  ${suggestions.win_rate_analysis.suggestion}\n\n`;
            feedbackText += `- ${suggestions.risk_reward_analysis.metric}: ${suggestions.risk_reward_analysis.value}\n  ${suggestions.risk_reward_analysis.suggestion}\n\n`;
            feedbackText += `- ${suggestions.drawdown_analysis.metric}: ${suggestions.drawdown_analysis.value}\n  ${suggestions.drawdown_analysis.suggestion}\n\n`;
            
            if (suggestions.behavioral_pattern_analysis.length > 0) {
                feedbackText += `BEHAVIORAL PATTERNS DETECTED:\n`;
                suggestions.behavioral_pattern_analysis.forEach(p => {
                    feedbackText += `- ${p.pattern}: ${p.suggestion}\n`;
                });
                feedbackText += `\n`;
            }
            
            feedbackText += `RECOMMENDATIONS:\n`;
            feedbackText += `[Add your specific recommendations based on the analysis above]\n\n`;
            
            textarea.value = feedbackText;
            charCount.textContent = feedbackText.length;
            textarea.focus();
        }
    </script>
    @endpush
@endsection
