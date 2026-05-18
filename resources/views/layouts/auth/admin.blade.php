<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-[440px] space-y-8">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                        <span class="text-2xl font-bold text-slate-900">CS</span>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Showroom Admin</h1>
                    <p class="mt-2 text-sm text-slate-500">Access the vehicle management portal</p>
                </div>

                <div class="admin-card overflow-hidden">
                    <div class="admin-card-body p-8">
                        {{ $slot }}
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-slate-500 hover:text-slate-900">
                        &larr; Back to public showroom
                    </a>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
