<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
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

            <div class="fixed inset-0 z-30 hidden bg-slate-900/40 peer-checked:block lg:hidden">
                <label for="admin-mobile-nav" class="block h-full w-full cursor-pointer" aria-label="Close navigation"></label>
            </div>

            <aside
                id="admin-sidebar"
                class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full border-r border-slate-200 bg-white transition-transform duration-200 ease-out peer-checked:translate-x-0 lg:translate-x-0"
                aria-label="Sidebar"
            >
                <div class="flex h-16 items-center justify-between border-b border-slate-200 px-4">
                    <a href="{{ $homeRoute }}" class="flex items-center gap-3 text-slate-900">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 text-xs font-semibold text-white">CS</span>
                        <span class="admin-brand-text text-sm font-semibold">Showroom Admin</span>
                    </a>

                    <label for="admin-mobile-nav" class="rounded-md p-2 text-slate-500 hover:bg-slate-100 lg:hidden" aria-label="Close sidebar">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                            <path d="M6 6L18 18M6 18L18 6" stroke-linecap="round"/>
                        </svg>
                    </label>
                </div>

                <nav class="flex h-[calc(100%-4rem)] flex-col px-3 py-4">
                    @if ($isAdmin)
                        <a
                            href="{{ route('admin.dashboard') }}"
                            data-admin-nav-link
                            class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 12L12 4L21 12V20A1 1 0 0 1 20 21H4A1 1 0 0 1 3 20V12Z" stroke-linejoin="round"/>
                            </svg>
                            <span class="admin-label">Dashboard</span>
                        </a>

                        <a
                            href="{{ route('admin.units.index') }}"
                            data-admin-nav-link
                            class="admin-nav-item {{ request()->routeIs('admin.units.*') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/>
                                <path d="M4 8.5L12 13L20 8.5" stroke-linecap="round"/>
                            </svg>
                            <span class="admin-label">Units</span>
                        </a>

                        <a
                            href="{{ route('admin.categories.index') }}"
                            data-admin-nav-link
                            class="admin-nav-item {{ request()->routeIs('admin.categories.*') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 7H20M4 12H20M4 17H14" stroke-linecap="round"/>
                            </svg>
                            <span class="admin-label">Categories</span>
                        </a>

                        <a
                            href="{{ route('admin.employees.index') }}"
                            data-admin-nav-link
                            class="admin-nav-item {{ request()->routeIs('admin.employees.*') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                <path d="M7 11A3 3 0 1 0 7 5A3 3 0 0 0 7 11Z" stroke-linejoin="round"/>
                                <path d="M17 13A2 2 0 1 0 17 9A2 2 0 0 0 17 13Z" stroke-linejoin="round"/>
                                <path d="M3.5 19.5C3.5 16.7 5.6 15 8.2 15H8.8C11.4 15 13.5 16.7 13.5 19.5" stroke-linecap="round"/>
                                <path d="M14.5 19.5C14.5 17.6 16 16.4 17.8 16.4H18.2C20 16.4 21.5 17.6 21.5 19.5" stroke-linecap="round"/>
                            </svg>
                            <span class="admin-label">Employees</span>
                        </a>

                        <a
                            href="{{ route('admin.inquiries.index') }}"
                            data-admin-nav-link
                            class="admin-nav-item {{ request()->routeIs('admin.inquiries.*') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                <path d="M21 15V19A2 2 0 0 1 19 21H5A2 2 0 0 1 3 19V15M7 10L12 15L17 10M12 15V3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="admin-label">Inquiries</span>
                        </a>

                        <a
                            href="{{ route('admin.logs.index') }}"
                            data-admin-nav-link
                            class="admin-nav-item {{ request()->routeIs('admin.logs.*') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                <path d="M6 6H18V18H6V6Z" stroke-linejoin="round"/>
                                <path d="M9 10H15M9 14H13" stroke-linecap="round"/>
                            </svg>
                            <span class="admin-label">Logs</span>
                        </a>
                    @elseif ($isStaff)
                        <a
                            href="{{ route('admin.units.index') }}"
                            data-admin-nav-link
                            class="admin-nav-item {{ request()->routeIs('admin.units.*') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/>
                                <path d="M4 8.5L12 13L20 8.5" stroke-linecap="round"/>
                            </svg>
                            <span class="admin-label">Units</span>
                        </a>

                        <a
                            href="{{ route('admin.units.index') }}"
                            data-admin-nav-link
                            class="admin-nav-item {{ request()->routeIs('admin.units.qr') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 6H20V18H4V6Z" stroke-linejoin="round"/>
                                <path d="M8 10L11 13L16 8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="admin-label">QR Actions</span>
                        </a>
                    @endif

                    <div class="mt-auto border-t border-slate-200 pt-3">
                        <form method="POST" action="{{ route('logout') }}" data-disable-on-submit>
                            @csrf
                            <button type="submit" class="admin-nav-item w-full text-slate-700 hover:bg-slate-100">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                    <path d="M9 5H5V19H9" stroke-linecap="round"/>
                                    <path d="M15 16L19 12L15 8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M19 12H9" stroke-linecap="round"/>
                                </svg>
                                <span class="admin-label">Logout</span>
                            </button>
                        </form>
                    </div>
                </nav>
            </aside>

            <div id="admin-content" class="min-h-screen transition-[padding] duration-200 ease-out lg:pl-72">
                <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
                    <div class="flex flex-wrap items-center gap-3 px-4 py-3 sm:px-6 lg:px-8">
                        <div class="flex flex-1 items-center gap-2 sm:gap-3">
                            <label for="admin-mobile-nav" class="inline-flex rounded-md border border-slate-300 p-2 text-slate-600 hover:bg-slate-100 lg:hidden" aria-label="Open sidebar">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 7H20M4 12H20M4 17H20" stroke-linecap="round"/>
                                </svg>
                            </label>

                            <button
                                id="admin-sidebar-toggle"
                                type="button"
                                class="hidden rounded-md border border-slate-300 p-2 text-slate-600 hover:bg-slate-100 lg:inline-flex"
                                aria-label="Collapse sidebar"
                                title="Collapse sidebar"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                    <path d="M15 6L9 12L15 18" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-[0.12em] text-slate-500">{{ $panelTitle }}</p>
                                <h1 class="text-lg font-semibold text-slate-900">{{ $title ?? 'Admin' }}</h1>
                            </div>
                        </div>

                        <div class="order-3 w-full md:order-2 md:w-auto md:flex-1 md:max-w-xl">
                            @if ($isAdmin)
                                <form method="GET" action="{{ route('admin.units.index') }}" class="relative">
                                    <label for="admin-global-search" class="sr-only">Search units by name</label>
                                    <svg viewBox="0 0 24 24" fill="none" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="11" cy="11" r="7"/>
                                        <path d="M20 20L16.65 16.65" stroke-linecap="round"/>
                                    </svg>
                                    <input
                                        id="admin-global-search"
                                        type="search"
                                        name="q"
                                        value="{{ request('q') }}"
                                        placeholder="Search units by name"
                                        class="admin-input w-full pl-9"
                                    >
                                </form>
                            @else
                                <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">
                                    Staff mode: Add units, manage images, and run QR status actions.
                                </div>
                            @endif
                        </div>

                        <div class="order-2 ms-auto flex items-center gap-2 md:order-3">
                            <button
                                type="button"
                                class="inline-flex rounded-md border border-slate-300 p-2 text-slate-600 hover:bg-slate-100"
                                aria-label="Notifications"
                                title="Notifications placeholder"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                                    <path d="M12 4A4 4 0 0 0 8 8V11.4L6.3 14.8A1 1 0 0 0 7.2 16.2H16.8A1 1 0 0 0 17.7 14.8L16 11.4V8A4 4 0 0 0 12 4Z" stroke-linejoin="round"/>
                                    <path d="M10 19A2 2 0 0 0 14 19" stroke-linecap="round"/>
                                </svg>
                            </button>

                            <details class="relative [&_summary::-webkit-details-marker]:hidden">
                                <summary class="inline-flex cursor-pointer items-center gap-2 rounded-md border border-slate-300 px-2.5 py-2 text-slate-700 hover:bg-slate-100">
                                    <span class="hidden text-left sm:block">
                                        <span class="block text-sm font-medium leading-none">{{ $currentUser?->name }}</span>
                                        <span class="mt-1 block text-xs text-slate-500">{{ $currentUser?->email }}</span>
                                    </span>
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                                        <path d="M6 9L12 15L18 9" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </summary>

                                <div class="absolute right-0 z-30 mt-2 w-64 rounded-lg border border-slate-200 bg-white p-3 shadow-lg">
                                    <p class="text-sm font-semibold text-slate-900">{{ $currentUser?->name }}</p>
                                    <p class="mb-3 mt-1 break-all text-xs text-slate-500">{{ $currentUser?->email }}</p>
                                    <form method="POST" action="{{ route('logout') }}" data-disable-on-submit>
                                        @csrf
                                        <button type="submit" class="admin-btn-secondary w-full">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </details>
                        </div>
                    </div>
                </header>

                <div id="admin-toast-region" class="pointer-events-none fixed right-4 top-4 z-50 space-y-2" aria-live="polite" aria-atomic="true">
                    @if (session('status'))
                        <div data-admin-toast class="admin-toast pointer-events-auto rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow">
                            <div class="flex items-start justify-between gap-4">
                                <p>{{ session('status') }}</p>
                                <button type="button" class="text-emerald-700" data-admin-toast-close aria-label="Close notification">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                                        <path d="M6 6L18 18M6 18L18 6" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if (session('info'))
                        <div data-admin-toast class="admin-toast pointer-events-auto rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800 shadow">
                            <div class="flex items-start justify-between gap-4">
                                <p>{{ session('info') }}</p>
                                <button type="button" class="text-blue-700" data-admin-toast-close aria-label="Close notification">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                                        <path d="M6 6L18 18M6 18L18 6" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div data-admin-toast class="admin-toast pointer-events-auto rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow">
                            <div class="flex items-start justify-between gap-4">
                                <p>{{ session('error') }}</p>
                                <button type="button" class="text-red-700" data-admin-toast-close aria-label="Close notification">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                                        <path d="M6 6L18 18M6 18L18 6" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <main id="admin-main" class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
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
