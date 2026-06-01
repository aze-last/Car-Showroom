<header class="fixed top-0 lg:left-72 right-0 h-20 bg-white/80 backdrop-blur-md border-b border-gallery-outline/10 z-40 hidden md:flex items-center justify-between px-12">
    <div>
        <h2 class="text-sm font-bold uppercase tracking-widest text-zinc-400">{{ $title ?? 'Management Console' }}</h2>
    </div>

    <div class="flex items-center gap-6">
        <livewire:public.notification-bell />
        <div class="h-10 w-px bg-gallery-outline/10"></div>
        
        <flux:dropdown align="end" class="h-12">
            <flux:button variant="ghost" class="h-12 !px-4 hover:bg-zinc-50 rounded-2xl transition-all">
                <div class="flex items-center gap-4 text-left">
                    <div class="hidden lg:block">
                        <p class="text-xs font-bold text-black leading-none">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Institutional Admin</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-zinc-900 text-white flex items-center justify-center font-bold text-xs shadow-lg ring-4 ring-white">
                        {{ auth()->user()->initials() }}
                    </div>
                </div>
            </flux:button>

            <flux:menu class="min-w-[220px] !p-2 rounded-3xl border-none ambient-shadow shadow-2xl">
                <div class="px-4 py-3 mb-2 border-b border-zinc-50">
                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-300">Identity Vault</p>
                </div>
                
                <flux:menu.item href="{{ route('profile.edit') }}" wire:navigate icon="user" class="rounded-xl font-bold text-xs py-3 uppercase tracking-widest">
                    Curator Profile
                </flux:menu.item>
                
                <flux:menu.item href="{{ route('user-password.edit') }}" wire:navigate icon="key" class="rounded-xl font-bold text-xs py-3 uppercase tracking-widest">
                    Credentials
                </flux:menu.item>

                <flux:separator variant="subtle" class="my-2" />

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="rounded-xl font-bold text-xs py-3 uppercase tracking-widest text-red-600 hover:bg-red-50">
                        Terminate Session
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </div>
</header>
