@extends('layouts.app')

@section('title', 'Pending Analyst Applications')

@section('content')
<div class="flex flex-col gap-1 mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-white">Analyst Applications</h1>
    <p class="text-slate-400">Review and verify prospective analysts</p>
</div>

<div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-700 bg-gray-900/50">
                    <th class="p-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Applicant</th>
                    <th class="p-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Location</th>
                    <th class="p-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Experience</th>
                    <th class="p-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Credentials</th>
                    <th class="p-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Submitted</th>
                    <th class="p-4 text-xs font-medium text-gray-400 text-right uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($applications as $application)
                <tr class="hover:bg-gray-700/50 transition-colors">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-500 font-bold">
                                {{ substr($application->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-medium text-white">{{ $application->name }}</div>
                                <div class="text-sm text-gray-400">{{ $application->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="text-sm text-white">{{ $application->country ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">{{ $application->timezone }}</div>
                    </td>
                    <td class="p-4">
                        <div class="text-sm text-white">{{ $application->years_experience }} Years</div>
                        <div class="text-xs text-gray-500">{{ $application->clients_coached }} clients</div>
                    </td>
                    <td class="p-4">
                        <div class="flex flex-wrap gap-1">
                            @if($application->certifications)
                                @foreach($application->certifications as $cert)
                                    @if($cert !== 'None')
                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/20">
                                            {{ $cert }}
                                        </span>
                                    @endif
                                @endforeach
                            @endif
                            @if($application->track_record_url)
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-purple-500/20 text-purple-400 border border-purple-500/20">
                                    Linked
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="text-sm text-gray-300">{{ $application->created_at->diffForHumans() }}</div>
                        <div class="text-xs text-gray-500">{{ $application->created_at->format('M d, Y') }}</div>
                    </td>
                    <td class="p-4 text-right">
                        <a href="{{ route('admin.analyst-applications.show', $application) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                            <span>Review</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-500">
                        No pending applications found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($applications->hasPages())
        <div class="p-4 border-t border-gray-700">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection
