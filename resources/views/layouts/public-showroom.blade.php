<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased">
        <div class="pointer-events-none fixed inset-0 overflow-hidden">

        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

            {{ $slot }}
        </div>

        @fluxScripts
    </body>
</html>
