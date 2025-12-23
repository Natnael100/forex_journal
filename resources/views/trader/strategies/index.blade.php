@extends('layouts.app')

@section('title', 'Playbook')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8 pt-6">
        <div>
            <h1 class="text-3xl font-bold text-white mb-1">Playbook</h1>
            <p class="text-slate-400">Document and track your trading strategies</p>
        </div>
        <button onclick="openStrategyModal()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg shadow-blue-900/20 transition-all hover:scale-105 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Strategy
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50">
            <p class="text-slate-400 text-xs uppercase tracking-wider mb-1">Active Strategies</p>
            <p class="text-2xl font-bold text-white">{{ $activeCount }}</p>
        </div>
        <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50">
            <p class="text-slate-400 text-xs uppercase tracking-wider mb-1">Testing</p>
            <p class="text-2xl font-bold text-white">{{ $testingCount }}</p>
        </div>
        <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50">
            <p class="text-slate-400 text-xs uppercase tracking-wider mb-1">Total Trades</p>
            <p class="text-2xl font-bold text-white">{{ $totalTrades }}</p>
        </div>
        <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50">
            <p class="text-slate-400 text-xs uppercase tracking-wider mb-1">Total Profit</p>
            <p class="text-2xl font-bold {{ $totalProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                {{ $totalProfit >= 0 ? '+' : '' }}${{ number_format($totalProfit, 0) }}
            </p>
        </div>
    </div>

    @if($strategies->isEmpty())
        <!-- Empty State -->
        <div class="bg-slate-800/50 rounded-xl border border-slate-700/50 p-12 text-center">
            <div class="w-16 h-16 bg-slate-700/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">📘</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Build Your Playbook</h3>
            <p class="text-slate-400 mb-6 max-w-sm mx-auto">Create strategies to categorize your trades and track performance metrics.</p>
            <button onclick="openStrategyModal()" class="text-blue-400 hover:text-blue-300 font-medium">Create First Strategy &rarr;</button>
        </div>
    @else
        <!-- Master-Detail Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-300px)]">
            
            <!-- List Column -->
            <div class="lg:col-span-1 bg-slate-800/50 rounded-xl border border-slate-700/50 overflow-hidden flex flex-col">
                <div class="p-4 border-b border-slate-700/50 bg-slate-800/80 backdrop-blur-sm sticky top-0 z-10">
                    <h3 class="font-semibold text-slate-300">Strategies</h3>
                </div>
                <div class="overflow-y-auto flex-1 p-2 space-y-2 custom-scrollbar">
                    @foreach($strategies as $strategy)
                        <a href="?view_strategy={{ $strategy->id }}" 
                           class="block p-3 rounded-lg border transition-all {{ isset($selectedStrategy) && $selectedStrategy->id === $strategy->id ? 'bg-blue-600/10 border-blue-500/50 ring-1 ring-blue-500/20' : 'bg-slate-800 border-slate-700 hover:border-slate-600' }}">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-white truncate pr-2">{{ $strategy->name }}</h4>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $strategy->status->value === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                                    {{ $strategy->status->label() }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-slate-400">
                                <span>{{ $strategy->win_rate }}% WR</span>
                                <span>•</span>
                                <span>{{ $strategy->total_trades }} trades</span>
                                <span>•</span>
                                <span class="{{ $strategy->total_profit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $strategy->total_profit >= 0 ? '+' : '' }}${{ number_format($strategy->total_profit, 0) }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Details Column -->
            <div class="lg:col-span-2 bg-slate-800/50 rounded-xl border border-slate-700/50 overflow-hidden flex flex-col">
                @if($selectedStrategy)
                    <!-- Toolbar -->
                    <div class="p-4 border-b border-slate-700/50 flex justify-between items-center bg-slate-800/80 backdrop-blur-sm">
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-bold text-white">{{ $selectedStrategy->name }}</h2>
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider {{ $selectedStrategy->status->value === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                {{ $selectedStrategy->status->label() }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('trader.strategies.edit', $selectedStrategy) }}" class="p-2 text-slate-400 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors">
                                ✏️ Edit
                            </a>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="overflow-y-auto flex-1 p-6 custom-scrollbar space-y-8">
                        <!-- KPI Cards -->
                        <div class="grid grid-cols-4 gap-4">
                            <div class="bg-slate-900/50 p-4 rounded-lg border border-slate-700/50 text-center">
                                <span class="block text-slate-500 text-xs uppercase mb-1">Win Rate</span>
                                <span class="text-xl font-bold text-white">{{ $selectedStrategy->win_rate }}%</span>
                            </div>
                            <div class="bg-slate-900/50 p-4 rounded-lg border border-slate-700/50 text-center">
                                <span class="block text-slate-500 text-xs uppercase mb-1">Trades</span>
                                <span class="text-xl font-bold text-white">{{ $selectedStrategy->total_trades }}</span>
                            </div>
                            <div class="bg-slate-900/50 p-4 rounded-lg border border-slate-700/50 text-center">
                                <span class="block text-slate-500 text-xs uppercase mb-1">PnL</span>
                                <span class="text-xl font-bold {{ $selectedStrategy->total_profit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                    {{ $selectedStrategy->total_profit >= 0 ? '+' : '' }}${{ number_format($selectedStrategy->total_profit) }}
                                </span>
                            </div>
                            <div class="bg-slate-900/50 p-4 rounded-lg border border-slate-700/50 text-center">
                                <span class="block text-slate-500 text-xs uppercase mb-1">Avg R</span>
                                <span class="text-xl font-bold text-white">{{ number_format($selectedStrategy->avg_r, 1) }}R</span>
                            </div>
                        </div>

                        <!-- Description & Tags -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-3">Description</h3>
                                <div class="text-slate-400 prose prose-invert prose-sm">
                                    {{ $selectedStrategy->description ?: 'No description provided.' }}
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-3">Tags</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($selectedStrategy->tags ?? [] as $tag)
                                        <span class="px-2 py-1 rounded bg-slate-700/50 text-slate-300 text-xs border border-slate-600/50">#{{ $tag }}</span>
                                    @endforeach
                                    @if(empty($selectedStrategy->tags))
                                        <span class="text-slate-500 text-sm italic">No tags</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Rules -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-3">Trading Rules</h3>
                            <div class="space-y-2">
                                @forelse($selectedStrategy->rules ?? [] as $rule)
                                    <div class="flex items-start gap-3 p-3 bg-slate-900/30 rounded-lg border border-slate-800">
                                        <span class="text-blue-500 mt-0.5">✓</span>
                                        <span class="text-slate-300">{{ $rule }}</span>
                                    </div>
                                @empty
                                    <p class="text-slate-500 italic">No rules defined. Edit strategy to add rules.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-slate-500 p-8">
                        <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        <p>Select a strategy from the left to view details</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Create Strategy Modal -->
    <div id="strategyModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdrop"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-slate-900 border border-slate-700 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="modalPanel">
                    
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-slate-700 bg-slate-800/50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white" id="modal-title">New Strategy</h3>
                        <button onclick="closeStrategyModal()" class="text-slate-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('trader.strategies.store') }}" method="POST">
                        @csrf
                        <div class="px-6 py-6 max-h-[calc(100vh-200px)] overflow-y-auto space-y-6 custom-scrollbar">
                            
                            <!-- Name & Status -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Strategy Name *</label>
                                    <input type="text" name="name" required placeholder="e.g. London Breakout" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 placeholder-slate-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Status</label>
                                    <select name="status" class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500">
                                        <option value="active">Active</option>
                                        <option value="testing">Testing</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Description</label>
                                <textarea name="description" rows="3" placeholder="Describe your trading strategy concept..." class="w-full bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 placeholder-slate-600"></textarea>
                            </div>

                            <!-- Tags -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tags</label>
                                <div id="tagsContainer" class="flex flex-wrap gap-2 mb-2 p-2 bg-slate-950/50 border border-slate-700 rounded-lg min-h-[46px]">
                                    <input type="text" id="tagInput" placeholder="Add a tag..." class="bg-transparent border-none text-white text-sm focus:ring-0 p-1 flex-1 min-w-[100px]" onkeydown="handleTagInput(event)">
                                </div>
                                <!-- Hidden inputs for submission -->
                                <div id="hiddenTags"></div> 
                            </div>

                            <!-- Rules -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-sm font-medium text-slate-300">Trading Rules</label>
                                    <span class="text-xs text-slate-500">Add checklist rules</span>
                                </div>
                                
                                <div id="rulesList" class="space-y-2 mb-4">
                                    <!-- Dynamic rules added here -->
                                </div>
                                
                                <div class="flex gap-2">
                                    <input type="text" id="ruleInput" placeholder="Type a rule..." class="flex-1 bg-slate-950/50 border border-slate-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-500 text-sm">
                                    <button type="button" onclick="addRuleFromInput()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-sm font-medium transition-colors">Add</button>
                                </div>

                                <!-- Quick Add Templates -->
                                <div class="mt-4">
                                    <p class="text-xs text-slate-500 mb-2 font-medium uppercase tracking-wider">Quick Add Templates</p>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" onclick="addRule('Only trade during high-volume sessions')" class="px-3 py-1.5 bg-slate-800/50 hover:bg-slate-700 text-slate-400 hover:text-white text-xs rounded-full border border-slate-700 transition-colors text-left">+ High volume sessions</button>
                                        <button type="button" onclick="addRule('Wait for confirmation candle')" class="px-3 py-1.5 bg-slate-800/50 hover:bg-slate-700 text-slate-400 hover:text-white text-xs rounded-full border border-slate-700 transition-colors text-left">+ Confirmation candle</button>
                                        <button type="button" onclick="addRule('Never risk more than 2%')" class="px-3 py-1.5 bg-slate-800/50 hover:bg-slate-700 text-slate-400 hover:text-white text-xs rounded-full border border-slate-700 transition-colors text-left">+ Max 2% risk</button>
                                        <button type="button" onclick="addRule('Min 1:2 Risk/Reward')" class="px-3 py-1.5 bg-slate-800/50 hover:bg-slate-700 text-slate-400 hover:text-white text-xs rounded-full border border-slate-700 transition-colors text-left">+ Min 1:2 RR</button>
                                        <button type="button" onclick="addRule('No trading during major news')" class="px-3 py-1.5 bg-slate-800/50 hover:bg-slate-700 text-slate-400 hover:text-white text-xs rounded-full border border-slate-700 transition-colors text-left">+ No News Trading</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="px-6 py-4 bg-slate-800/50 border-t border-slate-700 flex justify-end gap-3">
                            <button type="button" onclick="closeStrategyModal()" class="px-4 py-2 text-slate-400 hover:text-white transition-colors">Cancel</button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg">Save Strategy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Modal Logic
    const modal = document.getElementById('strategyModal');
    const backdrop = document.getElementById('modalBackdrop');
    const panel = document.getElementById('modalPanel');

    function openStrategyModal() {
        modal.classList.remove('hidden');
        // Simple animation sequence
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
        }, 10);
    }

    function closeStrategyModal() {
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Tags Logic
    const tagsContainer = document.getElementById('tagsContainer');
    const tagInput = document.getElementById('tagInput');
    const hiddenTags = document.getElementById('hiddenTags');
    let tags = [];

    function handleTagInput(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = e.target.value.trim();
            if (val && !tags.includes(val)) {
                addTag(val);
                e.target.value = '';
            }
        }
    }

    function addTag(text) {
        tags.push(text);
        renderTags();
    }

    function removeTag(text) {
        tags = tags.filter(t => t !== text);
        renderTags();
    }

    function renderTags() {
        // Clear current tags (keep input)
        const currentParams = Array.from(tagsContainer.children).filter(c => c.id !== 'tagInput');
        currentParams.forEach(c => c.remove());

        // Render tags
        tags.forEach(tag => {
            const el = document.createElement('span');
            el.className = 'flex items-center gap-1 bg-blue-600/20 text-blue-400 px-2 py-1 rounded text-xs border border-blue-600/30';
            el.innerHTML = `
                #${tag}
                <button type="button" onclick="removeTag('${tag}')" class="hover:text-white ml-1">&times;</button>
            `;
            tagsContainer.insertBefore(el, tagInput);
        });

        // Update hidden inputs
        hiddenTags.innerHTML = '';
        tags.forEach(tag => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tags[]';
            input.value = tag;
            hiddenTags.appendChild(input);
        });
    }

    // Rules Logic
    const rulesList = document.getElementById('rulesList');
    const ruleInput = document.getElementById('ruleInput');
    
    function addRuleFromInput() {
        const val = ruleInput.value.trim();
        if (val) {
            addRule(val);
            ruleInput.value = '';
        }
    }

    function addRule(text) {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-3 bg-slate-950/50 rounded-lg border border-slate-700 group';
        div.innerHTML = `
            <div class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full border border-slate-600 flex items-center justify-center text-xs text-transparent bg-slate-800">✓</span>
                <span class="text-sm text-slate-300">${text}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-500 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <input type="hidden" name="rules[]" value="${text}">
        `;
        rulesList.appendChild(div);
    }
</script>
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(30, 41, 59, 0.5);
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(71, 85, 105, 0.8);
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(100, 116, 139, 1);
    }
</style>
@endsection
