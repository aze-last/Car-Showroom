<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-950 text-zinc-100 antialiased">
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -top-48 left-1/2 h-[520px] w-[980px] -translate-x-1/2 rounded-full bg-amber-500/10 blur-3xl"></div>
            <div class="absolute -bottom-48 left-1/3 h-[520px] w-[880px] -translate-x-1/2 rounded-full bg-zinc-300/10 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.05)_1px,transparent_0)] [background-size:26px_26px] opacity-30"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <header class="mb-8 rounded-[28px] border border-zinc-800 bg-zinc-900/60 p-6 shadow-[0_20px_45px_rgba(0,0,0,0.35)] backdrop-blur-sm showroom-fade-in">
                <div class="inline-flex items-center rounded-full border border-amber-400/20 bg-amber-400/10 px-3 py-1 text-xs font-medium text-amber-200">
                    Premium Vehicle Collection
                </div>
                <a href="{{ route('home') }}" class="mt-4 block text-3xl font-semibold tracking-tight text-zinc-100 sm:text-4xl">
                    Vehicle Showroom<span class="text-amber-400">.</span>
                </a>
                <p class="mt-3 max-w-3xl text-sm leading-relaxed text-zinc-300/80 sm:text-base">
                    Explore curated units with polished browsing, premium presentation, and smooth gallery navigation.
                </p>
            </header>

            {{ $slot }}
        </div>

        @fluxScripts
    </body>
</html>
