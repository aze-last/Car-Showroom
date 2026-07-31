@props([
    'title',
    'subtitle' => null,
    'badge' => null,
    'badgeColor' => 'emerald',
])

<div {{ $attributes->merge(['class' => 'flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8']) }}>
    <div>
        <div class="flex items-center gap-3 mb-1">
            <h1 class="text-3xl font-bold text-zinc-900 tracking-tight">{{ $title }}</h1>
            @if ($badge)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeColor === 'emerald' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($badgeColor === 'red' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-zinc-100 text-zinc-800 border border-zinc-200') }}">
                    {{ $badge }}
                </span>
            @endif
        </div>
        @if ($subtitle)
            <p class="text-xs font-medium text-zinc-400">{{ $subtitle }}</p>
        @endif
    </div>
    @if (isset($actions))
        <div class="flex flex-wrap items-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
