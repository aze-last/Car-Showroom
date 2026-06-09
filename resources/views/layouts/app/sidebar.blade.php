<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        @php
            $user = auth()->user();
            $isAdmin = (bool) ($user?->is_admin ?? false);
            $adminLinks = [
                [
                    'label' => __('Dashboard'),
                    'href' => route('dashboard'),
                    'current' => request()->routeIs('dashboard'),
                    'icon' => 'dashboard',
                ],
            ];

            if ($isAdmin) {
                $adminLinks = [
                    ...$adminLinks,
                    [
                        'label' => __('Units'),
                        'href' => route('admin.units.index'),
                        'current' => request()->routeIs('admin.units.*'),
                        'icon' => 'units',
                    ],
                    [
                        'label' => __('Categories'),
                        'href' => route('admin.categories.index'),
                        'current' => request()->routeIs('admin.categories.*'),
                        'icon' => 'categories',
                    ],
                    [
                        'label' => __('Logs'),
                        'href' => route('admin.logs.index'),
                        'current' => request()->routeIs('admin.logs.*'),
                        'icon' => 'logs',
                    ],
                ];
            }
        @endphp

        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-800 bg-zinc-950/95 text-zinc-100">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <div class="grid gap-1.5">
                        @foreach ($adminLinks as $link)
                            <a
                                href="{{ $link['href'] }}"
                                wire:navigate
                                class="group inline-flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $link['current'] ? 'bg-amber-400 text-zinc-950 shadow-md shadow-amber-500/20' : 'text-zinc-300 hover:bg-zinc-800 hover:text-zinc-100' }}"
                                @if ($link['current']) aria-current="page" @endif
                            >
                                @switch($link['icon'])
                                    @case('dashboard')
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                                            <path d="M3 12L12 4L21 12V20A1 1 0 0 1 20 21H4A1 1 0 0 1 3 20V12Z" stroke-linejoin="round"/>
                                        </svg>
                                        @break
                                    @case('units')
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                                            <path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/>
                                            <path d="M4 8.5L12 13L20 8.5" stroke-linecap="round"/>
                                        </svg>
                                        @break
                                    @case('categories')
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                                            <path d="M4 7H20M4 12H20M4 17H14" stroke-linecap="round"/>
                                        </svg>
                                        @break
                                    @default
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                                            <path d="M6 6H18V18H6V6Z" stroke-linejoin="round"/>
                                            <path d="M9 10H15M9 14H13" stroke-linecap="round"/>
                                        </svg>
                                @endswitch
                                <span>{{ $link['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <a href="https://github.com/laravel/livewire-starter-kit" target="_blank" class="inline-flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-zinc-400 transition hover:bg-zinc-800 hover:text-zinc-200">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                        <path d="M7 7H17V17H7V7Z" stroke-linejoin="round"/>
                    </svg>
                    {{ __('Repository') }}
                </a>

                <a href="https://laravel.com/docs/starter-kits#livewire" target="_blank" class="inline-flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-zinc-400 transition hover:bg-zinc-800 hover:text-zinc-200">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                        <path d="M5 5H19V19H5V5Z" stroke-linejoin="round"/>
                        <path d="M8 9H16M8 13H14" stroke-linecap="round"/>
                    </svg>
                    {{ __('Documentation') }}
                </a>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>


        <!-- Mobile User Menu -->
        <flux:header class="border-b border-zinc-800 bg-zinc-950 lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <div class="bg-zinc-900/60 p-4 sm:p-5 lg:p-6">
            {{ $slot }}
        </div>

        @fluxScripts
        @stack('scripts')
    </body>
</html>
