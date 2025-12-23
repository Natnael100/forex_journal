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
                                <img src="{{ $item->analyst->getProfilePhotoUrl('medium') }}" 
                                     alt="{{ $item->analyst->name }}" 
                                     class="w-14 h-14 rounded-full border-2 border-purple-500/30 shadow-lg" />
                                <div>
                                    <h3 class="text-xl font-bold text-white mb-1">{{ $item->analyst->name }}</h3>
                                    <div class="flex items-center gap-2 text-sm">
                                        <a href="{{ $item->analyst->getProfileUrl() }}" class="text-purple-400 hover:text-purple-300 transition">
                                            @{{ $item->analyst->username }}
                                        </a>
                                        @if($item->analyst->specialization)
                                            <span class="text-slate-400">• {{ $item->analyst->specialization }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-slate-400 mt-1">
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
                    <!-- Feedback Content -->
                    <div class="p-6 space-y-6">
                        
                        <!-- Confidence Rating -->
                        @if($item->confidence_rating)
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Confidence Rating:</span>
                                <div class="flex items-center gap-1 bg-slate-800/50 rounded-full px-3 py-1 border border-slate-700">
                                    <div class="flex gap-0.5">
                                        @for($i = 1; $i <= 10; $i++)
                                            <div class="w-1.5 h-3 rounded-full {{ $i <= $item->confidence_rating ? 'bg-blue-500' : 'bg-slate-700' }}"></div>
                                        @endfor
                                    </div>
                                    <span class="text-sm font-bold text-white ml-2">{{ $item->confidence_rating }}/10</span>
                                </div>
                            </div>
                        @endif

                        <!-- Main Content / Summary -->
                        @if($item->content)
                            <div class="bg-gradient-to-br from-blue-500/5 to-purple-500/5 border border-blue-500/20 rounded-lg p-5">
                                <h4 class="text-sm font-semibold text-blue-400 mb-2 uppercase tracking-wide">Summary</h4>
                                <p class="text-slate-200 leading-relaxed whitespace-pre-line text-base">{{ $item->content }}</p>
                            </div>
                        @endif

                        <!-- Structured Data Grid -->
                        @if(!empty($item->strengths) || !empty($item->weaknesses))
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Strengths -->
                                @if(!empty($item->strengths))
                                    <div class="bg-emerald-500/5 border border-emerald-500/20 rounded-lg p-5">
                                        <div class="flex items-center gap-2 mb-3">
                                            <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                            </div>
                                            <h4 class="font-semibold text-emerald-400">Strengths</h4>
                                        </div>
                                        <ul class="space-y-2">
                                            @foreach($item->strengths as $strength)
                                                <li class="flex items-start gap-2 text-slate-300 text-sm">
                                                    <svg class="w-5 h-5 text-emerald-500/50 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <span>{{ $strength }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Weaknesses -->
                                @if(!empty($item->weaknesses))
                                    <div class="bg-red-500/5 border border-red-500/20 rounded-lg p-5">
                                        <div class="flex items-center gap-2 mb-3">
                                            <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                </svg>
                                            </div>
                                            <h4 class="font-semibold text-red-400">Areas for Improvement</h4>
                                        </div>
                                        <ul class="space-y-2">
                                            @foreach($item->weaknesses as $weakness)
                                                <li class="flex items-start gap-2 text-slate-300 text-sm">
                                                    <svg class="w-5 h-5 text-red-500/50 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    <span>{{ $weakness }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Recommendations -->
                        @if(!empty($item->recommendations))
                            <div class="bg-blue-500/5 border border-blue-500/20 rounded-lg p-5">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-blue-400">Recommendations</h4>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($item->recommendations as $rec)
                                        <div class="flex items-start gap-3 bg-slate-800/50 rounded-lg p-3 border border-slate-700/50">
                                            <span class="text-blue-400 font-bold text-lg select-none opacity-50">{{ $loop->iteration }}.</span>
                                            <p class="text-slate-300 text-sm leading-relaxed">{{ $rec }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
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
