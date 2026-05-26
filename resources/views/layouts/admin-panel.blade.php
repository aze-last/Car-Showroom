<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.head')
        <link href="https://fonts.googleapis.com" rel="preconnect"/>
        <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>      
        <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    </head>
    <body class="min-h-screen bg-gallery-background font-hanken text-zinc-900 antialiased overflow-x-hidden">
        @php
            $currentUser = auth()->user();
            $isAdmin = (bool) ($currentUser?->is_admin ?? false);
            $isStaff = (bool) ($currentUser?->isStaff() ?? false);
            $panelTitle = $isAdmin ? 'Admin Panel' : 'Staff Panel';
            $homeRoute = $isAdmin ? route('admin.dashboard') : route('admin.units.index');
        @endphp

        <!-- Mobile TopNav -->     
        <div class="md:hidden flex justify-between items-center w-full px-8 h-20 bg-white border-b border-gallery-outline/20 fixed top-0 z-50">
            <div class="text-[12px] font-bold uppercase tracking-widest text-black">Admin Suite</div>   
            <button class="p-2 text-black" x-data x-on:click="document.getElementById('admin-mobile-nav').checked = true">
                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
        </div>

        <input id="admin-mobile-nav" type="checkbox" class="peer sr-only" aria-hidden="true">
        <div class="fixed inset-0 z-[60] hidden bg-black/40 backdrop-blur-sm peer-checked:block lg:hidden" x-data x-on:click="document.getElementById('admin-mobile-nav').checked = false"></div>

        <!-- Sidebar Navigation -->
        <aside class="fixed left-0 top-0 h-full w-72 bg-gallery-surface-low border-r border-gallery-outline/10 shadow-sm flex flex-col py-10 px-6 z-[70] transition-transform duration-300 -translate-x-full lg:translate-x-0 peer-checked:translate-x-0">
            <div class="mb-12 px-2">  
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 rounded-full bg-black text-white flex items-center justify-center font-bold text-lg shadow-xl">
                        {{ $currentUser?->initials() }}
                    </div>
                    <div>
                        <h1 class="text-[13px] font-bold text-black uppercase tracking-widest">Admin Suite</h1>
                        <p class="text-[11px] font-medium text-zinc-400">Elite Management</p>    
                    </div>
                </div>

                <a href="{{ route('admin.units.create') }}" class="w-full bg-black text-white font-bold text-[11px] uppercase tracking-widest py-4 rounded-2xl hover:opacity-90 transition-all flex items-center justify-center gap-2 ambient-shadow">        
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                    Add Vehicle
                </a>     
            </div>

            <nav class="flex-grow">
                <ul class="space-y-2">
                    @if ($isAdmin)
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-white text-black ambient-shadow font-bold' : 'text-zinc-400 hover:text-black' }}">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2"><path d="M3 12L12 4L21 12V20A1 1 0 0 1 20 21H4A1 1 0 0 1 3 20V12Z" stroke-linejoin="round"/></svg>
                                <span class="text-[12px] uppercase tracking-widest">Dashboard</span>
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="{{ route('admin.units.index') }}" class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all {{ request()->routeIs('admin.units.*') ? 'bg-white text-black ambient-shadow font-bold' : 'text-zinc-400 hover:text-black' }}">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2"><path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/></svg>
                            <span class="text-[12px] uppercase tracking-widest">Inventory</span>
                        </a>
                    </li>

                    @if ($isAdmin)
                        <li>
                            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-white text-black ambient-shadow font-bold' : 'text-zinc-400 hover:text-black' }}">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2"><path d="M4 7H20M4 12H20M4 17H14" stroke-linecap="round"/></svg>
                                <span class="text-[12px] uppercase tracking-widest">Categories</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.inquiries.index') }}" class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all {{ request()->routeIs('admin.inquiries.*') ? 'bg-white text-black ambient-shadow font-bold' : 'text-zinc-400 hover:text-black' }}">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2"><path d="M21 15V19A2 2 0 0 1 19 21H5A2 2 0 0 1 3 19V15M7 10L12 15L17 10M12 15V3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span class="text-[12px] uppercase tracking-widest">Inquiries</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.auctions.index') }}" class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all {{ request()->routeIs('admin.auctions.*') ? 'bg-white text-black ambient-shadow font-bold' : 'text-zinc-400 hover:text-black' }}">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2"><path d="M12 8V12L15 15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span class="text-[12px] uppercase tracking-widest">Auctions</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all {{ request()->routeIs('admin.employees.*') ? 'bg-white text-black ambient-shadow font-bold' : 'text-zinc-400 hover:text-black' }}">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2"><path d="M17 21V19A4 4 0 0 0 13 15H5A4 4 0 0 0 1 19V21M9 11A4 4 0 1 0 9 3A4 4 0 0 0 9 11ZM23 21V19A4 4 0 0 0 19.33 15.17M16 3.13A4 4 0 0 1 16 11" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span class="text-[12px] uppercase tracking-widest">Employees</span>
                            </a>
                        </li>

                         <li>
                            <a href="{{ route('admin.logs.index') }}" class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all {{ request()->routeIs('admin.logs.*') ? 'bg-white text-black ambient-shadow font-bold' : 'text-zinc-400 hover:text-black' }}">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2"><path d="M6 6H18V18H6V6Z" stroke-linejoin="round"/><path d="M9 10H15M9 14H13" stroke-linecap="round"/></svg>
                                <span class="text-[12px] uppercase tracking-widest">Audit Trail</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>

            <div class="mt-auto pt-8 border-t border-gallery-outline/10 space-y-4">
                <a href="{{ route('admin.settings.shop') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-zinc-400 hover:text-black transition-all">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    <span class="text-[12px] uppercase tracking-widest">Global Settings</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-4 px-4 py-3 rounded-2xl text-zinc-400 hover:text-black transition-all">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                        <span class="text-[12px] uppercase tracking-widest">Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="lg:ml-72 pt-24 lg:pt-12 px-6 lg:px-12 pb-24 min-h-screen">
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>

        @fluxScripts
    </body>
</html>
