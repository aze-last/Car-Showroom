@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-6 md:p-8',
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-[32px] border border-zinc-200/80 shadow-sm transition-all hover:shadow-md ' . $padding]) }}>
    @if ($title || $subtitle)
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-zinc-100">
            <div>
                @if ($title)
                    <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-widest">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs font-medium text-zinc-400 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            @if (isset($headerAction))
                <div>{{ $headerAction }}</div>
            @endif
        </div>
    @endif
    {{ $slot }}
</div>
