<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    @php
        use App\Models\Setting;
        use App\Models\Category;

        $carsCategory = Category::where('name', 'Cars')->first();
        $motorcyclesCategory = Category::where('name', 'Motorcycle')->first();
    @endphp

    <body class="min-h-screen bg-white text-zinc-900 antialiased font-sans">
        <header class="sticky top-0 z-50 border-b border-zinc-100 bg-white/80 backdrop-blur-md">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-20 items-center justify-between">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group transition-transform hover:scale-105">
                        <x-app-logo-icon class="h-10 w-10 rounded-xl bg-zinc-900 p-2 text-white shadow-xl shadow-zinc-900/20" />
                        <div class="flex flex-col leading-none">
                            <span class="text-sm font-black uppercase tracking-[0.2em] text-zinc-900">{{ Setting::get('shop_name', 'Car Showroom') }}</span>
                            <span class="text-[9px] font-bold uppercase tracking-[0.3em] text-zinc-400 mt-1">Premium Selection</span>
                        </div>
                    </a>

                    <!-- Navigation -->
                    <nav class="hidden md:flex items-center gap-10">
                        <a href="{{ route('home') }}" class="text-[10px] font-black uppercase tracking-[0.3em] {{ request()->routeIs('home') && !request('category') ? 'text-zinc-900' : 'text-zinc-400 hover:text-zinc-900' }} transition-colors">Home</a>
                        
                        @if($carsCategory)
                            <a href="{{ route('home', ['category' => $carsCategory->id]) }}" class="text-[10px] font-black uppercase tracking-[0.3em] {{ request('category') == $carsCategory->id ? 'text-zinc-900' : 'text-zinc-400 hover:text-zinc-900' }} transition-colors">Cars</a>
                        @endif

                        @if($motorcyclesCategory)
                            <a href="{{ route('home', ['category' => $motorcyclesCategory->id]) }}" class="text-[10px] font-black uppercase tracking-[0.3em] {{ request('category') == $motorcyclesCategory->id ? 'text-zinc-900' : 'text-zinc-400 hover:text-zinc-900' }} transition-colors">Motorcycles</a>
                        @endif

                        <a href="{{ route('about') }}" class="text-[10px] font-black uppercase tracking-[0.3em] {{ request()->routeIs('about') ? 'text-zinc-900' : 'text-zinc-400 hover:text-zinc-900' }} transition-colors">About</a>
                    </nav>

                    <!-- Actions (Search Toggle or Login) -->
                    <div class="flex items-center gap-6">
                        <a href="{{ route('login') }}" class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 hover:text-zinc-900 transition-colors">Admin</a>
                        
                        <a href="{{ route('home') }}" class="inline-flex h-11 px-6 items-center justify-center rounded-xl bg-zinc-900 text-[10px] font-black uppercase tracking-widest text-white shadow-xl shadow-zinc-900/20 transition-all hover:-translate-y-0.5 active:translate-y-0">
                            View Inventory
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

            {{ $slot }}
        </div>

        @fluxScripts
    </body>
</html>
