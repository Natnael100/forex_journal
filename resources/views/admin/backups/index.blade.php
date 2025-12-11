@extends('layouts.app')

@section('title', 'Database Backups')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Database Backups 💾</h1>
            <p class="text-slate-400">Manage database backups and restores</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.backups.create') }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Backup Now
                </button>
            </form>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
                ← Back
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        @include('components.stat-card', [
            'icon' => '💾',
            'value' => number_format($stats['total_backups']),
            'label' => 'Total Backups',
            'accentColor' => 'blue'
        ])
        
        @include('components.stat-card', [
            'icon' => '📦',
            'value' => number_format($stats['total_size'] / 1024 / 1024, 2) . ' MB',
            'label' => 'Total Size',
            'accentColor' => 'purple'
        ])
        
        @include('components.stat-card', [
            'icon' => '💿',
            'value' => number_format($stats['db_size'] / 1024 / 1024, 2) . ' MB',
            'label' => 'Database Size',
            'accentColor' => 'green'
        ])
        
        @include('components.stat-card', [
            'icon' => '📅',
            'value' => $stats['latest_backup'] ? \Carbon\Carbon::createFromTimestamp($stats['latest_backup']['date'])->diffForHumans() : 'Never',
            'label' => 'Latest Backup',
            'accentColor' => 'yellow'
        ])
    </div>

    <!-- Backups List -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50 overflow-hidden">
        @if($backups->count() > 0)
            <div class="p-6 border-b border-slate-700">
                <h2 class="text-xl font-semibold text-white">Available Backups</h2>
                <p class="text-sm text-slate-400 mt-1">Click restore to roll back to a previous state</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700 bg-slate-800/50">
                            <th class="text-left py-4 px-4 text-slate-300 font-semibold">Filename</th>
                            <th class="text-center py-4 px-4 text-slate-300 font-semibold">Size</th>
                            <th class="text-center py-4 px-4 text-slate-300 font-semibold">Created</th>
                            <th class="text-right py-4 px-4 text-slate-300 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                            <tr class="border-b border-slate-800 hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-white font-medium">{{ $backup['name'] }}</p>
                                            <p class="text-xs text-slate-400">{{ $backup['path'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center text-slate-300">
                                    {{ number_format($backup['size'] / 1024 / 1024, 2) }} MB
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="text-white">{{ \Carbon\Carbon::createFromTimestamp($backup['date'])->format('M d, Y') }}</div>
                                    <div class="text-xs text-slate-400">{{ \Carbon\Carbon::createFromTimestamp($backup['date'])->format('h:i A') }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Download -->
                                        <a href="{{ route('admin.backups.download', $backup['name']) }}" 
                                           class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Download
                                        </a>

                                        <!-- Restore -->
                                        <button onclick="showRestoreModal('{{ $backup['name'] }}')" 
                                                class="px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Restore
                                        </button>

                                        <!-- Delete -->
                                        <button onclick="showDeleteModal('{{ $backup['name'] }}')" 
                                                class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-16">
                <div class="text-6xl mb-4">💾</div>
                <h3 class="text-xl font-semibold text-white mb-2">No Backups Yet</h3>
                <p class="text-slate-400 mb-6">Create your first backup to protect your data</p>
                <form action="{{ route('admin.backups.create') }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors">
                        Create First Backup
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Restore Modal -->
    <div id="restoreModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4 border border-slate-700">
            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Restore Database
            </h3>
            <p class="text-slate-300 mb-4">
                Are you sure you want to restore from <span id="restoreFileName" class="font-semibold text-yellow-400"></span>?
            </p>
            <div class="bg-yellow-900/20 border border-yellow-700/50 rounded-lg p-4 mb-4">
                <p class="text-sm text-yellow-400">
                    ⚠️ <strong>Warning:</strong> This will replace your current database. A pre-restore backup will be created automatically.
                </p>
            </div>
            
            <form id="restoreForm" method="POST">
                @csrf
                <div class="flex items-center gap-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors">
                        Confirm Restore
                    </button>
                    <button type="button" onclick="closeRestoreModal()" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4 border border-slate-700">
            <h3 class="text-xl font-bold text-white mb-4">Delete Backup</h3>
            <p class="text-slate-300 mb-4">
                Are you sure you want to delete <span id="deleteFileName" class="font-semibold text-red-400"></span>?
            </p>
            <p class="text-sm text-slate-400 mb-4">This action cannot be undone.</p>
            
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex items-center gap-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        Delete Backup
                    </button>
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function showRestoreModal(filename) {
            document.getElementById('restoreFileName').textContent = filename;
            document.getElementById('restoreForm').action = `/admin/backups/${filename}/restore`;
            document.getElementById('restoreModal').classList.remove('hidden');
        }

        function closeRestoreModal() {
            document.getElementById('restoreModal').classList.add('hidden');
        }

        function showDeleteModal(filename) {
            document.getElementById('deleteFileName').textContent = filename;
            document.getElementById('deleteForm').action = `/admin/backups/${filename}`;
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRestoreModal();
                closeDeleteModal();
            }
        });
    </script>
    @endpush
@endsection
