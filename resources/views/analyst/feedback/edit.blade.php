@extends('layouts.app')

@section('title', 'Edit Structured Feedback')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Edit Structured Feedback</h1>
            <p class="text-slate-400">For {{ $feedback->trader->name }}</p>
        </div>
        <a href="{{ route('analyst.dashboard') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
            ← Back
        </a>
    </div>

    @if(!$feedback->isEditable())
        <!-- Locked Message -->
        <div class="mb-6 bg-gradient-to-br from-red-900/20 to-pink-900/20 backdrop-blur-xl rounded-xl p-6 border border-red-700/50">
            <div class="flex items-start gap-4">
                <div class="text-3xl">🔒</div>
                <div>
                    <h3 class="text-lg font-semibold text-red-400 mb-2">Feedback Locked</h3>
                    <p class="text-slate-300 mb-2">This feedback was submitted more than 24 hours ago and can no longer be edited.</p>
                </div>
            </div>
        </div>
    @else
        <!-- Time Remaining Alert -->
        <div class="mb-6 bg-gradient-to-br from-yellow-900/20 to-orange-900/20 backdrop-blur-xl rounded-xl p-4 border border-yellow-700/50">
            <div class="flex items-center gap-3">
                <div class="text-2xl">⏰</div>
                <div>
                    <p class="text-yellow-400 font-medium">Edit Window Remaining</p>
                    <p class="text-sm text-slate-300">
                        You can edit until {{ $feedback->submitted_at->addHours(24)->format('M d, Y h:i A') }}
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('analyst.feedback.update', $feedback->id) }}" method="POST" id="feedback-form">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Confidence Rating -->
                    <div class="bg-slate-800/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                        <label class="block text-lg font-semibold text-white mb-4">Confidence Rating (1-10)</label>
                        <div class="flex items-center gap-4">
                            <input type="range" name="confidence_rating" min="1" max="10" 
                                   value="{{ old('confidence_rating', $feedback->confidence_rating ?? 5) }}" 
                                   class="w-full h-2 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-blue-500" 
                                   oninput="document.getElementById('rating-val').innerText = this.value">
                            <span id="rating-val" class="text-2xl font-bold text-blue-400">{{ old('confidence_rating', $feedback->confidence_rating ?? 5) }}</span>
                        </div>
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
                            @php $strengths = old('strengths', $feedback->strengths ?? []); @endphp
                            @if(is_array($strengths) && count($strengths) > 0)
                                @foreach($strengths as $item)
                                    <div class="flex gap-2">
                                        <input type="text" name="strengths[]" value="{{ $item }}" class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-green-500" required>
                                        <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-500/10 text-red-400 hover:bg-red-500/20 rounded-lg border border-red-500/30">✕</button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex gap-2">
                                    <input type="text" name="strengths[]" class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-green-500" placeholder="e.g. Excellent discipline" required>
                                </div>
                            @endif
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
                            @php $weaknesses = old('weaknesses', $feedback->weaknesses ?? []); @endphp
                            @if(is_array($weaknesses) && count($weaknesses) > 0)
                                @foreach($weaknesses as $item)
                                    <div class="flex gap-2">
                                        <input type="text" name="weaknesses[]" value="{{ $item }}" class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-red-500" required>
                                        <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-500/10 text-red-400 hover:bg-red-500/20 rounded-lg border border-red-500/30">✕</button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex gap-2">
                                    <input type="text" name="weaknesses[]" class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-red-500" placeholder="e.g. Early exit" required>
                                </div>
                            @endif
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
                             @php $recommendations = old('recommendations', $feedback->recommendations ?? []); @endphp
                             @if(is_array($recommendations) && count($recommendations) > 0)
                                @foreach($recommendations as $item)
                                    <div class="flex gap-2">
                                        <input type="text" name="recommendations[]" value="{{ $item }}" class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-blue-500" required>
                                        <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-500/10 text-red-400 hover:bg-red-500/20 rounded-lg border border-red-500/30">✕</button>
                                    </div>
                                @endforeach
                             @else
                                <div class="flex gap-2">
                                    <input type="text" name="recommendations[]" class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-blue-500" placeholder="e.g. Wait for candle close" required>
                                </div>
                             @endif
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="bg-slate-800/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                        <h2 class="text-xl font-semibold text-white mb-4">Overall Summary / Additional Notes</h2>
                        <textarea name="content" rows="5" class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500" required>{{ old('content', $feedback->content) }}</textarea>
                    </div>

                    <div class="flex items-center gap-3 pt-4">
                        <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20">
                            Update Feedback
                        </button>
                    </div>
                </div>

                <!-- Right Column: Sidebar Actions -->
                 <div class="space-y-6">
                    <div class="bg-slate-800/30 backdrop-blur-xl rounded-xl p-6 border border-slate-700/30">
                        <h3 class="text-lg font-semibold text-white mb-4">Danger Zone</h3>
                         <button type="button" onclick="if(confirm('Delete this feedback?')) document.getElementById('delete-form').submit();" class="w-full px-4 py-3 bg-red-600/10 hover:bg-red-600/20 text-red-400 border border-red-600/20 rounded-lg transition-colors">
                            🗑️ Delete Feedback
                        </button>
                    </div>
                 </div>
            </div>
        </form>

        <form id="delete-form" action="{{ route('analyst.feedback.destroy', $feedback->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <script>
            function addField(containerId, inputName) {
                const container = document.getElementById(containerId);
                const div = document.createElement('div');
                div.className = 'flex gap-2';
                div.innerHTML = `
                    <input type="text" name="${inputName}" class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-blue-500" required>
                    <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-500/10 text-red-400 hover:bg-red-500/20 rounded-lg border border-red-500/30">✕</button>
                `;
                container.appendChild(div);
            }
        </script>
    @endif
@endsection
