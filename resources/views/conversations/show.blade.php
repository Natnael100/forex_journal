@extends('layouts.app')

@section('content')
<div class="flex flex-col h-[calc(100vh-64px)]">
    <!-- Header -->
    @php
        $otherUser = $conversation->analyst_id === auth()->id() ? $conversation->trader : $conversation->analyst;
    @endphp
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('conversations.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <img class="h-10 w-10 rounded-full" 
                 src="{{ $otherUser->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($otherUser->name) }}" 
                 alt="{{ $otherUser->name }}">
            <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ $otherUser->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $otherUser->roles->first()->name ?? 'User' }}</p>
            </div>
        </div>
    </div>

    <!-- Messages Area -->
    <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900">
        @foreach($conversation->messages as $message)
            <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }} message-item" data-id="{{ $message->id }}">
                <div class="max-w-[70%] rounded-lg px-4 py-2 {{ $message->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow' }}">
                    <p class="text-sm whitespace-pre-wrap">{{ $message->content }}</p>
                    <p class="text-xs mt-1 {{ $message->sender_id === auth()->id() ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400' }}">
                        {{ $message->created_at->format('H:i') }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Input Area -->
    <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4">
        <form id="message-form" class="flex space-x-4">
            @csrf
            <input type="text" name="content" id="message-content" 
                   class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                   placeholder="Type a message..." autocomplete="off">
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Send
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messagesContainer = document.getElementById('messages-container');
        const messageForm = document.getElementById('message-form');
        const contentInput = document.getElementById('message-content');
        const conversationId = {{ $conversation->id }};
        const currentUserId = {{ auth()->id() }};
        let lastMessageId = {{ $conversation->messages->last()?->id ?? 0 }};

        // Scroll to bottom
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        scrollToBottom();

        // Send Message
        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const content = contentInput.value.trim();
            if (!content) return;

            // Optimistic UI update could be here, but let's wait for server for simplicity/consistency
            
            try {
                const response = await fetch(`{{ route('messages.store', $conversation) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ content: content })
                });

                if (response.ok) {
                    contentInput.value = '';
                    const data = await response.json();
                    appendMessage(data.message);
                    lastMessageId = Math.max(lastMessageId, data.message.id);
                    scrollToBottom();
                } else {
                    console.error('Failed to send message');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });

        // Append message function
        function appendMessage(message) {
            const isMe = message.sender_id === currentUserId;
            const div = document.createElement('div');
            div.className = `flex ${isMe ? 'justify-end' : 'justify-start'} message-item`;
            div.dataset.id = message.id;
            
            const time = new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            div.innerHTML = `
                <div class="max-w-[70%] rounded-lg px-4 py-2 ${isMe ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow'}">
                    <p class="text-sm whitespace-pre-wrap">${message.content}</p>
                    <p class="text-xs mt-1 ${isMe ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400'}">
                        ${time}
                    </p>
                </div>
            `;
            
            messagesContainer.appendChild(div);
        }

        // Poll for new messages
        setInterval(async function() {
            try {
                const response = await fetch(`{{ route('messages.poll', $conversation) }}?last_id=${lastMessageId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            if (msg.id > lastMessageId) {
                                appendMessage(msg);
                                lastMessageId = msg.id;
                            }
                        });
                        scrollToBottom();
                    }
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        }, 3000); // Poll every 3 seconds
    });
</script>
@endsection
