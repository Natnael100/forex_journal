@props(['user'])

<div class="rounded-xl border border-gray-700 bg-gray-800 text-gray-100 shadow-sm p-4 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
        <!-- Profile Photo -->
        <img src="{{ $user->getProfilePhotoUrl('small') }}" 
             alt="{{ $user->username }}" 
             class="w-12 h-12 rounded-full object-cover flex-shrink-0 border border-gray-600" />

        <!-- User Info -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <h3 class="font-semibold text-gray-200 truncate">{{ $user->username }}</h3>
                @if($user->is_profile_verified)
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-blue-500 flex-shrink-0"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                @endif
            </div>
            <p class="text-xs text-gray-400 truncate">@ {{ $user->username }}</p>
            @if($user->bio)
                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ Str::limit($user->bio, 80) }}</p>
            @endif
        </div>
    </div>

    <!-- View Profile Link -->
    <a href="{{ route('profile.show', $user->username ?? $user->id) }}" 
       class="mt-3 block w-full text-center inline-flex items-center justify-center whitespace-nowrap rounded-md text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:pointer-events-none disabled:opacity-50 border border-gray-600 bg-gray-700 text-gray-200 hover:bg-gray-600 hover:text-white h-8 px-3">
        View Profile
    </a>
</div>
