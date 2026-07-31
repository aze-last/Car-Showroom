@props([
    'status',
    'type' => 'available', // available | sold | pending | approved | rejected | blocked
    'size' => 'md',
])

@php
    $typeClasses = match($type) {
        'available', 'active', 'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'sold', 'closed', 'resolved' => 'bg-zinc-100 text-zinc-900 border-zinc-200',
        'pending', 'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
        'rejected', 'blocked', 'trashed', 'critical' => 'bg-red-50 text-red-700 border-red-200',
        default => 'bg-zinc-100 text-zinc-700 border-zinc-200',
    };

    $dotClasses = match($type) {
        'available', 'active', 'approved' => 'bg-emerald-500',
        'sold', 'closed', 'resolved' => 'bg-zinc-900',
        'pending', 'warning' => 'bg-amber-500',
        'rejected', 'blocked', 'trashed', 'critical' => 'bg-red-500',
        default => 'bg-zinc-400',
    };

    $sizeClasses = match($size) {
        'sm' => 'px-2 py-0.5 text-[9px]',
        'lg' => 'px-4 py-1.5 text-xs',
        default => 'px-3 py-1 text-[10px]',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 font-bold uppercase tracking-wider rounded-full border ' . $typeClasses . ' ' . $sizeClasses]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dotClasses }}"></span>
    <span>{{ $status }}</span>
</span>
