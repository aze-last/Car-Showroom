<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-2 rounded-full hover:bg-zinc-100 transition-colors group">
        <span class="material-symbols-outlined text-zinc-500 group-hover:text-black">notifications</span>
        @if($unreadCount > 0)
            <span class="absolute top-2 right-2 flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
            </span>
        @endif
    </button>

    <div 
        x-show="open" 
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        class="absolute right-0 mt-4 w-96 bg-white rounded-[32px] shadow-2xl border border-zinc-100 z-50 overflow-hidden"
    >
        <div class="p-6 border-b border-zinc-50 flex justify-between items-center">
            <h3 class="text-sm font-bold text-black uppercase tracking-widest">Alert Console</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[10px] font-bold text-zinc-400 hover:text-black uppercase tracking-widest transition-colors">Clear All</button>
            @endif
        </div>

        <div class="max-h-[400px] overflow-y-auto">
            @forelse($notifications as $notification)
                <div 
                    wire:click="markAsRead('{{ $notification->id }}')"
                    class="p-6 hover:bg-zinc-50 transition-colors cursor-pointer border-b border-zinc-50 last:border-0 flex gap-4 {{ $notification->read_at ? 'opacity-60' : '' }}"
                >
                    <div class="h-10 w-10 rounded-full bg-zinc-100 flex items-center justify-center shrink-0">
                        @if($notification->type === 'App\Notifications\BidPlacedNotification')
                            <span class="material-symbols-outlined text-[20px] text-black">gavel</span>
                        @elseif($notification->type === 'App\Notifications\UnitAcquiredNotification')
                            <span class="material-symbols-outlined text-[20px] text-emerald-600">verified</span>
                        @else
                            <span class="material-symbols-outlined text-[20px] text-zinc-400">info</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-black leading-snug">
                            {{ $notification->data['message'] ?? 'Notification' }}
                        </p>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <span class="material-symbols-outlined text-zinc-100 text-6xl mb-4">notifications_off</span>
                    <p class="text-[10px] font-bold text-zinc-300 uppercase tracking-[0.4em]">Silence in the Hall</p>
                </div>
            @endforelse
        </div>

        @if($notifications->isNotEmpty())
            <div class="p-4 bg-zinc-50 text-center">
                <a href="{{ route('garage') }}" class="text-[9px] font-bold text-zinc-400 hover:text-black uppercase tracking-widest transition-colors">View My Collection</a>
            </div>
        @endif
    </div>
</div>
