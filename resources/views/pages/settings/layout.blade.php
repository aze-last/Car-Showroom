<article class="bg-white rounded-[48px] border border-zinc-100 ambient-shadow overflow-hidden animate-showroom-fade-up" style="animation-delay: 0.1s;">
    <div class="p-4 md:p-12">
        <div class="flex items-start max-md:flex-col gap-12">
            <div class="w-full md:w-[280px] space-y-8">
                <div>
                    <p class="mb-6 text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Security & Profile</p>
                    <div class="space-y-2">
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('admin.settings.shop') }}" wire:navigate class="flex items-center gap-4 rounded-2xl px-6 py-4 text-xs font-bold transition-all {{ request()->routeIs('admin.settings.shop') ? 'bg-black text-white shadow-2xl scale-[1.02]' : 'text-zinc-500 hover:bg-zinc-50 hover:text-black' }}">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/></svg>
                                {{ __('System Master') }}
                            </a>
                        @endif
                        <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-4 rounded-2xl px-6 py-4 text-xs font-bold transition-all {{ request()->routeIs('profile.edit') ? 'bg-black text-white shadow-2xl scale-[1.02]' : 'text-zinc-500 hover:bg-zinc-50 hover:text-black' }}">
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            {{ __('Identity') }}
                        </a>
                        <a href="{{ route('user-password.edit') }}" wire:navigate class="flex items-center gap-4 rounded-2xl px-6 py-4 text-xs font-bold transition-all {{ request()->routeIs('user-password.edit') ? 'bg-black text-white shadow-2xl scale-[1.02]' : 'text-zinc-500 hover:bg-zinc-50 hover:text-black' }}">
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            {{ __('Credentials') }}
                        </a>
                        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                            <a href="{{ route('two-factor.show') }}" wire:navigate class="flex items-center gap-4 rounded-2xl px-6 py-4 text-xs font-bold transition-all {{ request()->routeIs('two-factor.show') ? 'bg-black text-white shadow-2xl scale-[1.02]' : 'text-zinc-500 hover:bg-zinc-50 hover:text-black' }}">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                {{ __('Fortress') }}
                            </a>
                        @endif
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('admin.backup.show') }}" wire:navigate class="flex items-center gap-4 rounded-2xl px-6 py-4 text-xs font-bold transition-all {{ request()->routeIs('admin.backup.show') ? 'bg-black text-white shadow-2xl scale-[1.02]' : 'text-zinc-500 hover:bg-zinc-50 hover:text-black' }}">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                {{ __('Vault Backup') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="hidden h-auto w-px bg-zinc-50 md:block self-stretch mx-4"></div>

            <div class="flex-1 self-stretch space-y-12">
                <header class="pb-10 border-b border-zinc-50">
                    <h2 class="text-3xl font-bold tracking-tight text-black">{{ $heading ?? '' }}</h2>
                    <p class="mt-2 text-sm font-medium text-zinc-400">{{ $subheading ?? '' }}</p>
                </header>

                <div class="max-w-2xl">
                    @if ($errors->any())
                        <div class="mb-8 p-6 rounded-3xl bg-red-50 border border-red-100 text-red-600 animate-showroom-fade-up">
                            <p class="text-[10px] font-black uppercase tracking-widest mb-2">Update Interrupted</p>
                            <ul class="list-disc list-inside text-xs font-bold space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</article>
