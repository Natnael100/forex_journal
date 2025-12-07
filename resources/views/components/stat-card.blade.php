<div class="bg-gradient-to-br {{ $bgGradient ?? 'from-slate-800/50 to-slate-900/50' }} backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 hover:border-{{ $accentColor ?? 'emerald' }}-500/30 transition-all duration-200">
    <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 bg-{{ $accentColor ?? 'emerald' }}-500/20 rounded-lg flex items-center justify-center">
            <span class="text-2xl">{!! $icon !!}</span>
        </div>
        @if(isset($trend))
            <span class="text-xs px-2 py-1 rounded-full {{ $trend > 0 ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                {{ $trend > 0 ? '+' : '' }}{{ $trend }}%
            </span>
        @endif
    </div>
    <p class="text-3xl font-bold text-white mb-1">{{ $value }}</p>
    <p class="text-sm text-slate-400">{{ $label }}</p>
    @if(isset($subtitle))
        <p class="text-xs text-slate-500 mt-1">{{ $subtitle }}</p>
    @endif
</div>
