<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <link href="https://fonts.googleapis.com" rel="preconnect"/>
        <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
        <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    </head>
    @php
        use App\Models\Setting;
        use App\Models\Category;

        $carsCategory = Category::where('name', 'Cars')->first();
        $motorcyclesCategory = Category::where('name', 'Motorcycle')->first();
    @endphp

    <body class="min-h-screen bg-gallery-background text-zinc-900 antialiased font-hanken">
        <header class="fixed top-0 w-full z-50 bg-gallery-surface/90 backdrop-blur-md transition-all duration-300">
            <div class="flex justify-between items-center w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto h-20">
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 transition-transform hover:scale-105">
                        <span class="text-3xl font-bold tracking-tighter text-black">{{ Setting::get('shop_name', 'The Gallery') }}</span>
                    </a>
                </div>

                <nav class="hidden md:flex gap-8 items-center h-full">
                    <a href="{{ route('home') }}" class="text-[12px] font-semibold uppercase tracking-widest {{ request()->routeIs('home') && !request('category') ? 'text-black border-b border-black pb-1' : 'text-zinc-500 hover:text-black pb-1' }} transition-all duration-300">Catalog</a>
                    
                    <a href="{{ route('auction.hall') }}" class="relative text-[12px] font-semibold uppercase tracking-widest {{ request()->routeIs('auction.*') ? 'text-black border-b border-black pb-1' : 'text-zinc-500 hover:text-black pb-1' }} transition-all duration-300">
                        Auction
                        <livewire:public.auction-nav-badge />
                    </a>

                    <a href="{{ route('comparison') }}" class="text-[12px] font-semibold uppercase tracking-widest {{ request()->routeIs('comparison') ? 'text-black border-b border-black pb-1' : 'text-zinc-500 hover:text-black pb-1' }} transition-all duration-300">Comparison</a>

                    @if($carsCategory)
                        <a href="{{ route('home', ['category' => $carsCategory->id]) }}" class="text-[12px] font-semibold uppercase tracking-widest {{ request('category') == $carsCategory->id ? 'text-black border-b border-black pb-1' : 'text-zinc-500 hover:text-black pb-1' }} transition-all duration-300">Cars</a>
                    @endif

                    @if($motorcyclesCategory)
                        <a href="{{ route('home', ['category' => $motorcyclesCategory->id]) }}" class="text-[12px] font-semibold uppercase tracking-widest {{ request('category') == $motorcyclesCategory->id ? 'text-black border-b border-black pb-1' : 'text-zinc-500 hover:text-black pb-1' }} transition-all duration-300">Motorcycles</a>
                    @endif

                    <a href="{{ route('about') }}" class="text-[12px] font-semibold uppercase tracking-widest {{ request()->routeIs('about') ? 'text-black border-b border-black pb-1' : 'text-zinc-500 hover:text-black pb-1' }} transition-all duration-300">About</a>
                </nav>

                <div class="flex items-center gap-4">
                    @auth
                        <livewire:public.notification-bell />
                        <a href="{{ route('garage') }}" class="text-[12px] font-semibold uppercase tracking-widest text-zinc-500 hover:text-black transition-colors mr-4">My Garage</a>
                        <div class="h-10 w-10 rounded-full bg-black text-white flex items-center justify-center font-bold text-xs select-none">{{ auth()->user()->initials() }}</div>
                    @else
                        <a href="{{ route('register') }}" class="text-[12px] font-bold uppercase tracking-widest text-black hover:opacity-60 transition-opacity mr-4">Start Your Collection</a>
                        <a href="{{ route('login') }}" class="text-[14px] font-bold bg-black text-white rounded-full px-6 py-2 hover:opacity-90 transition-opacity duration-300">Sign In</a>
                    @endauth
                    <button class="md:hidden p-2 text-black">
                        <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </button>
                </div>
            </div>
        </header>

        <div class="relative pt-20">
            {{ $slot }}
        </div>

        @fluxScripts
    </body>
</html>
