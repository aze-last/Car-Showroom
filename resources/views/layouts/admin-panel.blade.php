<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 font-sans text-zinc-900 antialiased">
        <a
            href="#admin-main"
            class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[100] focus:rounded-md focus:bg-white focus:px-3 focus:py-2 focus:text-sm focus:shadow"
        >
            Skip to main content
        </a>

        @php
            $currentUser = auth()->user();
            $isAdmin = (bool) ($currentUser?->is_admin ?? false);
            $isStaff = (bool) ($currentUser?->isStaff() ?? false);
            $panelTitle = $isAdmin ? 'Admin Panel' : 'Staff Panel';
            $homeRoute = $isAdmin ? route('admin.dashboard') : route('admin.units.index');
        @endphp

        <div id="admin-shell" class="min-h-screen" data-collapsed="false">
            <input id="admin-mobile-nav" type="checkbox" class="peer sr-only" aria-hidden="true">

            <div class="fixed inset-0 z-30 hidden bg-zinc-900/40 peer-checked:block lg:hidden">
                <label for="admin-mobile-nav" class="block h-full w-full cursor-pointer" aria-label="Close navigation"></label>
            </div>

            <aside
                id="admin-sidebar"
                class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full border-r border-zinc-100 bg-white transition-transform duration-200 ease-out peer-checked:translate-x-0 lg:translate-x-0"
                aria-label="Sidebar"
            >
                <div class="flex h-16 items-center justify-between border-b border-zinc-100 px-6">
                    <a href="{{ $homeRoute }}" class="flex items-center gap-3 text-zinc-900">
                        <x-app-logo-icon class="h-8 w-8 rounded bg-zinc-900 p-1.5 text-white" />
                        <span class="admin-brand-text text-sm font-black uppercase tracking-widest">{{ \App\Models\Setting::get('shop_name', 'Showroom') }}</span>
                    </a>

                    <label for="admin-mobile-nav" class="rounded-md p-2 text-zinc-400 hover:bg-zinc-50 lg:hidden" aria-label="Close sidebar">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                            <path d="M6 6L18 18M6 18L18 6" stroke-linecap="round"/>
                        </svg>
                    </label>
                </div>

                <nav class="flex h-[calc(100%-4rem)] flex-col px-4 py-6">
                    <div class="space-y-1">
                        @if ($isAdmin)
                            <a
                                href="{{ route('admin.dashboard') }}"
                                data-admin-nav-link
                                class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                    <path d="M3 12L12 4L21 12V20A1 1 0 0 1 20 21H4A1 1 0 0 1 3 20V12Z" stroke-linejoin="round"/>
                                </svg>
                                <span class="admin-label text-xs font-bold uppercase tracking-widest">Dashboard</span>
                            </a>

                            <a
                                href="{{ route('admin.units.index') }}"
                                data-admin-nav-link
                                class="admin-nav-item {{ request()->routeIs('admin.units.*') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                    <path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/>
                                </svg>
                                <span class="admin-label text-xs font-bold uppercase tracking-widest">Units</span>
                            </a>

                            <a
                                href="{{ route('admin.categories.index') }}"
                                data-admin-nav-link
                                class="admin-nav-item {{ request()->routeIs('admin.categories.*') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                    <path d="M4 7H20M4 12H20M4 17H14" stroke-linecap="round"/>
                                </svg>
                                <span class="admin-label text-xs font-bold uppercase tracking-widest">Categories</span>
                            </a>

                            <a
                                href="{{ route('admin.employees.index') }}"
                                data-admin-nav-link
                                class="admin-nav-item {{ request()->routeIs('admin.employees.*') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" stroke-linecap="round"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round"/>
                                    <path d="M9 21v-2a4 4 0 0 1 4-4h2" stroke-linecap="round"/>
                                </svg>
                                <span class="admin-label text-xs font-bold uppercase tracking-widest">Employees</span>
                            </a>

                            <a
                                href="{{ route('admin.inquiries.index') }}"
                                data-admin-nav-link
                                class="admin-nav-item relative {{ request()->routeIs('admin.inquiries.*') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15V19A2 2 0 0 1 19 21H5A2 2 0 0 1 3 19V15M7 10L12 15L17 10M12 15V3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="admin-label text-xs font-bold uppercase tracking-widest">Inquiries</span>
                                @livewire('inquiry-badge')
                            </a>

                            <a
                                href="{{ route('admin.logs.index') }}"
                                data-admin-nav-link
                                class="admin-nav-item {{ request()->routeIs('admin.logs.*') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                    <path d="M6 6H18V18H6V6Z" stroke-linejoin="round"/>
                                    <path d="M9 10H15M9 14H13" stroke-linecap="round"/>
                                </svg>
                                <span class="admin-label text-xs font-bold uppercase tracking-widest">Logs</span>
                            </a>
                        @elseif ($isStaff)
                            <a
                                href="{{ route('admin.units.index') }}"
                                data-admin-nav-link
                                class="admin-nav-item {{ request()->routeIs('admin.units.*') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                    <path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/>
                                </svg>
                                <span class="admin-label text-xs font-bold uppercase tracking-widest">Units</span>
                            </a>
                        @endif

                        <div class="mt-6 mb-2 px-4">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Settings</span>
                        </div>

                        <a
                            href="{{ route('admin.settings.shop') }}"
                            data-admin-nav-link
                            class="admin-nav-item {{ request()->routeIs('admin.settings.*') || request()->is('settings*') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                            <span class="admin-label text-xs font-bold uppercase tracking-widest">Settings</span>
                        </a>
                    </div>

                    <div class="mt-auto border-t border-zinc-100 pt-6">
                        <form method="POST" action="{{ route('logout') }}" data-disable-on-submit>
                            @csrf
                            <button type="submit" class="admin-nav-item w-full text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                    <path d="M9 5H5V19H9" stroke-linecap="round"/>
                                    <path d="M19 12H9" stroke-linecap="round"/>
                                    <path d="M16 15L19 12L16 9" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="admin-label text-xs font-bold uppercase tracking-widest">Logout</span>
                            </button>
                        </form>
                    </div>
                </nav>
            </aside>

            <div id="admin-content" class="min-h-screen transition-[padding] duration-200 ease-out lg:pl-72">
                <header class="sticky top-0 z-20 border-b border-zinc-100 bg-white/80 backdrop-blur-md">
                    <div class="flex h-16 items-center justify-between gap-6 px-4 sm:px-8">
                        <div class="flex items-center gap-4">
                            <label for="admin-mobile-nav" class="inline-flex rounded-full border border-zinc-200 p-2 text-zinc-400 hover:bg-zinc-50 hover:text-zinc-900 lg:hidden" aria-label="Open sidebar">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                    <path d="M4 7H20M4 12H20M4 17H20" stroke-linecap="round"/>
                                </svg>
                            </label>

                            <button
                                id="admin-sidebar-toggle"
                                type="button"
                                class="hidden rounded-full border border-zinc-200 p-2 text-zinc-400 hover:bg-zinc-50 hover:text-zinc-900 lg:inline-flex"
                                aria-label="Collapse sidebar"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5">
                                    <path d="M15 18L9 12L15 6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <div class="h-8 w-px bg-zinc-100"></div>

                            <div>
                                <h1 class="text-sm font-black uppercase tracking-widest text-zinc-900">{{ $title ?? 'Admin' }}</h1>
                            </div>
                        </div>

                        <div class="hidden flex-1 max-w-xl md:block">
                            @if ($isAdmin)
                                <form method="GET" action="{{ route('admin.units.index') }}" class="relative">
                                    <svg viewBox="0 0 24 24" fill="none" class="pointer-events-none absolute left-3.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-zinc-400" stroke="currentColor" stroke-width="2.5">
                                        <circle cx="11" cy="11" r="7"/>
                                        <path d="M20 20L16.65 16.65" stroke-linecap="round"/>
                                    </svg>
                                    <input
                                        type="search"
                                        name="q"
                                        value="{{ request('q') }}"
                                        placeholder="Search catalog..."
                                        class="h-10 w-full rounded-full border border-zinc-100 bg-zinc-50 pl-10 text-xs text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none transition-all"
                                    >
                                </form>
                            @endif
                        </div>

                        <div class="flex items-center gap-4">
                            <a
                                href="{{ route('admin.inquiries.index') }}"
                                class="relative inline-flex rounded-full border border-zinc-200 p-2 text-zinc-400 hover:bg-zinc-50 hover:text-zinc-900"
                                aria-label="Inquiries"
                                title="Recent Inquiries"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                    <path d="M12 4A4 4 0 0 0 8 8V11.4L6.3 14.8A1 1 0 0 0 7.2 16.2H16.8A1 1 0 0 0 17.7 14.8L16 11.4V8A4 4 0 0 0 12 4Z" stroke-linejoin="round"/>
                                    <path d="M10 19A2 2 0 0 0 14 19" stroke-linecap="round"/>
                                </svg>
                                @livewire('inquiry-badge')
                            </a>

                            <div class="hidden flex-col items-end text-right sm:flex">
                                <span class="text-xs font-black tracking-tight text-zinc-900 leading-none">{{ $currentUser?->name }}</span>
                                <span class="mt-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400 leading-none">{{ $isAdmin ? 'Administrator' : 'Staff' }}</span>
                            </div>

                            <details class="relative [&_summary::-webkit-details-marker]:hidden">
                                <summary class="flex cursor-pointer h-9 w-9 items-center justify-center rounded-full bg-zinc-900 text-[10px] font-black text-white uppercase tracking-tighter hover:scale-105 transition-transform">
                                    {{ substr($currentUser?->name ?? 'U', 0, 1) }}
                                </summary>

                                <div class="absolute right-0 z-30 mt-3 w-64 rounded-2xl border border-zinc-100 bg-white p-4 shadow-2xl">
                                    <p class="text-xs font-black uppercase tracking-widest text-zinc-400">Account</p>
                                    <p class="mt-2 text-sm font-bold text-zinc-900">{{ $currentUser?->name }}</p>
                                    <p class="mt-0.5 text-xs text-zinc-500">{{ $currentUser?->email }}</p>
                                    
                                    <div class="mt-4 space-y-1 border-t border-zinc-50 pt-4">
                                        <form method="POST" action="{{ route('logout') }}" data-disable-on-submit>
                                            @csrf
                                            <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-xs font-bold text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 transition-colors">
                                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2">
                                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                                                </svg>
                                                Sign Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </details>
                        </div>
                    </div>
                </header>

                <div id="admin-toast-region" class="pointer-events-none fixed right-6 top-6 z-50 space-y-3" aria-live="polite" aria-atomic="true">
                    @if (session('status'))
                        <div data-admin-toast class="admin-toast pointer-events-auto rounded-2xl border border-emerald-100 bg-white p-4 shadow-xl shadow-emerald-500/5">
                            <div class="flex items-center gap-3">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 text-white">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" stroke="currentColor" stroke-width="3">
                                        <path d="M20 6L9 17L4 12" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <p class="text-xs font-bold text-zinc-900">{{ session('status') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <main id="admin-main" class="px-4 py-8 sm:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @fluxScripts
        <script>
            (() => {
                const storageKey = 'admin.sidebar.collapsed';

                const closeToast = (toast) => {
                    toast.classList.add('opacity-0', 'translate-y-2');
                    window.setTimeout(() => toast.remove(), 180);
                };

                const initAdminLayout = () => {
                    const shell = document.getElementById('admin-shell');
                    const sidebarToggle = document.getElementById('admin-sidebar-toggle');

                    if (shell && window.matchMedia('(min-width: 1024px)').matches) {
                        shell.dataset.collapsed = window.localStorage.getItem(storageKey) === '1' ? 'true' : 'false';
                    }

                    if (sidebarToggle && !sidebarToggle.dataset.bound) {
                        sidebarToggle.dataset.bound = '1';
                        sidebarToggle.addEventListener('click', () => {
                            const collapsed = shell?.dataset.collapsed === 'true';
                            const next = collapsed ? 'false' : 'true';
                            if (shell) {
                                shell.dataset.collapsed = next;
                            }
                            window.localStorage.setItem(storageKey, next === 'true' ? '1' : '0');
                        });
                    }

                    document.querySelectorAll('[data-admin-nav-link]').forEach((link) => {
                        if (link.dataset.boundCloseNav) {
                            return;
                        }

                        link.dataset.boundCloseNav = '1';
                        link.addEventListener('click', () => {
                            const mobileNav = document.getElementById('admin-mobile-nav');
                            if (mobileNav instanceof HTMLInputElement) {
                                mobileNav.checked = false;
                            }
                        });
                    });

                    document.querySelectorAll('[data-admin-toast-close]').forEach((button) => {
                        if (button.dataset.boundCloseToast) {
                            return;
                        }

                        button.dataset.boundCloseToast = '1';
                        button.addEventListener('click', () => {
                            const toast = button.closest('[data-admin-toast]');
                            if (toast instanceof HTMLElement) {
                                closeToast(toast);
                            }
                        });
                    });

                    document.querySelectorAll('[data-admin-toast]').forEach((toast) => {
                        if (toast.dataset.autoClose === '1') {
                            return;
                        }

                        toast.dataset.autoClose = '1';
                        window.setTimeout(() => closeToast(toast), 4200);
                    });

                    document.querySelectorAll('form[data-disable-on-submit]').forEach((form) => {
                        if (form.dataset.boundDisableSubmit) {
                            return;
                        }

                        form.dataset.boundDisableSubmit = '1';
                        form.addEventListener('submit', () => {
                            const submitter = form.querySelector('button[type="submit"]');
                            if (submitter instanceof HTMLButtonElement) {
                                submitter.disabled = true;
                                submitter.classList.add('opacity-60', 'cursor-not-allowed');
                            }
                        });
                    });
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initAdminLayout, { once: true });
                } else {
                    initAdminLayout();
                }

                document.addEventListener('livewire:navigated', initAdminLayout);
            })();
        </script>
    </body>
</html>
