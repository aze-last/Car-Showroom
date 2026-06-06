<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <link href="https://fonts.googleapis.com" rel="preconnect"/>
        <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
        <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Bebas+Neue&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet"/>
        @include('partials.theme-styles')
    </head>
    @php
        use App\Models\Setting;
        use App\Models\Category;
    @endphp

    <body class="min-h-screen bg-gallery-background text-zinc-900 antialiased font-hanken" x-data="{ mobileMenuOpen: false }">
        <header class="fixed top-0 w-full z-50 bg-gallery-surface/90 backdrop-blur-md transition-all duration-300">
            <div class="flex justify-between items-center w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto h-20">
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 transition-transform hover:scale-105">
                        @if($logo = Setting::get('design_logo_path'))
                            <img src="{{ Storage::url($logo) }}" class="h-10 w-auto object-contain" alt="{{ Setting::get('shop_name', 'The Gallery') }}">
                        @else
                            <span class="text-3xl font-bold tracking-tighter text-black">{{ Setting::get('shop_name', 'The Gallery') }}</span>
                        @endif
                    </a>
                </div>

                <nav class="hidden md:flex gap-8 items-center h-full">
                    <a href="{{ route('home') }}" wire:navigate class="text-[12px] font-semibold uppercase tracking-widest {{ request()->routeIs('home') && !request('category') ? 'text-black border-b border-black pb-1' : 'text-zinc-500 hover:text-black pb-1' }} transition-all duration-300">Catalog</a>
                    
                    @if(Setting::get('design_show_auctions', true))
                        <a href="{{ route('auction.hall') }}" wire:navigate class="relative text-[12px] font-semibold uppercase tracking-widest {{ request()->routeIs('auction.*') ? 'text-black border-b border-black pb-1' : 'text-zinc-500 hover:text-black pb-1' }} transition-all duration-300">
                            Auction
                            <livewire:public.auction-nav-badge />
                        </a>
                    @endif

                    @if(Setting::get('design_show_comparison', true))
                        <a href="{{ route('comparison') }}" wire:navigate class="text-[12px] font-semibold uppercase tracking-widest {{ request()->routeIs('comparison') ? 'text-black border-b border-black pb-1' : 'text-zinc-500 hover:text-black pb-1' }} transition-all duration-300">Comparison</a>
                    @endif

                    <a href="{{ route('about') }}" wire:navigate class="text-[12px] font-semibold uppercase tracking-widest {{ request()->routeIs('about') ? 'text-black border-b border-black pb-1' : 'text-zinc-500 hover:text-black pb-1' }} transition-all duration-300">About</a>
                </nav>

                <div class="flex items-center gap-4">
                    @auth
                        <livewire:public.notification-bell />
                        
                        <flux:dropdown align="end" class="h-10">
                            <flux:button variant="ghost" class="h-10 !px-0 rounded-full hover:bg-zinc-50 transition-all select-none">
                                <div class="flex items-center gap-4">
                                    <div class="hidden lg:block text-right">
                                        <p class="text-[10px] font-bold text-black leading-none">{{ auth()->user()->name }}</p>
                                        <p class="text-[8px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Collector</p>
                                    </div>
                                    <div class="h-10 w-10 rounded-full bg-black text-white flex items-center justify-center font-bold text-xs shadow-lg ring-2 ring-white">
                                        {{ auth()->user()->initials() }}
                                    </div>
                                </div>
                            </flux:button>

                            <flux:menu class="min-w-[200px] !p-2 rounded-3xl border-none ambient-shadow shadow-2xl">
                                <div class="px-4 py-3 mb-2 border-b border-zinc-50">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-300">Member Portal</p>
                                </div>
                                
                                <flux:menu.item href="{{ route('garage') }}" wire:navigate icon="home" class="rounded-xl font-bold text-xs py-3 uppercase tracking-widest">
                                    My Garage
                                </flux:menu.item>

                                <flux:menu.item href="{{ route('profile.edit') }}" wire:navigate icon="user" class="rounded-xl font-bold text-xs py-3 uppercase tracking-widest">
                                    Account Settings
                                </flux:menu.item>

                                <flux:separator variant="subtle" class="my-2" />

                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="rounded-xl font-bold text-xs py-3 uppercase tracking-widest text-red-600 hover:bg-red-50">
                                        Sign Out
                                    </flux:menu.item>
                                </form>
                            </flux:menu>
                        </flux:dropdown>
                    @else
                        <div class="hidden md:flex items-center gap-4">
                            <a href="{{ route('register') }}" wire:navigate class="text-[12px] font-bold uppercase tracking-widest text-black hover:opacity-60 transition-opacity mr-4">Start Your Collection</a>
                            <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center justify-center text-[13px] font-bold bg-black text-white rounded-xl px-8 h-12 hover:bg-zinc-800 transition-all duration-300 shadow-lg shadow-black/10 leading-none">Sign In</a>
                        </div>
                    @endauth
                    
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-black hover:bg-zinc-100 rounded-xl transition-colors">
                        <svg x-show="!mobileMenuOpen" viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                        <svg x-show="mobileMenuOpen" viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div 
                x-show="mobileMenuOpen" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="md:hidden bg-white border-b border-zinc-100 overflow-hidden"
            >
                <div class="px-4 pt-2 pb-6 space-y-1">
                    <a href="{{ route('home') }}" wire:navigate class="block px-3 py-4 text-sm font-bold uppercase tracking-widest text-black border-b border-zinc-50">Catalog</a>
                    
                    @if(Setting::get('design_show_auctions', true))
                        <a href="{{ route('auction.hall') }}" wire:navigate class="block px-3 py-4 text-sm font-bold uppercase tracking-widest text-black border-b border-zinc-50 flex justify-between items-center">
                            Auction
                            <livewire:public.auction-nav-badge />
                        </a>
                    @endif

                    @if(Setting::get('design_show_comparison', true))
                        <a href="{{ route('comparison') }}" wire:navigate class="block px-3 py-4 text-sm font-bold uppercase tracking-widest text-black border-b border-zinc-50">Comparison</a>
                    @endif

                    <a href="{{ route('about') }}" wire:navigate class="block px-3 py-4 text-sm font-bold uppercase tracking-widest text-black border-b border-zinc-50">About</a>
                    
                    <div class="pt-6 pb-2">
                        @auth
                            <div class="flex items-center gap-4 px-3 mb-6">
                                <div class="h-10 w-10 rounded-full bg-black text-white flex items-center justify-center font-bold text-xs">{{ auth()->user()->initials() }}</div>
                                <div>
                                    <p class="text-xs font-bold text-black">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-zinc-500 font-medium uppercase tracking-widest mt-1">Collector</p>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <a href="{{ route('garage') }}" wire:navigate class="block px-3 py-4 text-sm font-bold uppercase tracking-widest text-black border-b border-zinc-50">My Garage</a>
                                <a href="{{ route('profile.edit') }}" wire:navigate class="block px-3 py-4 text-sm font-bold uppercase tracking-widest text-black border-b border-zinc-50">Account Settings</a>
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-4 text-sm font-bold uppercase tracking-widest text-red-600">Sign Out</button>
                                </form>
                            </div>
                        @else
                            <div class="grid gap-4 px-3">
                                <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center justify-center w-full py-4 text-sm font-bold uppercase tracking-widest bg-black text-white rounded-xl">Sign In</a>
                                <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center justify-center w-full py-4 text-sm font-bold uppercase tracking-widest text-black border border-zinc-200 rounded-xl">Register</a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <div class="relative pt-20">
            {{ $slot }}
        </div>

        @fluxScripts
    </body>
</html>
