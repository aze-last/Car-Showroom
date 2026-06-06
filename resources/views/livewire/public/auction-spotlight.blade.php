<div>
    @if($featuredAuction)
        <section class="max-w-7xl mx-auto px-6 py-24">
            <div class="relative rounded-[30px] md:rounded-[50px] overflow-hidden bg-zinc-900 aspect-auto md:aspect-[21/9] min-h-[400px] md:min-h-[500px] flex items-center group shadow-2xl">
                @if($featuredAuction->unit->mainImage)
                    <img src="{{ Storage::url($featuredAuction->unit->mainImage->url) }}" class="absolute inset-0 w-full h-full object-cover opacity-50 transition-transform duration-1000 group-hover:scale-110">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-r from-black via-black/40 to-transparent"></div>
                
                <div class="relative z-10 p-6 md:p-20 space-y-6 md:space-y-8 max-w-2xl animate-showroom-fade-up">
                    <div class="flex items-center gap-4">
                        <span class="bg-red-600 text-white text-[9px] md:text-[10px] font-black uppercase tracking-[0.3em] px-3 md:px-4 py-1.5 rounded-full flex items-center gap-2">
                            <span class="w-1.5 h-1.5 md:w-2 md:h-2 rounded-full bg-white animate-pulse"></span> LIVE NOW
                        </span>
                        <span class="text-white/40 text-[9px] md:text-[10px] font-bold uppercase tracking-widest">Lot #{{ $featuredAuction->lot_number }}</span>
                    </div>
                    
                    <div>
                        <h2 class="text-3xl md:text-7xl font-bold text-white tracking-tighter leading-tight">{{ $featuredAuction->unit->name }}</h2>
                        <p class="text-lg md:text-xl text-zinc-400 mt-2 md:mt-4 font-medium">{{ $featuredAuction->unit->category->name }} • Curator's Pick</p>
                    </div>

                    <div class="flex items-end gap-8 md:gap-12">
                        <div>
                            <p class="text-[9px] md:text-[10px] text-zinc-500 uppercase tracking-widest mb-1">Current Highest</p>
                            <p class="text-2xl md:text-3xl font-bold text-white tracking-tight">₱{{ number_format($featuredAuction->current_bid_php ?: $featuredAuction->starting_bid_php) }}</p>
                        </div>
                        <div class="h-10 md:h-12 w-px bg-white/10"></div>
                        <div>
                            <p class="text-[9px] md:text-[10px] text-zinc-500 uppercase tracking-widest mb-1">Ends In</p>
                            <div x-data="{
                                expiry: new Date('{{ $featuredAuction->end_at->toIso8601String() }}').getTime(),
                                remaining: '',
                                update() {
                                    let now = new Date().getTime();
                                    let diff = this.expiry - now;
                                    
                                    if (diff <= 0) {
                                        this.remaining = '00:00:00';
                                        return;
                                    }

                                    let h = Math.floor(diff / (1000 * 60 * 60));
                                    let m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                    let s = Math.floor((diff % (1000 * 60)) / 1000);
                                    
                                    this.remaining = [h, m, s].map(v => v.toString().padStart(2, '0')).join(':');
                                }
                            }" x-init="update(); setInterval(() => update(), 1000)">
                                <p class="text-2xl md:text-3xl font-bold text-brand-primary tabular-nums" x-text="remaining" wire:poll.5s>
                                    {{ now()->diff($featuredAuction->end_at)->format('%H:%I:%S') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-2 md:pt-4 relative z-20">
                        <a href="{{ route('auction.room', $featuredAuction) }}" wire:navigate class="w-full md:w-auto text-center bg-white text-black font-black uppercase tracking-widest text-[10px] md:text-[11px] px-8 md:px-12 py-4 md:py-5 rounded-2xl hover:scale-105 transition-all shadow-xl after:absolute after:inset-0 after:z-10">
                            <span class="relative z-20">Enter Auction Room</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>
