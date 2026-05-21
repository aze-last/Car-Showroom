<article class="admin-card">
    <div class="admin-card-body">
        <div class="flex items-start max-md:flex-col">
            <div class="me-10 w-full pb-4 md:w-[240px]">
                <p class="mb-4 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Settings</p>
                <div class="space-y-1">
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.settings.shop') }}" wire:navigate class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-bold transition-all {{ request()->routeIs('admin.settings.shop') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                            {{ __('Shop Information') }}
                        </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-bold transition-all {{ request()->routeIs('profile.edit') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                        {{ __('Profile') }}
                    </a>
                    <a href="{{ route('user-password.edit') }}" wire:navigate class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-bold transition-all {{ request()->routeIs('user-password.edit') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                        {{ __('Password') }}
                    </a>
                    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                        <a href="{{ route('two-factor.show') }}" wire:navigate class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-bold transition-all {{ request()->routeIs('two-factor.show') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                            {{ __('Two-Factor Auth') }}
                        </a>
                    @endif
                    <a href="{{ route('appearance.edit') }}" wire:navigate class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-bold transition-all {{ request()->routeIs('appearance.edit') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                        {{ __('Appearance') }}
                    </a>
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.backup.show') }}" wire:navigate class="flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-bold transition-all {{ request()->routeIs('admin.backup.show') ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900' }}">
                            {{ __('Backup & Restore') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden h-auto w-px bg-zinc-100 md:block self-stretch mx-4"></div>
            <flux:separator class="md:hidden my-6" />

            <div class="flex-1 self-stretch max-md:pt-6">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-widest text-zinc-900">{{ $heading ?? '' }}</h2>
                    <p class="mt-1 text-xs font-bold text-zinc-400">{{ $subheading ?? '' }}</p>
                </div>

                <div class="mt-8 w-full max-w-lg">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</article>
