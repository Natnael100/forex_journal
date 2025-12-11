@extends('layouts.app')

@section('title', 'My Feedback')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">My Feedback 💬</h1>
        <p class="text-slate-400">View feedback from your assigned analyst</p>
    </div>

    @if($feedback->count() > 0)
        <div class="space-y-6">
            @foreach($feedback as $item)
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-purple-600/10 to-pink-600/10 border-b border-slate-700/50 p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                    {{ substr($item->analyst->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white mb-1">{{ $item->analyst->name }}</h3>
                                    <div class="flex items-center gap-2 text-sm text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $item->created_at->format('M d, Y') }} • {{ $item->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            
                            @if($item->rating)
                                <div class="flex flex-col items-end">
                                    <div class="flex items-center gap-1 mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= $item->rating ? 'text-yellow-400' : 'text-slate-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-sm font-medium text-slate-400">{{ $item->rating }}/5 Rating</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Feedback Content -->
                    <div class="p-6">
                        <div class="prose prose-invert max-w-none">
                            <div class="bg-gradient-to-br from-blue-500/5 to-purple-500/5 border border-blue-500/20 rounded-lg p-5">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-1">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-slate-200 leading-relaxed whitespace-pre-line text-base">{{ $item->content }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($item->trade_id)
                            <div class="mt-4 flex items-center gap-2 px-4 py-2 bg-emerald-500/10 border border-emerald-500/30 rounded-lg">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                <span class="text-sm font-medium text-emerald-400">Related to specific trade</span>
                            </div>
                        @endif

                        @if($item->isEditable())
                            <div class="mt-4 flex items-center gap-2 px-4 py-2 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-yellow-400">Analyst can still edit this feedback (within 24 hours of submission)</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $feedback->links() }}
        </div>
    @else
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-12 border border-slate-700/50 text-center">
            <div class="text-6xl mb-4">💬</div>
            <h3 class="text-xl font-semibold text-white mb-2">No Feedback Yet</h3>
            <p class="text-slate-400">Your analyst hasn't provided any feedback yet. Keep trading!</p>
        </div>
    @endif
@endsection
