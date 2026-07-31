@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'flex items-center justify-between mb-4']) }}>
    <div>
        <h3 class="text-xs font-bold uppercase tracking-widest text-zinc-400">{{ $title }}</h3>
        @if ($subtitle)
            <p class="text-[11px] font-medium text-zinc-500 mt-0.5">{{ $subtitle }}</p>
        @endif
    </div>
    @if (isset($action))
        <div>{{ $action }}</div>
    @endif
</div>
