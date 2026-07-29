@php
    // Shared inner content for custom error pages.
    // Expects: $code, $title, $message
@endphp
<div class="min-h-[60vh] flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl w-full text-center py-24 animate-showroom-fade-up">
        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-6">Error {{ $code }}</p>
        <h1 class="text-5xl sm:text-6xl font-bold tracking-tighter text-black mb-6">{{ $title }}</h1>
        <p class="text-sm text-zinc-500 leading-relaxed mb-10">{{ $message }}</p>
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center text-[13px] font-bold uppercase tracking-widest bg-black text-white rounded-xl px-8 h-12 hover:bg-zinc-800 transition-all duration-300 shadow-lg shadow-black/10 leading-none">
            Back to the Showroom
        </a>
    </div>
</div>
