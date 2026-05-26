<div class="px-6 md:px-container-padding py-12 md:py-16 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Left: Image Gallery -->
        <div class="lg:col-span-2 space-y-8">
            <div class="relative aspect-[16/9] rounded-[40px] overflow-hidden shadow-2xl bg-zinc-100">
                @if($activeImage)
                    <img src="{{ Storage::url($activeImage) }}" alt="{{ $auction->unit->name }}" class="w-full h-full object-cover animate-showroom-fade-up">
                @endif
                <div class="absolute top-6 left-6 flex gap-3">
                    <span class="text-[12px] font-bold text-white uppercase tracking-widest bg-black/50 px-4 py-2 rounded-full backdrop-blur-md border border-white/20">Lot {{ $auction->lot_number }}</span>
                    @if($auction->unit->isSold())
                        <span class="text-[12px] font-bold text-white uppercase tracking-widest bg-black px-4 py-2 rounded-full backdrop-blur-md border border-white/20 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-white"></span> SOLD
                        </span>
                    @elseif($auction->status === 'live')
                        <span class="text-[12px] font-bold text-white uppercase tracking-widest bg-red-600/80 px-4 py-2 rounded-full backdrop-blur-md border border-white/20 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> Live Now
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-4 gap-4">
                @foreach($auction->unit->images->take(4) as $img)
                    <div 
                        wire:click="setActiveImage('{{ $img->url }}')"
                        class="aspect-square rounded-2xl overflow-hidden bg-zinc-100 border-2 {{ $activeImage === $img->url ? 'border-black' : 'border-transparent' }} group cursor-pointer transition-all hover:scale-105"
                    >
                        <img src="{{ Storage::url($img->url) }}" class="w-full h-full object-cover">
                    </div>
                @endforeach
            </div>

            <div class="prose prose-zinc max-w-none">
                <h1 class="text-4xl font-bold text-black">{{ $auction->unit->name }}</h1>
                <p class="text-xl text-zinc-500">{{ $auction->unit->curator_note }}</p>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 py-8 border-y border-zinc-100 my-8">
                    <div>
                        <p class="text-[10px] text-zinc-400 uppercase tracking-widest mb-1">Year</p>
                        <p class="text-lg font-bold text-black">{{ $auction->unit->year }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-zinc-400 uppercase tracking-widest mb-1">Transmission</p>
                        <p class="text-lg font-bold text-black">{{ $auction->unit->transmission }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-zinc-400 uppercase tracking-widest mb-1">Fuel Type</p>
                        <p class="text-lg font-bold text-black">{{ $auction->unit->fuel_type }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-zinc-400 uppercase tracking-widest mb-1">Class</p>
                        <p class="text-lg font-bold text-black">{{ $auction->unit->category->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Bidding Console -->
        <div class="space-y-8">
            <div class="bg-white rounded-[40px] border border-zinc-100 shadow-xl p-8 sticky top-28">
                <div class="space-y-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[10px] text-zinc-500 uppercase tracking-widest mb-1">Current Bid</p>
                            <p class="text-4xl font-bold text-black">₱{{ number_format($auction->current_bid_php ?: $auction->starting_bid_php) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-zinc-500 uppercase tracking-widest mb-1">Time Remaining</p>
                            <p class="text-2xl font-bold text-red-600 tabular-nums" wire:poll.1s>
                                @php
                                    $diff = now()->diff($auction->end_at);
                                    echo now()->greaterThan($auction->end_at) ? 'ENDED' : $diff->format('%H:%I:%S');
                                @endphp
                            </p>
                        </div>
                    </div>

                    <div class="py-4 border-t border-zinc-50">
                        @if($auction->unit->isSold())
                            <div class="bg-black text-white rounded-[32px] p-8 text-center space-y-4 animate-showroom-fade-up">
                                <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-4">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17L4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <h3 class="text-2xl font-bold tracking-tight">SOLD EXTERNALLY</h3>
                                <p class="text-xs font-medium text-zinc-400 leading-relaxed">This asset was acquired via a direct private transaction. Bidding is now formally closed.</p>
                                <a href="{{ route('home') }}" class="inline-block pt-4 text-[10px] font-bold uppercase tracking-widest border-b border-white/30 hover:border-white transition-all">Return to Showroom</a>
                            </div>
                        @else
                            <div class="flex items-center gap-3 mb-4">
                                @if($auction->isReserveMet())
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-widest">Reserve Met</span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-zinc-300"></span>
                                    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest">Reserve Not Met</span>
                                @endif
                            </div>

                            <form wire:submit.prevent="placeBid" class="space-y-4">
                                <div class="relative">
                                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-zinc-400 font-bold">₱</span>
                                    <input type="number" wire:model="bidAmount" class="w-full bg-zinc-50 border-none rounded-2xl py-4 pl-12 pr-6 font-bold text-xl focus:ring-2 focus:ring-black transition-all" placeholder="Enter Amount">
                                </div>
                                @error('bidAmount') <span class="text-red-600 text-xs font-bold">{{ $message }}</span> @enderror
                                @if($message) <span class="text-emerald-600 text-xs font-bold">{{ $message }}</span> @endif

                                <button type="submit" class="w-full bg-black text-white rounded-2xl py-5 font-bold text-lg hover:opacity-90 transition-all shadow-lg active:scale-[0.98]">
                                    Place Bid
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <p class="text-[10px] text-zinc-400 uppercase tracking-widest font-bold">Recent Bids</p>
                        <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2">
                            @forelse($auction->bids->sortByDesc('created_at') as $bid)
                                <div class="flex justify-between items-center p-4 rounded-2xl bg-zinc-50 border border-zinc-100 animate-showroom-fade-up">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center text-[10px] font-bold">
                                            {{ strtoupper(substr($bid->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-black">{{ $bid->user->name }}</p>
                                            <p class="text-[10px] text-zinc-400">{{ $bid->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-black">₱{{ number_format($bid->amount_php) }}</p>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <p class="text-xs text-zinc-400 font-bold uppercase tracking-widest">No bids yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
