@extends('layouts.app')

@section('title', 'Analyst Agreement')

@section('content')
<div class="max-w-3xl mx-auto py-12">
    <div class="mb-8">
        <a href="{{ route('trader.analyst-request.create') }}" class="text-slate-400 hover:text-white flex items-center gap-2">
            ← Back to Status
        </a>
    </div>

    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden shadow-2xl">
        <!-- Header -->
        <div class="bg-indigo-900/40 p-8 border-b border-indigo-500/30">
            <h1 class="text-2xl font-bold text-white mb-2">Analyst Assignment Agreement</h1>
            <p class="text-indigo-200">Please review the terms of service before confirming your analyst assignment.</p>
        </div>

        <!-- Charter Content -->
        <div class="p-8 space-y-8">
            <div class="flex gap-4">
                <div class="w-12 h-12 bg-blue-500/20 text-blue-400 rounded-full flex items-center justify-center text-2xl shrink-0">
                    👁️
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-2">Scope of Access</h3>
                    <ul class="list-disc list-inside text-slate-300 space-y-1">
                        <li>The Analyst will have <strong>read-only access</strong> to your trade history.</li>
                        <li>The Analyst can view your journal entries and screenshots.</li>
                        <li>The Analyst CANNOT execute trades on your behalf.</li>
                        <li>The Analyst CANNOT withdraw funds or modify account settings.</li>
                    </ul>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="w-12 h-12 bg-green-500/20 text-green-400 rounded-full flex items-center justify-center text-2xl shrink-0">
                    🤝
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-2">Expectations</h3>
                    <ul class="list-disc list-inside text-slate-300 space-y-1">
                        <li>You agree to receive constructive feedback on a weekly basis.</li>
                        <li>You understand that advice is educational, not financial advice.</li>
                        <li>Harassment or abuse will result in immediate termination of the assignment.</li>
                    </ul>
                </div>
            </div>

            <div class="bg-slate-900/50 p-6 rounded-xl border border-slate-700">
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Assignment Details</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="block text-slate-500">Applicant</span>
                        <span class="text-white font-medium">{{ Auth::user()->name }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-500">Proposed Analyst</span>
                        <span class="text-white font-medium">
                            {{ $analystRequest->analyst ? $analystRequest->analyst->name : 'To be assigned by Admin' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Consent Action -->
            <form action="{{ route('trader.analyst-request.process-consent', $analystRequest->id) }}" method="POST" class="mt-8 pt-8 border-t border-slate-700">
                @csrf
                
                <div class="flex items-start gap-3 mb-6">
                    <input type="checkbox" id="consent" name="consent" required class="mt-1 w-5 h-5 rounded border-slate-600 bg-slate-700 text-indigo-600 focus:ring-indigo-500">
                    <label for="consent" class="text-slate-300 text-sm">
                        I, <strong>{{ Auth::user()->name }}</strong>, explicitly grant access to my trading data as described above. I understand I can revoke this access at any time by contacting support.
                    </label>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('trader.analyst-request.create') }}" class="px-6 py-3 text-slate-400 hover:text-white font-medium">
                        Cancel & Decline
                    </a>
                    <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-lg shadow-indigo-600/20 transition-all">
                        Confirm Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
