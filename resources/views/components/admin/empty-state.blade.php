@props([
    'title' => 'No Data Available',
    'description' => 'There are no records matching your request.',
    'icon' => 'inbox',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-3xl border border-dashed border-zinc-200 bg-zinc-50/50 p-12 text-center']) }}>
    <div class="h-12 w-12 rounded-full bg-zinc-100 flex items-center justify-center text-zinc-400 mb-4 shadow-inner">
        <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2">
            @if ($icon === 'search')
                <circle cx="11" cy="11" r="7"/><path d="M20 20L16.65 16.65"/>
            @elseif ($icon === 'user')
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            @else
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            @endif
        </svg>
    </div>
    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-widest mb-1">{{ $title }}</h3>
    <p class="text-xs text-zinc-400 max-w-sm mb-6">{{ $description }}</p>
    @if (isset($action))
        <div>{{ $action }}</div>
    @endif
</div>
