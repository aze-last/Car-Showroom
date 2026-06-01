<div class="px-6 md:px-container-padding py-12 md:py-16 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 md:gap-12">
        <!-- Left: Image Gallery & Details -->
        <div class="lg:col-span-2 space-y-8 md:space-y-12">
            <div class="relative aspect-[16/9] rounded-[30px] md:rounded-[50px] overflow-hidden shadow-2xl bg-zinc-100 group">
                @if($activeImage)
                    <img src="{{ Storage::url($activeImage) }}" alt="{{ $auction->unit->name }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                @endif
                
                <div class="absolute top-4 md:top-8 left-4 md:left-8 flex gap-2 md:gap-3">
                    <span class="text-[9px] md:text-[11px] font-black text-white uppercase tracking-[0.3em] bg-black/40 px-4 md:px-6 py-2 md:py-2.5 rounded-full backdrop-blur-xl border border-white/20 shadow-2xl">Lot #{{ $auction->lot_number }}</span>
                    @if($auction->status === 'live')
                        <span class="text-[9px] md:text-[11px] font-black text-white uppercase tracking-[0.3em] bg-red-600/80 px-4 md:px-6 py-2 md:py-2.5 rounded-full backdrop-blur-xl border border-white/20 shadow-2xl flex items-center gap-1.5 md:gap-2">
                            <span class="w-1.5 h-1.5 md:w-2 md:h-2 rounded-full bg-white animate-pulse"></span> LIVE
                        </span>
                    @endif
                </div>

                <!-- Overlay Gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>
            </div>

            <!-- Image Thumbnails -->
            <div class="grid grid-cols-4 md:grid-cols-5 gap-3 md:gap-4">
                @foreach($auction->unit->images as $img)
                    <button 
                        wire:click="setActiveImage('{{ $img->url }}')"
                        class="aspect-[4/3] rounded-2xl md:rounded-3xl overflow-hidden bg-zinc-50 border-2 transition-all duration-300 {{ $activeImage === $img->url ? 'border-black scale-95' : 'border-transparent hover:border-zinc-200' }}"
                    >
                        <img src="{{ Storage::url($img->url) }}" class="w-full h-full object-cover">
                    </button>
                @endforeach
            </div>

            <!-- Vehicle Specs Bento -->
            <div class="bg-zinc-50 rounded-[30px] md:rounded-[40px] p-6 md:p-10 grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-10 border border-zinc-100">
                <div>
                    <p class="text-[9px] md:text-[10px] text-zinc-400 font-black uppercase tracking-[0.2em] mb-1 md:mb-2">Year Model</p>
                    <p class="text-lg md:text-xl font-bold text-black">{{ $auction->unit->year }}</p>
                </div>
                <div>
                    <p class="text-[9px] md:text-[10px] text-zinc-400 font-black uppercase tracking-[0.2em] mb-1 md:mb-2">Transmission</p>
                    <p class="text-lg md:text-xl font-bold text-black">{{ $auction->unit->transmission }}</p>
                </div>
                <div>
                    <p class="text-[9px] md:text-[10px] text-zinc-400 font-black uppercase tracking-[0.2em] mb-1 md:mb-2">Fuel Type</p>
                    <p class="text-lg md:text-xl font-bold text-black">{{ $auction->unit->fuel_type }}</p>
                </div>
                <div>
                    <p class="text-[9px] md:text-[10px] text-zinc-400 font-black uppercase tracking-[0.2em] mb-1 md:mb-2">Mileage</p>
                    <p class="text-lg md:text-xl font-bold text-black">{{ number_format($auction->unit->mileage) }} KM</p>
                </div>
            </div>

            <div class="prose prose-zinc max-w-none">
                <h1 class="text-3xl md:text-5xl font-bold text-black tracking-tighter">{{ $auction->unit->name }}</h1>
                <p class="text-lg md:text-xl text-zinc-500 font-medium leading-relaxed mt-4 md:mt-6">{{ $auction->unit->description ?: 'No additional description provided.' }}</p>
            </div>
        </div>

        <!-- Right: Bidding Console (Sticky) -->
        <div class="space-y-8">
            <div class="bg-white rounded-[30px] md:rounded-[50px] border border-zinc-100 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.1)] p-6 md:p-10 sticky top-28 z-10 space-y-8 md:space-y-10">
                <!-- Current Bid Header -->
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <p class="text-[10px] md:text-[11px] text-zinc-400 font-black uppercase tracking-[0.3em]">Current Highest</p>
                        <p class="text-3xl md:text-5xl font-bold text-black tracking-tighter">₱{{ number_format($auction->current_bid_php ?: $auction->starting_bid_php) }}</p>
                    </div>
                    <div class="text-right space-y-1">
                        <p class="text-[10px] md:text-[11px] text-zinc-400 font-black uppercase tracking-[0.3em]">Time Left</p>
                        <p class="text-xl md:text-2xl font-bold text-red-600 tabular-nums animate-pulse" wire:poll.1s>
                            {{ now()->greaterThan($auction->end_at) ? 'EXPIRED' : now()->diff($auction->end_at)->format('%H:%I:%S') }}
                        </p>
                    </div>
                </div>

                <!-- Bidding Form -->
                <div class="space-y-6 pt-6 border-t border-zinc-50">
                    <div class="flex items-center gap-3">
                        @if($auction->current_bid_php >= $auction->reserve_price_php)
                            <span class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                            <span class="text-[10px] md:text-[11px] font-black text-emerald-600 uppercase tracking-widest">Reserve Met</span>
                        @else
                            <span class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-zinc-300"></span>
                            <span class="text-[10px] md:text-[11px] font-black text-zinc-400 uppercase tracking-widest">Reserve Not Met</span>
                        @endif
                    </div>

                    <!-- Quick Bid Bubbles -->
                    <div class="grid grid-cols-3 gap-2 md:gap-3">
                        @php
                            $current = $auction->current_bid_php ?: $auction->starting_bid_php;
                            $increments = [50000, 100000, 250000];
                        @endphp
                        @foreach($increments as $inc)
                            <button 
                                type="button"
                                wire:click="$set('bidAmount', {{ $current + $inc }})"
                                class="py-2.5 md:py-3 px-3 md:px-4 rounded-xl md:rounded-2xl border border-zinc-100 text-[9px] md:text-[10px] font-black uppercase tracking-widest hover:bg-black hover:text-white hover:border-black transition-all"
                            >
                                +₱{{ number_format($inc/1000) }}k
                            </button>
                        @endforeach
                    </div>

                    <form wire:submit.prevent="placeBid" class="space-y-4">
                        <div class="relative group">
                            <span class="absolute left-6 md:left-8 top-1/2 -translate-y-1/2 text-zinc-400 font-bold text-lg md:text-xl transition-colors group-focus-within:text-black">₱</span>
                            <input 
                                type="text" 
                                x-data="{ 
                                    raw: @entangle('bidAmount'),
                                    format(val) {
                                        if (val === null || val === '') return '';
                                        return val.toString().replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                                    }
                                }" 
                                x-on:input="
                                    let clean = $event.target.value.replace(/,/g, '').replace(/\D/g, '');
                                    raw = clean === '' ? null : parseInt(clean);
                                    $event.target.value = format(clean);
                                "
                                x-init="$el.value = format(raw)"
                                class="w-full bg-zinc-50 border-none rounded-[25px] md:rounded-[30px] py-4 md:py-6 pl-12 md:pl-14 pr-6 md:pr-8 font-bold text-xl md:text-2xl focus:ring-2 focus:ring-black transition-all"
                            >
                        </div>
                        
                        <button type="submit" class="w-full bg-black text-white rounded-[25px] md:rounded-[30px] py-4 md:py-6 font-black uppercase tracking-[0.2em] text-xs md:text-sm hover:scale-[1.02] active:scale-95 transition-all shadow-2xl shadow-black/20">
                            Confirm Bid
                        </button>
                    </form>
                    
                    @if($message)
                        <div class="p-4 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-2xl border border-emerald-100 text-center animate-showroom-fade-up">
                            {{ $message }}
                        </div>
                    @endif
                    @error('bidAmount')
                        <div class="p-4 bg-red-50 text-red-700 text-xs font-bold rounded-2xl border border-red-100 text-center animate-showroom-fade-up">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Bid History -->
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <p class="text-[10px] md:text-[11px] text-zinc-400 font-black uppercase tracking-[0.3em]">Bid History</p>
                        <span class="text-[9px] md:text-[10px] font-bold text-zinc-300">{{ $auction->bids->count() }} Bids</span>
                    </div>
                    
                    <div class="space-y-4 max-h-[300px] md:max-h-[350px] overflow-y-auto pr-2 md:pr-3 custom-scrollbar">
                        @forelse($auction->bids->sortByDesc('amount_php') as $index => $bid)
                            <div class="flex justify-between items-center p-4 md:p-5 rounded-[20px] md:rounded-[25px] border border-zinc-50 {{ $index === 0 ? 'bg-black text-white shadow-xl scale-[1.02]' : 'bg-zinc-50 text-black' }} transition-all animate-showroom-fade-up" style="animation-delay: {{ $index * 50 }}ms">
                                <div class="flex items-center gap-3 md:gap-4">
                                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full {{ $index === 0 ? 'bg-white/20' : 'bg-black/10' }} flex items-center justify-center text-[9px] md:text-[10px] font-black">
                                        {{ strtoupper(substr($bid->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-[11px] md:text-xs font-black tracking-tight">{{ $bid->user->name }}</p>
                                        <p class="text-[9px] md:text-[10px] {{ $index === 0 ? 'text-white/40' : 'text-zinc-400' }} font-bold">{{ $bid->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs md:text-sm font-black">₱{{ number_format($bid->amount_php) }}</p>
                                    @if($index === 0)
                                        <span class="text-[7px] md:text-[8px] font-black uppercase tracking-widest text-emerald-400">Winning</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 opacity-30">
                                <p class="text-xs font-black uppercase tracking-widest">Waiting for first bid</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
