<div class="relative" x-data="{ open: false }" wire:poll.30s>
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
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        class="absolute right-[-10px] sm:right-0 mt-6 w-[calc(100vw-1.5rem)] sm:w-96 bg-white rounded-[32px] shadow-[0_40px_80px_-16px_rgba(0,0,0,0.3)] border border-zinc-100 z-50 overflow-hidden"
    >
        <div class="p-6 md:p-8 border-b border-zinc-100 flex justify-between items-center bg-white">
            <div>
                <h3 class="text-xs md:text-sm font-black text-black uppercase tracking-[0.2em]">Alert Console</h3>
                <div class="flex items-center gap-2 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest">{{ $unreadCount }} Unread Messages</p>
                </div>
            </div>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[9px] md:text-[10px] font-black text-black hover:bg-zinc-100 uppercase tracking-widest transition-all px-4 py-2 rounded-xl border border-zinc-100">Clear All</button>
            @endif
        </div>

        <div class="max-h-[50vh] md:max-h-[400px] overflow-y-auto custom-scrollbar bg-white">
            @forelse($notifications as $notification)
                <div 
                    wire:click="handleNotificationClick('{{ $notification->id }}')"
                    class="group p-5 md:p-6 hover:bg-zinc-50 transition-all cursor-pointer border-b border-zinc-50 last:border-0 flex gap-4 {{ $notification->read_at ? 'opacity-30' : '' }}"
                >
                    <div class="h-10 w-10 md:h-12 md:w-12 rounded-2xl {{ $notification->read_at ? 'bg-zinc-50' : 'bg-zinc-100' }} flex items-center justify-center shrink-0 transition-transform group-active:scale-90">
                        @if($notification->type === 'App\Notifications\BidPlacedNotification')
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-black"><path d="m14.5 12.5-8 8a2.11 2.11 0 1 1-3-3l8-8"/><path d="m16 16 2 2"/><path d="m19 13 2 2"/><path d="m5 5 2 2"/><path d="m2 2 2 2"/><path d="M22 22 2 2"/></svg>
                        @elseif($notification->type === 'App\Notifications\UnitAcquiredNotification' || $notification->type === 'App\Notifications\DepositApprovedNotification')
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600"><path d="M20 6 9 17l-5-5"/></svg>
                        @elseif($notification->type === 'App\Notifications\DepositRejectedNotification')
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-red-600"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        @elseif($notification->type === 'App\Notifications\DepositSubmittedNotification')
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-900"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        @elseif($notification->type === 'App\Notifications\UserSentMessageNotification' || $notification->type === 'App\Notifications\AdminRepliedToInquiry.php')
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] md:text-xs font-bold text-black leading-snug break-words group-hover:text-zinc-600 transition-colors">
                            {{ $notification->data['message'] ?? 'System Alert' }}
                        </p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                            @if(!$notification->read_at)
                                <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 px-12 text-center">
                    <div class="h-24 w-24 bg-zinc-50 rounded-[30px] flex items-center justify-center mx-auto mb-6 transform rotate-12">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-200 -rotate-12"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                    </div>
                    <p class="text-[10px] font-black text-zinc-300 uppercase tracking-[0.4em]">Silence in the Hall</p>
                </div>
            @endforelse
        </div>

        @if($notifications->isNotEmpty())
            <div class="p-6 bg-white border-t border-zinc-50">
                <a href="{{ route('garage') }}" class="flex items-center justify-center w-full py-4 bg-zinc-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-zinc-900/10 active:scale-95">
                    View My Collection
                </a>
            </div>
        @endif
    </div>
</div>
