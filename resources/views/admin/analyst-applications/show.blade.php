@extends('layouts.app')

@section('title', 'Review Application: ' . $application->name)

@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.analyst-applications.index') }}" class="p-2 text-gray-400 hover:text-white bg-gray-800 rounded-lg transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>
    <div class="flex-1">
        <h1 class="text-2xl font-bold text-white">{{ $application->name }}</h1>
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <span>{{ $application->email }}</span>
            <span>•</span>
            <span>Applied {{ $application->created_at->diffForHumans() }}</span>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <!-- Reject Button -->
        <button onclick="document.getElementById('rejectModal').showModal()" class="px-4 py-2 text-sm font-medium text-red-400 bg-red-400/10 hover:bg-red-400/20 border border-red-400/20 rounded-lg transition-colors">
            Reject Application
        </button>
        
        <!-- Approve Button -->
        <form action="{{ route('admin.analyst-applications.approve', $application) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this analyst? This will create a user account and send an email.')">
            @csrf
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-lg hover:shadow-emerald-500/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Approve & Register
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Application Statements -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span>📝</span> Application Statements
            </h3>
            
            <div class="space-y-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-400 mb-2 uppercase tracking-wide">Why they want to join</h4>
                    <div class="p-4 bg-gray-900/50 rounded-lg text-gray-300 leading-relaxed border border-gray-700/50">
                        {{ $application->why_join }}
                    </div>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-400 mb-2 uppercase tracking-wide">Unique Value Proposition</h4>
                    <div class="p-4 bg-gray-900/50 rounded-lg text-gray-300 leading-relaxed border border-gray-700/50">
                        {{ $application->unique_value }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Experience & Methodology -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span>📊</span> Trading Experience
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-400 mb-3">Methodology</h4>
                    @if($application->methodology)
                        <div class="flex flex-wrap gap-2">
                            @foreach($application->methodology as $item)
                                <span class="px-3 py-1 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-full text-sm">
                                    {{ $item }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">None specified</p>
                    @endif
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-400 mb-3">Asset Specialization</h4>
                    @if($application->specializations)
                        <div class="flex flex-wrap gap-2">
                            @foreach($application->specializations as $item)
                                <span class="px-3 py-1 bg-purple-500/10 text-purple-400 border border-purple-500/20 rounded-full text-sm">
                                    {{ $item }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">None specified</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Certificates -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span>📎</span> Uploaded Documents
            </h3>
            
            @if($application->certificate_files)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($application->certificate_files as $index => $file)
                        <div class="flex items-center p-3 bg-gray-900/50 rounded-lg border border-gray-700 hover:border-gray-600 transition-colors">
                            <div class="w-10 h-10 rounded bg-gray-800 flex items-center justify-center mr-3 text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-white truncate">Certificate File {{ $index + 1 }}</p>
                                <p class="text-xs text-gray-500">Document</p>
                            </div>
                            <a href="{{ Storage::url($file) }}" target="_blank" class="p-2 text-gray-400 hover:text-emerald-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-gray-500 bg-gray-900/30 rounded-lg border border-dashed border-gray-700">
                    No documents uploaded
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-6">
        <!-- Quick Stats -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wide mb-4">Quick Stats</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-700/50">
                    <span class="text-gray-400">Trading Exp</span>
                    <span class="font-medium text-white">{{ $application->years_experience }} Years</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-700/50">
                    <span class="text-gray-400">Coaching Exp</span>
                    <span class="font-medium text-white">{{ $application->coaching_experience }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-700/50">
                    <span class="text-gray-400">Clients Coached</span>
                    <span class="font-medium text-white">{{ $application->clients_coached }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-700/50">
                    <span class="text-gray-400">Capacity</span>
                    <span class="font-medium text-white">{{ $application->max_clients }} Clients</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-400">Location</span>
                    <span class="font-medium text-white text-right">{{ $application->country }}<br><span class="text-xs text-gray-500">{{ $application->timezone }}</span></span>
                </div>
            </div>
        </div>

        <!-- Links -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wide mb-4">Verification Links</h3>
            
            <div class="space-y-3">
                @if($application->track_record_url)
                    <a href="{{ $application->track_record_url }}" target="_blank" class="flex items-center gap-3 p-3 bg-gray-900/50 hover:bg-gray-900 border border-gray-700 rounded-lg transition-colors group">
                        <span class="text-2xl group-hover:scale-110 transition-transform">📈</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">Track Record</p>
                            <p class="text-xs text-gray-500 truncate">{{ $application->track_record_url }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                @endif

                @if($application->linkedin_url)
                    <a href="{{ $application->linkedin_url }}" target="_blank" class="flex items-center gap-3 p-3 bg-gray-900/50 hover:bg-gray-900 border border-gray-700 rounded-lg transition-colors group">
                        <span class="text-2xl group-hover:scale-110 transition-transform">🔗</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">LinkedIn</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                @endif
                
                @if(!$application->track_record_url && !$application->linkedin_url)
                    <p class="text-sm text-gray-500 italic">No external links provided</p>
                @endif
            </div>
        </div>
        
        <!-- Certifications List -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wide mb-4">Claimed Credentials</h3>
            @if($application->certifications)
                <ul class="space-y-2">
                    @foreach($application->certifications as $cert)
                        <li class="flex items-center gap-2 text-white">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $cert }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500 italic">None selected</p>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<dialog id="rejectModal" class="bg-transparent p-0 w-full max-w-md backdrop:bg-gray-900/80">
    <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-2xl p-6">
        <h3 class="text-xl font-bold text-white mb-4">Reject Application</h3>
        <p class="text-gray-400 mb-6">
            Please provide a reason for rejecting this application. This will be emailed to the applicant.
        </p>
        
        <form action="{{ route('admin.analyst-applications.reject', $application) }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="rejection_reason" class="block text-sm font-medium text-gray-400 mb-2">Reason for Rejection</label>
                <textarea 
                    name="rejection_reason" 
                    id="rejection_reason" 
                    rows="4" 
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg text-white p-3 focus:outline-none focus:border-red-500"
                    placeholder="e.g. Insufficient experience, invalid credentials..."
                    required
                    minlength="10"
                ></textarea>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').close()" class="px-4 py-2 text-gray-400 hover:text-white transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</dialog>
@endsection
