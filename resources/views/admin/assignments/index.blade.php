@extends('layouts.app')

@section('title', 'Analyst Assignments')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Analyst Assignments</h1>
            <p class="text-slate-400">Manage analyst-trader relationships</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-400 mb-1">Total Assignments</p>
                    <p class="text-3xl font-bold text-white">{{ $stats['total_assignments'] }}</p>
                </div>
                <div class="text-4xl">🔗</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-900/20 to-pink-900/20 backdrop-blur-xl rounded-xl p-6 border border-red-700/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-400 mb-1">Unassigned Traders</p>
                    <p class="text-3xl font-bold text-white">{{ $stats['unassigned_traders'] }}</p>
                </div>
                <div class="text-4xl">⚠️</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-400 mb-1">Total Analysts</p>
                    <p class="text-3xl font-bold text-white">{{ $stats['total_analysts'] }}</p>
                </div>
                <div class="text-4xl">👥</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Unassigned Traders -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50">
                <div class="p-6 border-b border-slate-700/50">
                    <h2 class="text-xl font-bold text-white">Unassigned Traders</h2>
                    <p class="text-sm text-slate-400 mt-1">{{ $unassignedTraders->count() }} trader(s)</p>
                </div>
                <div class="p-6 max-h-96 overflow-y-auto">
                    @forelse($unassignedTraders as $trader)
                        <div class="mb-4 last:mb-0 p-4 bg-white/5 rounded-lg border border-slate-700">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr($trader->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-white">{{ $trader->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $trader->email }}</p>
                                </div>
                            </div>
                            <form action="{{ route('admin.assignments.assign') }}" method="POST">
                                @csrf
                                <input type="hidden" name="trader_id" value="{{ $trader->id }}">
                                <select name="analyst_id" class="w-full px-3 py-2 mb-2 bg-white/10 border border-white/20 rounded text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Select Analyst...</option>
                                    @foreach($analysts as $analyst)
                                        <option value="{{ $analyst->id }}">{{ $analyst->name }} ({{ $analyst->traders_assigned_count }} assigned)</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition-colors">
                                    Assign
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-slate-400">✅ All traders assigned</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Current Assignments -->
        <div class="lg:col-span-2">
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50">
                <div class="p-6 border-b border-slate-700/50">
                    <h2 class="text-xl font-bold text-white">Current Assignments</h2>
                    <p class="text-sm text-slate-400 mt-1">{{ $assignments->count() }} active assignment(s)</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-slate-400 border-b border-slate-700/50">
                                <th class="px-6 py-3 font-medium">Trader</th>
                                <th class="px-6 py-3 font-medium">Analyst</th>
                                <th class="px-6 py-3 font-medium">Assigned By</th>
                                <th class="px-6 py-3 font-medium">Date</th>
                                <th class="px-6 py-3 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @forelse($assignments as $assignment)
                                <tr class="text-slate-300 hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                                {{ substr($assignment->trader->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-white">{{ $assignment->trader->name }}</p>
                                                <p class="text-xs text-slate-400">{{ $assignment->trader->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                                {{ substr($assignment->analyst->name, 0, 1) }}
                                            </div>
                                            <p class="font-medium text-white">{{ $assignment->analyst->name }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">{{ $assignment->assignedBy->name }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $assignment->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <button onclick="showReassignModal({{ $assignment->id }}, '{{ $assignment->trader->name }}')" 
                                                class="px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-medium rounded transition-colors mr-2">
                                            Reassign
                                        </button>
                                        <form action="{{ route('admin.assignments.remove', $assignment->id) }}" method="POST" class="inline" onsubmit="return confirm('Remove this assignment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        No assignments yet
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Reassign Modal -->
    <div id="reassignModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4 border border-slate-700">
            <h3 class="text-xl font-bold text-white mb-4">Reassign Trader</h3>
            <p class="text-slate-300 mb-4">Reassign <span id="reassignTraderName" class="font-semibold"></span> to a new analyst</p>
            
            <form id="reassignForm" method="POST">
                @csrf
                @method('PUT')
                <label class="block text-sm font-medium text-slate-300 mb-2">Select New Analyst:</label>
                <select name="analyst_id" class="w-full px-4 py-2 mb-4 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Choose analyst...</option>
                    @foreach($analysts as $analyst)
                        <option value="{{ $analyst->id }}">{{ $analyst->name }} ({{ $analyst->traders_assigned_count }} assigned)</option>
                    @endforeach
                </select>
                
                <div class="flex items-center gap-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        Reassign
                    </button>
                    <button type="button" onclick="closeReassignModal()" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function showReassignModal(assignmentId, traderName) {
            document.getElementById('reassignTraderName').textContent = traderName;
            document.getElementById('reassignForm').action = `/admin/assignments/${assignmentId}`;
            document.getElementById('reassignModal').classList.remove('hidden');
        }

        function closeReassignModal() {
            document.getElementById('reassignModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeReassignModal();
            }
        });
    </script>
    @endpush
@endsection
