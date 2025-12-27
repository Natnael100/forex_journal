@extends('layouts.app')

@section('title', 'Analyst Requests')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-white mb-2">Analyst Requests</h1>
        <p class="text-slate-400">Review and approve trader requests for performance analysts.</p>
    </div>
</div>

<div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-900 border-b border-slate-700">
                <tr>
                    <th class="px-6 py-4 text-slate-400 font-semibold text-sm">Date</th>
                    <th class="px-6 py-4 text-slate-400 font-semibold text-sm">Trader</th>
                    <th class="px-6 py-4 text-slate-400 font-semibold text-sm w-1/3">Motivation</th>
                    <th class="px-6 py-4 text-slate-400 font-semibold text-sm">Status</th>
                    <th class="px-6 py-4 text-slate-400 font-semibold text-sm">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse($requests as $req)
                    <tr class="hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-300">
                            {{ $req->created_at->format('M d, H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-600 flex items-center justify-center text-xs font-bold text-white">
                                    {{ substr($req->trader->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-white text-sm font-medium">{{ $req->trader->name }}</p>
                                    <p class="text-slate-500 text-xs">{{ $req->trader->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-400 italic">
                            "{{ Str::limit($req->motivation, 80) }}"
                        </td>
                        <td class="px-6 py-4">
                            @if($req->status === 'pending')
                                <span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded text-xs font-bold uppercase">Pending</span>
                            @elseif($req->status === 'approved')
                                <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-bold uppercase">Approved</span>
                            @elseif($req->status === 'consented')
                                <span class="px-2 py-1 bg-purple-500/20 text-purple-400 rounded text-xs font-bold uppercase">Consented</span>
                            @elseif($req->status === 'completed')
                                <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-bold uppercase">Active</span>
                            @else
                                <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-bold uppercase">Rejected</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($req->status === 'pending')
                                    <button 
                                        data-id="{{ $req->id }}"
                                        data-name="{{ $req->trader->name }}"
                                        data-motivation="{{ $req->motivation }}"
                                        onclick="openReviewModal(this)"
                                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded">
                                        Review
                                    </button>
                                </div>
                            @elseif($req->status === 'consented')
                                <div class="flex gap-2">
                                    <button class="px-3 py-1.5 bg-green-600/50 text-white text-xs font-bold rounded cursor-default">
                                        Processing...
                                    </button>
                                </div>
                            @else
                                <span class="text-xs text-slate-500">
                                    Done
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            No requests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-700">
        {{ $requests->links() }}
    </div>
</div>

<!-- Review Modal -->
<div id="review-modal" class="fixed inset-0 bg-slate-900/80 hidden items-center justify-center z-50">
    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6 max-w-lg w-full shadow-2xl">
        <h3 class="text-xl font-bold text-white mb-4">Review Request</h3>
        <p class="text-slate-400 text-sm mb-4">Trader: <span id="modal-trader-name" class="text-white font-semibold"></span></p>
        
        <div class="bg-slate-900/50 p-4 rounded-lg mb-6">
            <p class="text-sm text-slate-300 italic" id="modal-motivation"></p>
        </div>
        
        <form id="review-form" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-300 mb-2">Assign Analyst (Optional)</label>
                <select name="analyst_id" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white">
                    <option value="">-- Let System Decide Later --</option>
                    @foreach($analysts as $analyst)
                        <option value="{{ $analyst->id }}">{{ $analyst->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-300 mb-2">Admin Notes / Rejection Reason</label>
                <textarea name="admin_notes" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('review-modal').classList.add('hidden')" class="px-4 py-2 text-slate-400 hover:text-white">Cancel</button>
                <button type="submit" name="status" value="rejected" class="px-4 py-2 bg-red-600/20 text-red-400 hover:bg-red-600 hover:text-white rounded-lg font-bold transition-colors">Reject</button>
                <button type="submit" name="status" value="approved" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition-colors">Approve Request</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReviewModal(button) {
        const id = button.dataset.id;
        const name = button.dataset.name;
        const motivation = button.dataset.motivation;

        document.getElementById('modal-trader-name').innerText = name;
        document.getElementById('modal-motivation').innerText = motivation;
        document.getElementById('review-form').action = "/admin/assignments/requests/" + id;
        document.getElementById('review-modal').classList.remove('hidden');
        document.getElementById('review-modal').classList.add('flex');
    }
</script>
@endsection
