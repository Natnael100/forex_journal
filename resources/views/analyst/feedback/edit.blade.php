@extends('layouts.app')

@section('title', 'Edit Feedback')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Edit Feedback</h1>
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
                    <p class="text-sm text-slate-400">
                        Submitted: {{ $feedback->submitted_at->format('M d, Y h:i A') }}
                        ({{ $feedback->submitted_at->diffForHumans() }})
                    </p>
                </div>
            </div>
        </div>

        <!-- Read-Only View -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-semibold text-white mb-4">Feedback Content (Read-Only)</h3>
            <div class="p-4 bg-white/5 rounded-lg">
                <p class="text-slate-300 whitespace-pre-wrap">{{ $feedback->content }}</p>
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
                        You can edit this feedback until {{ $feedback->submitted_at->addHours(24)->format('M d, Y h:i A') }}
                        <span class="text-yellow-400">({{ $feedback->submitted_at->addHours(24)->diffForHumans() }})</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <form action="{{ route('analyst.feedback.update', $feedback->id) }}" method="POST" class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            @csrf
            @method('PUT')

            <!-- Feedback Content -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-300 mb-2">Feedback Content</label>
                <textarea name="content" rows="15" required
                          class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm">{{ old('content', $feedback->content) }}</textarea>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-xs text-slate-400">Use clear, constructive language</p>
                    <span id="charCount" class="text-xs text-slate-400">0 characters</span>
                </div>
                @error('content')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- AI Suggestions (if available) -->
            @if($feedback->ai_suggestions)
                <div class="mb-6 p-4 bg-blue-900/20 border border-blue-700/50 rounded-lg">
                    <details>
                        <summary class="text-sm font-medium text-blue-400 cursor-pointer">View Original AI Suggestions</summary>
                        <div class="mt-3 text-xs text-slate-300 space-y-2 max-h-60 overflow-y-auto">
                            @php
                                $suggestions = is_string($feedback->ai_suggestions) 
                                    ? json_decode($feedback->ai_suggestions, true) 
                                    : $feedback->ai_suggestions;
                            @endphp
                            
                            @if(isset($suggestions['overall_assessment']))
                                <div class="p-2 bg-white/5 rounded">
                                    <strong>Overall:</strong> {{ $suggestions['overall_assessment'] }}
                                </div>
                            @endif
                            
                            @if(isset($suggestions['suggestions']) && is_array($suggestions['suggestions']))
                                @foreach($suggestions['suggestions'] as $suggestion)
                                    <div class="p-2 bg-white/5 rounded">
                                        • {{ $suggestion['text'] ?? $suggestion }}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </details>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    💾 Save Changes
                </button>
                <a href="{{ route('analyst.dashboard') }}" class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">
                    Cancel
                </a>
                <form action="{{ route('analyst.feedback.destroy', $feedback->id) }}" method="POST" class="ml-auto" onsubmit="return confirm('Delete this feedback permanently?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        🗑️ Delete
                    </button>
                </form>
            </div>
        </form>
    @endif

    @push('scripts')
    <script>
        const textarea = document.querySelector('textarea[name="content"]');
        const charCount = document.getElementById('charCount');
        
        if (textarea && charCount) {
            function updateCount() {
                charCount.textContent = textarea.value.length + ' characters';
            }
            
            textarea.addEventListener('input', updateCount);
            updateCount();
        }
    </script>
    @endpush
@endsection
