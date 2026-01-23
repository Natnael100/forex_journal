@extends('layouts.app')

@section('title', 'Edit Analyst Profile')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Edit Your Analyst Profile</h1>
        <p class="text-slate-400">Update your public profile to attract more clients</p>
    </div>

    <form action="{{ route('analyst.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Profile Photos (New Section) -->
        <section class="bg-slate-800/50 rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-bold text-white mb-4">Profile Photos</h3>
            <div class="grid gap-6 sm:grid-cols-2">
                <!-- Profile Photo -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Profile Photo</label>
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 rounded-full bg-slate-700 flex items-center justify-center overflow-hidden border border-slate-600">
                            @if($analyst->profile_photo)
                                <img src="{{ $analyst->getProfilePhotoUrl() }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-slate-400 text-xs">No Photo</span>
                            @endif
                        </div>
                        <input type="file" name="photo" accept="image/*" class="flex-1 text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                    </div>
                    @error('photo') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Cover Photo -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Cover Photo</label>
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-24 rounded bg-slate-700 flex items-center justify-center overflow-hidden border border-slate-600">
                            @if($analyst->cover_photo)
                                <img src="{{ $analyst->getCoverPhotoUrl() }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-slate-400 text-xs">No Cover</span>
                            @endif
                        </div>
                        <input type="file" name="cover" accept="image/*" class="flex-1 text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                    </div>
                    @error('cover') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <!-- Bio -->
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-bold text-white mb-4">About You</h3>
            
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Bio</label>
                <textarea name="bio" rows="4" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tell traders about your experience and coaching philosophy...">{{ old('bio', $analyst->bio) }}</textarea>
                @error('bio')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Professional Details -->
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-bold text-white mb-4">Professional Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Years of Experience</label>
                    <div class="relative">
                        <input type="number" name="years_experience" value="{{ old('years_experience', $analyst->years_experience) }}" min="0" max="50" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 pl-4">
                    </div>
                    @error('years_experience')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Hourly Rate (ETB)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">ETB</span>
                        <input type="number" name="hourly_rate" value="{{ old('hourly_rate', $analyst->hourly_rate) }}" min="0" step="1" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 pl-14 text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                    </div>
                    @error('hourly_rate')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Specializations (Checkbox Grid) -->
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-bold text-white mb-2">Specializations</h3>
            <p class="text-sm text-slate-400 mb-4">Select all markets and styles you specialize in.</p>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @php
                $options = ['Scalping', 'Day Trading', 'Swing Trading', 'Position Trading', 'Risk Management', 'Psychology Coaching', 'Technical Analysis', 'Fundamental Analysis', 'Order Flow', 'Price Action'];
                $selected = old('specializations', $analyst->specializations ?? []);
                @endphp
                @foreach($options as $option)
                <label class="cursor-pointer relative group">
                    <input type="checkbox" name="specializations[]" value="{{ $option }}" @if(in_array($option, $selected)) checked @endif class="peer sr-only">
                    <div class="px-3 py-2 rounded-lg border border-slate-700 bg-slate-900/50 text-slate-300 text-sm font-medium transition-all peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-500 hover:border-slate-500 text-center">
                        {{ $option }}
                    </div>
                </label>
                @endforeach
            </div>
            @error('specializations')
            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Certifications (Checkbox Grid) -->
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-bold text-white mb-2">Certifications</h3>
            <p class="text-sm text-slate-400 mb-4">Select any professional credentials.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @php
                $certOptions = ['CFA (Chartered Financial Analyst)', 'CMT (Chartered Market Technician)', 'CFTe (Certified Financial Technician)', 'DEI (Licensed Broker)', 'Prop Firm Verified'];
                $selectedCerts = old('certifications', $analyst->certifications ?? []);
                @endphp
                @foreach($certOptions as $cert)
                <label class="cursor-pointer relative group">
                    <input type="checkbox" name="certifications[]" value="{{ $cert }}" @if(in_array($cert, $selectedCerts)) checked @endif class="peer sr-only">
                    <div class="px-3 py-2 rounded-lg border border-slate-700 bg-slate-900/50 text-slate-300 text-sm font-medium transition-all peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-500 hover:border-slate-500 flex items-center gap-2">
                        <svg class="w-4 h-4 opacity-50 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $cert }}
                    </div>
                </label>
                @endforeach
            </div>
            @error('certifications')
            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Offerings (Checkbox Grid) -->
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-bold text-white mb-2">Subscriber Benefits</h3>
            <p class="text-sm text-slate-400 mb-4">What do subscribers get from you?</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @php
                $offerOptions = ['Daily Market Analysis', 'Live Trading Sessions', '1-on-1 Messaging', 'Real-time Trade Alerts', 'Weekly Recap Video', 'Risk Management Plan', 'Psychology Coaching', 'Private Community Access'];
                $selectedOffers = old('offering_details', $analyst->offering_details ?? []);
                @endphp
                @foreach($offerOptions as $offer)
                <label class="cursor-pointer relative group">
                    <input type="checkbox" name="offering_details[]" value="{{ $offer }}" @if(in_array($offer, $selectedOffers)) checked @endif class="peer sr-only">
                    <div class="px-4 py-3 rounded-lg border border-slate-700 bg-slate-900/50 text-slate-300 text-sm transition-all peer-checked:bg-emerald-600/20 peer-checked:text-emerald-400 peer-checked:border-emerald-500 hover:border-slate-500">
                        <div class="font-bold mb-1">{{ $offer }}</div>
                        <div class="text-xs opacity-70">Include this in all subscription tiers?</div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('offering_details')
            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Subscription Plans (Phase 7) - SEPARATE FORM -->
    </form>

    <form action="{{ route('analyst.profile.plans.update') }}" method="POST" class="space-y-6 mt-6">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <h3 class="text-xl font-bold text-white">Subscription Tiers</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach(['basic', 'premium', 'elite'] as $tier)
                    @php
                        $plan = $analyst->getPlan($tier);
                        $features = $plan->features ?? [];
                    @endphp
                    <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700/50 flex flex-col h-full">
                        <div class="mb-4">
                            <h4 class="text-lg font-bold text-white capitalize mb-1">{{ $tier }}</h4>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 font-bold text-sm">ETB</span>
                                <input type="number" name="plans[{{ $tier }}][price]" value="{{ $plan->price ?? '' }}" class="w-24 bg-slate-900 border border-slate-700 rounded px-2 py-1 text-white" placeholder="0" step="1" min="0">
                                <span class="text-slate-400">/mo</span>
                            </div>
                        </div>

                        <div class="space-y-3 flex-1">
                            <p class="text-xs font-bold text-slate-500 uppercase">Features</p>
                            
                            @php
                                $tierFeatures = [
                                    'basic' => ['text_feedback' => 'Text Feedback', 'monthly_review' => 'Monthly Review', 'email_support' => 'Email Support'],
                                    'premium' => ['weekly_checkins' => 'Weekly Check-ins', 'risk_rules' => 'Automated Risk Rules', 'custom_reports' => 'Custom Reports'],
                                    'elite' => ['video_consultations' => 'Video Consultations', 'guided_journaling' => 'Guided Journaling', 'direct_access' => '24/7 Direct Access']
                                ];
                                // Cumulative features logic: Premium includes Basic, Elite includes Premium
                                $displayFeatures = match($tier) {
                                    'basic' => $tierFeatures['basic'],
                                    'premium' => array_merge($tierFeatures['basic'], $tierFeatures['premium']),
                                    'elite' => array_merge($tierFeatures['basic'], $tierFeatures['premium'], $tierFeatures['elite']),
                                };
                            @endphp

                            @foreach($displayFeatures as $key => $label)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="plans[{{ $tier }}][features][]" value="{{ $key }}" @if(in_array($key, $features)) checked @endif class="rounded border-slate-600 text-blue-600 focus:ring-blue-500 bg-slate-900">
                                    <span class="text-sm text-slate-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-slate-700">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="plans[{{ $tier }}][is_active]" value="1" @if($plan->is_active ?? false) checked @endif class="rounded border-slate-600 text-emerald-500 focus:ring-emerald-500 bg-slate-900">
                                <span class="text-sm font-bold text-emerald-400">Active</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition-colors">
                    Save Plans
                </button>
            </div>
        </div>
    </form>

    <!-- Continue with main form for visibility -->
    <form action="{{ route('analyst.profile.update') }}" method="POST" class="space-y-6 mt-6">
        @csrf
        @method('PUT')

        <!-- Visibility -->
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700/50 mt-8">
            <h3 class="text-lg font-bold text-white mb-4">Profile Visibility</h3>
            
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="profile_visibility" value="1" @if(old('profile_visibility', $analyst->profile_visibility) === 'public') checked @endif class="w-5 h-5 rounded border-slate-600 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-900">
                <div>
                    <p class="text-white font-medium">Show my profile in the analyst marketplace</p>
                    <p class="text-sm text-slate-400">Traders will be able to find and subscribe to you</p>
                </div>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold rounded-lg transition-all shadow-lg shadow-blue-900/50">
                Save Changes
            </button>
            <a href="{{ route('analyst.dashboard') }}" class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded-lg transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
