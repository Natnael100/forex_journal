@props(['completion', 'user'])

@if($completion < 100)
    <div class="rounded-xl border border-gray-700 bg-gray-800 text-gray-100 shadow-sm p-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-semibold text-gray-200">Profile Completion</h3>
            <span class="text-sm font-medium text-gray-200">{{ $completion }}%</span>
        </div>
        
        <!-- Progress Bar -->
        <div class="h-2 w-full bg-gray-700 rounded-full overflow-hidden mb-3">
            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $completion }}%"></div>
        </div>

        <!-- Completion Tips -->
        <div class="space-y-2">
            @if(!$user->profile_photo)
                <p class="text-xs text-gray-400 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    Add a profile photo
                </p>
            @endif
            @if(!$user->bio)
                <p class="text-xs text-gray-400 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    Add a bio
                </p>
            @endif
            @if(!$user->cover_photo)
                <p class="text-xs text-gray-400 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    Add a cover photo
                </p>
            @endif
        </div>

        <!-- Complete Profile CTA -->
        <a href="{{ route('profile.edit') }}" 
           class="mt-3 block w-full text-center inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:pointer-events-none disabled:opacity-50 bg-blue-600 text-white hover:bg-blue-500 h-9 px-3">
            Complete Profile
        </a>
    </div>
@endif
