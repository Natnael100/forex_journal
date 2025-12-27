@extends('layouts.app')

@section('title', 'Feedback Templates')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Feedback Templates 📝</h1>
            <p class="text-slate-400">Manage reusable feedback snippets to save time.</p>
        </div>
        <button onclick="document.getElementById('create_modal').classList.remove('hidden')" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition-colors">
            + New Template
        </button>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <div class="bg-slate-800/50 rounded-xl border border-slate-700/50 p-6 flex flex-col">
                <div class="flex justify-between items-start mb-4">
                    <span class="px-2 py-1 rounded bg-slate-700 text-xs text-slate-300">{{ ucfirst($template->category) }}</span>
                    <form action="{{ route('analyst.templates.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Delete this template?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-slate-500 hover:text-red-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">{{ $template->title }}</h3>
                <p class="text-slate-400 text-sm mb-4 flex-grow">{{ Str::limit($template->content, 100) }}</p>
                <button onclick="editTemplate({{ $template->toJson() }})" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                    Edit Template
                </button>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-slate-800/20 rounded-xl border border-dashed border-slate-700">
                <p class="text-slate-500">No templates created using yet.</p>
            </div>
        @endforelse
    </div>

    <!-- Create Modal -->
    <div id="create_modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('create_modal').classList.add('hidden')"></div>
            
            <div class="inline-block align-bottom bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full relative z-10" onclick="event.stopPropagation()">
                <form action="{{ route('analyst.templates.store') }}" method="POST" class="p-6">
                    @csrf
                    <h3 class="text-lg font-medium text-white mb-4">Create New Template</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Title</label>
                            <input type="text" name="title" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Category</label>
                             <select name="category" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white">
                                <option value="general">General</option>
                                <option value="risk">Risk Management</option>
                                <option value="psychology">Psychology</option>
                                <option value="strategy">Strategy</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Content</label>
                            <textarea name="content" rows="4" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('create_modal').classList.add('hidden')" class="px-4 py-2 text-slate-400 hover:text-white">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit functionality skipped for brevity, user can delete/create for now -->
    <script>
        function editTemplate(template) {
            // Populate and show modal (simplified: just alerting for MVP)
            alert('Mock Edit: ' + template.title);
        }
    </script>
@endsection
