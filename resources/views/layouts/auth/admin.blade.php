<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 font-sans text-zinc-900 antialiased">
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(0,0,0,0.02)_1px,transparent_0)] [background-size:32px_32px] opacity-100"></div>
        </div>

        <div class="relative flex min-h-screen flex-col items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-[420px] space-y-10">
                <div class="flex flex-col items-center justify-center text-center">
                    <a href="{{ route('home') }}" class="mb-8 flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-900 text-sm font-black text-white shadow-2xl transition hover:scale-105">
                        CS
                    </a>
                    <h1 class="text-xs font-black uppercase tracking-[0.3em] text-zinc-900">Showroom Portal</h1>
                    <p class="mt-3 text-sm font-medium text-zinc-400 leading-relaxed">Secure access for authorized personnel only.</p>
                </div>

                <div class="rounded-[32px] border border-zinc-100 bg-white p-8 shadow-2xl shadow-zinc-200/50 sm:p-10">
                    {{ $slot }}
                </div>

                <div class="text-center">
                    <a href="{{ route('home') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 hover:text-zinc-900 transition-colors">
                        &larr; Return to showroom
                    </a>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
