@props([
    'title',
    'value',
    'change' => null,
    'changeType' => 'positive', // positive | negative | neutral
    'caption' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-[32px] p-6 border border-zinc-200/80 shadow-sm relative overflow-hidden group hover:shadow-md transition-all']) }}>
    @if ($icon)
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
            {{ $icon }}
        </div>
    @endif
    
    <p class="text-[11px] font-bold uppercase tracking-widest text-zinc-400 mb-3">{{ $title }}</p>
    
    <div class="flex items-baseline justify-between gap-2 mb-2">
        <h2 class="text-3xl font-extrabold tracking-tight text-zinc-900">{{ $value }}</h2>
        @if ($change)
            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $changeType === 'positive' ? 'bg-emerald-50 text-emerald-700' : ($changeType === 'negative' ? 'bg-red-50 text-red-700' : 'bg-zinc-100 text-zinc-700') }}">
                {{ $change }}
            </span>
        @endif
    </div>

    @if ($caption)
        <p class="text-[11px] font-medium text-zinc-400">{{ $caption }}</p>
    @endif

    @if (isset($slot) && $slot->isNotEmpty())
        <div class="mt-4 pt-4 border-t border-zinc-100">
            {{ $slot }}
        </div>
    @endif
</div>
