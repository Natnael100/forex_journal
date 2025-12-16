@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container mx-auto py-8 px-4 max-w-6xl">
    
    <!-- Success/Error Alerts -->
    @if(session('success'))
        <div class="mb-4 p-4 border border-green-200 bg-green-50 text-green-800 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-green-600"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
            <span class="text-sm font-medium">Profile saved successfully!</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 border border-red-200 bg-red-50 text-red-800 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-red-600"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            <span class="text-sm font-medium">Failed to save profile. Please check the errors below.</span>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="flex gap-4">
        @csrf
        @method('PUT')

        <!-- Sidebar Section (Left) -->
        <div class="lg:col-span-1 space-y-6 flex-1">
            <div class="rounded-xl border border-gray-700 bg-gray-800 text-gray-100 shadow-sm overflow-hidden flex flex-col">
                <div class="p-6">
                    <!-- Profile Overview -->
                    <div class="flex flex-col items-center text-center">
                        <div class="relative h-20 w-20 mb-3">
                            <img src="{{ $user->getProfilePhotoUrl() }}" alt="{{ $user->username }}" class="h-full w-full rounded-full object-cover border border-gray-600" />
                        </div>
                        <h2 class="text-lg font-semibold text-gray-100">{{ $user->username }}</h2>
                        <p class="text-sm text-gray-400">@ {{ $user->username }}</p>
                        @if($user->bio)
                            <p class="text-sm text-gray-400 mt-2 line-clamp-2">
                                {{ $user->bio }}
                            </p>
                        @endif
                        <a href="{{ route('profile.show', $user->username ?? $user->id) }}" class="mt-4 w-full inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300 disabled:pointer-events-none disabled:opacity-50 border border-gray-600 bg-gray-700 text-gray-200 hover:bg-gray-600 hover:text-white h-9 px-3">
                            View Public Profile
                        </a>
                    </div>

                    <!-- Profile Completion -->
                    <div class="mt-6">
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="text-gray-400">Profile Completion</span>
                            <span class="font-medium text-gray-200">{{ $completion }}%</span>
                        </div>
                        <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $completion }}%"></div>
                        </div>
                    </div>

                    <!-- Visibility Settings -->
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-200 mb-3 block">Profile Visibility</label>
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <input type="radio" value="public" id="public" name="profile_visibility" 
                                    class="h-4 w-4 border-gray-500 text-blue-500 focus:ring-blue-500 bg-gray-700"
                                    {{ old('profile_visibility', $user->profile_visibility) == 'public' ? 'checked' : '' }}>
                                <label for="public" class="text-sm text-gray-300 font-normal cursor-pointer">
                                    Public
                                </label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="radio" value="analyst_only" id="analyst" name="profile_visibility"
                                    class="h-4 w-4 border-gray-500 text-blue-500 focus:ring-blue-500 bg-gray-700"
                                    {{ old('profile_visibility', $user->profile_visibility) == 'analyst_only' ? 'checked' : '' }}>
                                <label for="analyst" class="text-sm text-gray-300 font-normal cursor-pointer">
                                    Analysts Only
                                </label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="radio" value="private" id="private" name="profile_visibility"
                                    class="h-4 w-4 border-gray-500 text-blue-500 focus:ring-blue-500 bg-gray-700"
                                    {{ old('profile_visibility', $user->profile_visibility) == 'private' ? 'checked' : '' }}>
                                <label for="private" class="text-sm text-gray-300 font-normal cursor-pointer">
                                    Private
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Edit Form (Right) -->
        <div class="flex-2">
            <div class="rounded-xl border border-gray-700 bg-gray-800 text-gray-100 shadow-sm flex flex-col">
                <div class="flex flex-col space-y-1.5 p-6 pb-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold leading-none tracking-tight text-white">Edit Profile</h3>
                </div>
                <div class="p-6 space-y-8">
                    
                    <!-- Profile Photos -->
                    <section>
                        <h3 class="text-sm font-medium text-gray-200 mb-4">Profile Photos</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="profile-photo" class="text-sm text-gray-400 mb-1.5 block">
                                    Profile Photo
                                </label>
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 text-xs shrink-0 border border-gray-600 overflow-hidden">
                                        @if($user->profile_photo)
                                            <img src="{{ $user->getProfilePhotoUrl() }}" class="h-full w-full object-cover">
                                        @else
                                            80×80
                                        @endif
                                    </div>
                                    <input id="profile-photo" name="photo" type="file" accept="image/*" class="flex h-10 w-full rounded-md border border-gray-600 bg-gray-900 px-3 py-2 text-sm text-gray-200 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                </div>
                            </div>
                            <div>
                                <label for="cover-photo" class="text-sm text-gray-400 mb-1.5 block">
                                    Cover Photo
                                </label>
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-20 rounded bg-gray-700 flex items-center justify-center text-gray-400 text-xs shrink-0 border border-gray-600 overflow-hidden">
                                        @if($user->cover_photo)
                                            <img src="{{ $user->getCoverPhotoUrl() }}" class="h-full w-full object-cover">
                                        @else
                                            1200×400
                                        @endif
                                    </div>
                                    <input id="cover-photo" name="cover" type="file" accept="image/*" class="flex h-10 w-full rounded-md border border-gray-600 bg-gray-900 px-3 py-2 text-sm text-gray-200 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Basic Information -->
                    <section>
                        <h3 class="text-sm font-medium text-gray-200 mb-4">Basic Information</h3>
                        <div class="grid gap-4">
                            <div>
                                <label for="username" class="text-sm text-gray-400 mb-1.5 block">
                                    Username
                                </label>
                                <input id="username" name="username" placeholder="johndoe_trades" value="{{ old('username', $user->username) }}" class="flex h-10 w-full rounded-md border border-gray-600 bg-gray-900 px-3 py-2 text-sm text-gray-200 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label for="bio" class="text-sm text-gray-400 mb-1.5 block">
                                    Bio
                                </label>
                                <textarea
                                    id="bio"
                                    name="bio"
                                    placeholder="Tell us about yourself..."
                                    rows="3"
                                    class="flex min-h-[80px] w-full rounded-md border border-gray-600 bg-gray-900 px-3 py-2 text-sm text-gray-200 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >{{ old('bio', $user->bio) }}</textarea>
                                <p class="text-xs text-gray-500 mt-1 text-right">
                                    {{ strlen($user->bio ?? '') }}/500
                                </p>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="country" class="text-sm text-gray-400 mb-1.5 block">
                                        Country
                                    </label>
                                    <select id="country" name="country" class="flex h-10 w-full items-center justify-between rounded-md border border-gray-600 bg-gray-900 px-3 py-2 text-sm text-gray-200 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="" disabled selected>Select country</option>
                                        @foreach(["United States", "United Kingdom", "Germany", "Japan", "Australia", "Canada", "Singapore"] as $country)
                                            <option value="{{ $country }}" {{ old('country', $user->country) == $country ? 'selected' : '' }}>{{ $country }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="timezone" class="text-sm text-gray-400 mb-1.5 block">
                                        Timezone
                                    </label>
                                    <select id="timezone" name="timezone" class="flex h-10 w-full items-center justify-between rounded-md border border-gray-600 bg-gray-900 px-3 py-2 text-sm text-gray-200 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="" disabled selected>Select timezone</option>
                                        @foreach(["UTC-8 (PST)", "UTC-5 (EST)", "UTC+0 (GMT)", "UTC+1 (CET)", "UTC+8 (SGT)", "UTC+9 (JST)"] as $tz)
                                            <option value="{{ $tz }}" {{ old('timezone', $user->timezone) == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Trading Preferences -->
                    <section>
                        <h3 class="text-sm font-medium text-gray-200 mb-4">Trading Preferences</h3>
                        <div class="grid gap-4">
                            <div class="grid gap-4 sm:grid-cols-3">
                                <div>
                                    <label for="experience_level" class="text-sm text-gray-400 mb-1.5 block">
                                        Experience Level
                                    </label>
                                    <select id="experience_level" name="experience_level" class="flex h-10 w-full items-center justify-between rounded-md border border-gray-600 bg-gray-900 px-3 py-2 text-sm text-gray-200 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="" disabled selected>Select level</option>
                                        @foreach(["Beginner", "Intermediate", "Advanced", "Professional"] as $level)
                                            <option value="{{ strtolower($level) }}" {{ old('experience_level', $user->experience_level) == strtolower($level) ? 'selected' : '' }}>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="trading_style" class="text-sm text-gray-400 mb-1.5 block">
                                        Trading Style
                                    </label>
                                    <select id="trading_style" name="trading_style" class="flex h-10 w-full items-center justify-between rounded-md border border-gray-600 bg-gray-900 px-3 py-2 text-sm text-gray-200 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="" disabled selected>Select style</option>
                                        @foreach(["Day Trading", "Swing Trading", "Scalping", "Position Trading"] as $style)
                                            <option value="{{ $style }}" {{ old('trading_style', $user->trading_style) == $style ? 'selected' : '' }}>{{ $style }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="specialization" class="text-sm text-gray-400 mb-1.5 block">
                                        Specialization
                                    </label>
                                    <select id="specialization" name="specialization" class="flex h-10 w-full items-center justify-between rounded-md border border-gray-600 bg-gray-900 px-3 py-2 text-sm text-gray-200 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="" disabled selected>Select market</option>
                                        @foreach(["Forex", "Stocks", "Crypto", "Commodities", "Indices"] as $spec)
                                            <option value="{{ $spec }}" {{ old('specialization', $user->specialization) == $spec ? 'selected' : '' }}>{{ $spec }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Preferred Sessions -->
                            <div>
                                <label class="text-sm text-gray-400 mb-2 block">Preferred Sessions</label>
                                <div class="flex flex-wrap gap-3">
                                    @foreach(["London", "New York", "Tokyo", "Sydney"] as $session)
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox" id="session-{{ $session }}" name="preferred_sessions[]" value="{{ $session }}"
                                                class="h-4 w-4 rounded border-gray-600 bg-gray-700 text-blue-500 focus:ring-blue-500"
                                                {{ in_array($session, $user->preferred_sessions ?? []) ? 'checked' : '' }}>
                                            <label for="session-{{ $session }}" class="text-sm font-normal text-gray-300 cursor-pointer">
                                                {{ $session }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Favorite Pairs -->
                            <div>
                                <label class="text-sm text-gray-400 mb-2 block">Favorite Pairs</label>
                                <div class="flex flex-wrap gap-3">
                                    @foreach(["EUR/USD", "GBP/USD", "USD/JPY", "AUD/USD", "USD/CAD", "EUR/GBP"] as $pair)
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox" id="pair-{{ $pair }}" name="favorite_pairs[]" value="{{ $pair }}"
                                                class="h-4 w-4 rounded border-gray-600 bg-gray-700 text-blue-500 focus:ring-blue-500"
                                                {{ in_array($pair, $user->favorite_pairs ?? []) ? 'checked' : '' }}>
                                            <label for="pair-{{ $pair }}" class="text-sm font-normal text-gray-300 cursor-pointer">
                                                {{ $pair }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Tags -->
                            <div>
                                <label class="text-sm text-gray-400 mb-2 block">Tags</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(["Technical Analysis", "Fundamental Analysis", "Risk Management", "News Trading", "Algorithmic"] as $tag)
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="profile_tags[]" value="{{ $tag }}" class="hidden peer"
                                                {{ in_array($tag, $user->profile_tags ?? []) ? 'checked' : '' }}>
                                            <span class="px-3 py-1.5 text-sm rounded-md border border-gray-600 bg-gray-900 text-gray-400 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 hover:bg-gray-700 transition-colors">
                                                {{ $tag }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Save Button -->
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-900 disabled:pointer-events-none disabled:opacity-50 bg-blue-600 text-white hover:bg-blue-500 h-10 px-4 py-2">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Fallback for JS-less interactions if needed -->
<script>
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            document.querySelector('form').submit();
        }
    });
</script>
@endsection
