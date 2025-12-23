@extends('layouts.app')

@section('title', 'Edit Strategy')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Edit Strategy ✏️</h1>
                <p class="text-slate-400">Refine your strategy definition.</p>
            </div>
            
             <form action="{{ route('trader.strategies.destroy', $strategy) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this strategy? Trades linked to it will remain but won\'t be categorized.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-400 hover:text-red-300 transition-colors text-sm font-medium">
                    Delete Strategy
                </button>
            </form>
        </div>

        <div class="bg-slate-800/50 rounded-xl border border-slate-700/50 p-8 shadow-xl">
            <form action="{{ route('trader.strategies.update', $strategy) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Strategy Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $strategy->name) }}" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-600">
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-300 mb-2">Description / Rules</label>
                    <textarea name="description" id="description" rows="6" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-600">{{ old('description', $strategy->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-700/50">
                    <a href="{{ route('trader.strategies.index') }}" class="px-6 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg shadow-blue-900/20 transition-all hover:scale-[1.02]">
                        Update Strategy
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
