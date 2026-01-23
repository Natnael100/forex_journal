@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Messages</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($conversations as $conversation)
                @php
                    $otherUser = $conversation->analyst_id === auth()->id() ? $conversation->trader : $conversation->analyst;
                    $lastMessage = $conversation->messages->first();
                    $unreadCount = $conversation->messages()->where('sender_id', '!=', auth()->id())->where('is_read', false)->count();
                @endphp
                <a href="{{ route('conversations.show', $conversation) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <img class="h-10 w-10 rounded-full" 
                                     src="{{ $otherUser->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($otherUser->name) }}" 
                                     alt="{{ $otherUser->name }}">
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $otherUser->name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    {{ $lastMessage ?Str::limit($lastMessage->content, 50) : 'No messages yet' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end space-y-1">
                            @if($lastMessage)
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $lastMessage->created_at->diffForHumans() }}
                                </p>
                            @endif
                            @if($unreadCount > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    No conversations yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
