<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-2 rounded-full hover:bg-zinc-100 transition-colors group">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-zinc-500 group-hover:text-black transition-colors"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-black"><path d="m14.5 12.5-8 8a2.11 2.11 0 1 1-3-3l8-8"/><path d="m16 16 2 2"/><path d="m19 13 2 2"/><path d="m5 5 2 2"/><path d="m2 2 2 2"/><path d="M22 22 2 2"/></svg>
                        @elseif($notification->type === 'App\Notifications\UnitAcquiredNotification')
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600"><path d="M20 6 9 17l-5-5"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-black leading-snug">
                            {{ $notification->data['message'] ?? 'System Alert' }}
                        </p>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-zinc-100 mb-4"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
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
