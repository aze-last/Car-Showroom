<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <link href="https://fonts.googleapis.com" rel="preconnect"/>
        <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
        <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Bebas+Neue&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet"/>
        @include('partials.theme-styles')
        
        @php
            $designLayout = \App\Models\Setting::get('design_layout', 'cinema');
        @endphp

        @if($designLayout === 'bmw_m')
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&family=Saira+Condensed:wght@400;700&display=swap');
                
                body.theme-bmw-m {
                    background-color: #000000 !important;
                    color: #ffffff !important;
                    font-family: 'Inter', sans-serif !important;
                }

                /* Header Overrides */
                .theme-bmw-m header { background-color: rgba(0, 0, 0, 0.95) !important; border-bottom: 1px solid #3c3c3c !important; }
                .theme-bmw-m header a.text-black { color: #ffffff !important; border-bottom-color: #ffffff !important; }
                .theme-bmw-m header a.text-zinc-500 { color: #bbbbbb !important; }
                .theme-bmw-m header a.text-zinc-500:hover { color: #ffffff !important; }
                .theme-bmw-m header span.text-black { color: #ffffff !important; }
                .theme-bmw-m header button svg { stroke: #ffffff !important; }
                .theme-bmw-m header .bg-black { background-color: #ffffff !important; color: #000000 !important; border-radius: 0 !important;}
                
                /* Footer Overrides */
                .theme-bmw-m footer { background-color: #000000 !important; border-top: 1px solid #3c3c3c !important; color: #bbbbbb !important;}
                .theme-bmw-m footer a:hover { color: #ffffff !important; }

                /* Global Component Overrides for other pages (Auction/About/Garage) */
                .theme-bmw-m .bg-white { background-color: #1a1a1a !important; border-color: #3c3c3c !important; color: #ffffff !important;}
                .theme-bmw-m .bg-zinc-50 { background-color: #0d0d0d !important; border-color: #262626 !important; }
                .theme-bmw-m .text-zinc-900, .theme-bmw-m .text-black { color: #ffffff !important; }
                .theme-bmw-m .text-zinc-500, .theme-bmw-m .text-zinc-600 { color: #bbbbbb !important; }
                .theme-bmw-m .border-zinc-100, .theme-bmw-m .border-zinc-200 { border-color: #3c3c3c !important; }
                .theme-bmw-m .shadow-sm, .theme-bmw-m .shadow-md, .theme-bmw-m .shadow-lg, .theme-bmw-m .shadow-xl { box-shadow: none !important; border: 1px solid #262626 !important; }
                .theme-bmw-m .rounded-3xl, .theme-bmw-m .rounded-2xl, .theme-bmw-m .rounded-xl, .theme-bmw-m .rounded-lg { border-radius: 0 !important; }

                /* Typography overrides */
                .theme-bmw-m .font-hanken { font-family: 'Inter', sans-serif !important; }
                .theme-bmw-m h1, .theme-bmw-m h2, .theme-bmw-m h3, .theme-bmw-m h4, .theme-bmw-m h5, .theme-bmw-m h6 { font-family: 'Saira Condensed', sans-serif !important; text-transform: uppercase; }

                /* Top Nav M-Stripe globally */
                .theme-bmw-m::before {
                    content: '';
                    position: fixed;
                    top: 0; left: 0; right: 0; height: 4px; z-index: 9999;
                    background: linear-gradient(90deg, #0066b1 0%, #0066b1 33.33%, #1c69d4 33.33%, #1c69d4 66.66%, #e22718 66.66%, #e22718 100%);
                }
            </style>
        @elseif($designLayout === 'nintendo_2001')
            <style>
                body.theme-nintendo-2001 {
                    background-color: #7a8aba !important;
                    color: #21242e !important;
                    font-family: Arial, sans-serif !important;
                }

                .theme-nintendo-2001 header { 
                    background-color: #21242e !important; 
                    background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 0) !important;
                    background-size: 4px 4px !important;
                    border-bottom: 2px solid #5a5f8c !important; 
                    height: 50px !important;
                }
                .theme-nintendo-2001 header .max-w-7xl { height: 50px !important; }
                .theme-nintendo-2001 header a, .theme-nintendo-2001 header span { color: #e48600 !important; font-weight: 900 !important; text-transform: uppercase !important; font-size: 11px !important; }
                .theme-nintendo-2001 header a:hover { color: #ffffff !important; }
                .theme-nintendo-2001 header .bg-black { background-color: #f68d1f !important; color: #ffffff !important; border-radius: 2px !important; }
                
                .theme-nintendo-2001 footer { background-color: #21242e !important; border-top: 4px solid #3d4f97 !important; }
                
                .theme-nintendo-2001 .bg-white, .theme-nintendo-2001 .bg-zinc-50 { 
                    background-color: #7a8aba !important; 
                    border: 2px solid #3d4f97 !important;
                    box-shadow: inset 2px 2px 0 rgba(255,255,255,0.2) !important;
                }
                
                .theme-nintendo-2001 .text-zinc-900, .theme-nintendo-2001 .text-black { color: #21242e !important; }
                .theme-nintendo-2001 .rounded-3xl, .theme-nintendo-2001 .rounded-2xl, .theme-nintendo-2001 .rounded-xl { border-radius: 4px !important; }
            </style>
        @endif
    </head>

    <body class="min-h-screen antialiased {{ $designLayout === 'bmw_m' ? 'theme-bmw-m font-inter' : ($designLayout === 'nintendo_2001' ? 'theme-nintendo-2001' : 'bg-gallery-background text-zinc-900 font-hanken') }}" x-data="{ mobileMenuOpen: false }">

        <header class="fixed top-0 w-full z-50 transition-all duration-300 {{ $designLayout === 'bmw_m' ? 'bg-black/95 border-b border-[#3c3c3c]' : 'bg-gallery-surface/90 backdrop-blur-md' }}">
            <div class="flex justify-between items-center w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto h-20">
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 transition-transform hover:scale-105">
                        @if($logo = \App\Models\Setting::get('design_logo_path'))
                            <img src="{{ Storage::url($logo) }}" class="h-10 w-auto object-contain" alt="{{ \App\Models\Setting::get('shop_name', 'The Gallery') }}">
                        @else
                            <span class="text-3xl font-bold tracking-tighter text-black">{{ \App\Models\Setting::get('shop_name', 'The Gallery') }}</span>
                        @endif
                    </a>
                </div>

                <nav class="hidden md:flex gap-8 items-center h-full">
                    <a href="{{ route('home') }}" wire:navigate class="text-[12px] font-semibold uppercase tracking-widest {{ request()->routeIs('home') && !request('category') ? 'text-black border-b border-black pb-1' : 'text-zinc-500 hover:text-black pb-1' }} transition-all duration-300">Catalog</a>
                    
                    @if(\App\Models\Setting::get('design_show_auctions', true))
                        <a href="{{ route('auction.hall') }}" wire:navigate class="relative text-[12px] font-semibold uppercase tracking-widest {{ request()->routeIs('auction.*') ? 'text-black border-b border-black pb-1' : 'text-zinc-500 hover:text-black pb-1' }} transition-all duration-300">
                            Auction
                            <livewire:public.auction-nav-badge />
                        </a>
                    @endif

                    @if(\App\Models\Setting::get('design_show_comparison', true))
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
                    
                    @if(\App\Models\Setting::get('design_show_auctions', true))
                        <a href="{{ route('auction.hall') }}" wire:navigate class="block px-3 py-4 text-sm font-bold uppercase tracking-widest text-black border-b border-zinc-50 flex justify-between items-center">
                            Auction
                            <livewire:public.auction-nav-badge />
                        </a>
                    @endif

                    @if(\App\Models\Setting::get('design_show_comparison', true))
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

        <livewire:public.comparison-tray />

        <!-- Global Toast Notification Center -->
        <div 
            x-data="{ 
                toasts: [],
                addToast(message, type = 'success') {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 4000);
                }
            }"
            x-init="
                @if(session()->has('toast'))
                    $nextTick(() => addToast('{{ session('toast')['message'] }}', '{{ session('toast')['type'] }}'));
                @endif
            "
            @toast.window="addToast($event.detail.message, $event.detail.type)"
            class="fixed top-24 right-6 z-[100] flex flex-col gap-3 pointer-events-none w-full max-w-sm"
        >
            <template x-for="toast in toasts" :key="toast.id">
                <div 
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="translate-y-[-20px] opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="translate-y-0 opacity-100"
                    x-transition:leave-end="translate-y-[-20px] opacity-0"
                    class="pointer-events-auto bg-black/95 backdrop-blur-md text-white rounded-2xl px-6 py-4 shadow-[0_20px_40px_-5px_rgba(0,0,0,0.4)] border border-white/10 flex items-center justify-between gap-4"
                >
                    <div class="flex items-center gap-3">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="toast.type === 'info' ? 'bg-zinc-400' : 'bg-emerald-400'"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2" :class="toast.type === 'info' ? 'bg-zinc-500' : 'bg-emerald-500'"></span>
                        </span>
                        <p class="text-[10px] font-bold uppercase tracking-widest leading-normal" x-text="toast.message"></p>
                    </div>
                    <button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-white/45 hover:text-white transition-colors shrink-0">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>

        <footer class="bg-zinc-950 text-zinc-400 py-12 border-t border-zinc-900 mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-xs">&copy; {{ date('Y') }} {{ \App\Models\Setting::get('shop_name', 'The Gallery') }}. All rights reserved.</p>
                <div class="flex flex-wrap gap-6 text-[10px] uppercase tracking-widest font-bold">
                    <a href="{{ route('home') }}" wire:navigate class="hover:text-white transition-colors">Catalog</a>
                    <a href="{{ route('about') }}" wire:navigate class="hover:text-white transition-colors">About</a>
                    <a href="{{ route('privacy') }}" wire:navigate class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="{{ route('terms') }}" wire:navigate class="hover:text-white transition-colors">Terms of Service</a>
                    <a href="/sitemap.xml" target="_blank" class="hover:text-white transition-colors">Sitemap</a>
                </div>
            </div>
        </footer>

        @fluxScripts
        @stack('scripts')
    </body>
</html>
