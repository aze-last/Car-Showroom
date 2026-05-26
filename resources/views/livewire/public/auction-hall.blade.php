<div class="px-6 md:px-container-padding py-12 md:py-16 space-y-stack-lg max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
        <div>
            <p class="font-headline-sm text-[12px] text-zinc-500 uppercase tracking-widest mb-2">Curated Collection</p>
            <h2 class="text-5xl font-bold tracking-tight text-black">Auction Hall</h2>
        </div>
        <div class="flex gap-4">
            <button class="flex items-center gap-2 px-6 py-3 rounded-full border border-zinc-200 text-black font-semibold text-sm hover:bg-zinc-50 transition-all duration-300">
                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                Filter
            </button>
            <button class="flex items-center gap-2 px-6 py-3 rounded-full bg-black text-white font-semibold text-sm hover:opacity-90 transition-all duration-300">
                <span class="material-symbols-outlined text-[18px]">history</span>
                Past Results
            </button>
        </div>
    </div>

    <!-- Featured Live Auction Hero -->
    @if($featuredAuction)
    <section class="relative w-full h-[600px] rounded-[32px] overflow-hidden group shadow-xl">
        <div class="absolute inset-0 bg-zinc-100">
            @if($featuredAuction->unit->mainImage)
                <img src="{{ Storage::url($featuredAuction->unit->mainImage->url) }}" alt="{{ $featuredAuction->unit->name }}" class="w-full h-full object-cover transition-transform duration-[2s] group-hover:scale-105">
            @endif
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-full p-8 md:p-12 flex flex-col md:flex-row justify-between items-end gap-8">
            <div class="text-white max-w-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-600 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
                    </span>
                    <span class="text-[12px] font-bold text-white uppercase tracking-widest bg-black/50 px-3 py-1 rounded-full backdrop-blur-sm border border-white/20">Live Now</span>
                    <span class="text-[12px] font-bold text-white/80 bg-black/50 px-3 py-1 rounded-full backdrop-blur-sm border border-white/20">Lot {{ $featuredAuction->lot_number }}</span>
                </div>
                <h3 class="text-4xl md:text-5xl font-bold text-white mb-2">{{ $featuredAuction->unit->name }}</h3>
                <p class="text-lg text-white/80 line-clamp-2">{{ $featuredAuction->unit->curator_note ?? 'A legendary example of automotive history.' }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 min-w-[320px]">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <p class="text-[10px] text-white/70 uppercase tracking-wider mb-1">Current Bid</p>
                        <p class="text-3xl font-bold text-white">₱{{ number_format($featuredAuction->current_bid_php ?: $featuredAuction->starting_bid_php) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-white/70 uppercase tracking-wider mb-1">Ends In</p>
                        <p class="text-xl font-bold text-white tabular-nums" wire:poll.1s>
                            @php
                                $diff = now()->diff($featuredAuction->end_at);
                                echo $diff->format('%H:%I:%S');
                            @endphp
                        </p>
                    </div>
                </div>
                <a href="{{ route('auction.room', $featuredAuction->id) }}" class="w-full bg-white text-black rounded-full py-4 font-bold text-sm block text-center hover:bg-zinc-100 transition-colors duration-300">
                    Enter Live Auction
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- Auction Grid -->
    <section>
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-bold text-black">Active Lots</h3>
            <div class="flex gap-2">
                <span class="text-[11px] font-semibold text-zinc-500 uppercase tracking-widest">Sort: Ending Soon</span>
                <span class="material-symbols-outlined text-[16px] text-zinc-500">expand_more</span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($activeLots as $auction)
            <a href="{{ route('auction.room', $auction->id) }}" class="block group">
                <article class="bg-white rounded-[32px] border border-zinc-100 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full">
                    <div class="relative h-64 bg-zinc-100">
                        @if($auction->unit->mainImage)
                            <img src="{{ Storage::url($auction->unit->mainImage->url) }}" alt="{{ $auction->unit->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @endif
                        <div class="absolute top-4 left-4">
                            @if($auction->status === 'live')
                                <span class="text-[10px] font-bold text-black uppercase tracking-widest bg-white/90 px-3 py-1.5 rounded-full backdrop-blur-sm border border-zinc-100 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-pulse"></span> Live
                                </span>
                            @else
                                <span class="text-[10px] font-bold text-black uppercase tracking-widest bg-white/90 px-3 py-1.5 rounded-full backdrop-blur-sm border border-zinc-100 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span> Scheduled
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-[10px] font-semibold text-zinc-500 block mb-1">Lot {{ $auction->lot_number }}</span>
                                <h4 class="text-lg font-bold text-black leading-tight">{{ $auction->unit->name }}</h4>
                            </div>
                            <button type="button" class="w-8 h-8 rounded-full bg-zinc-50 flex items-center justify-center border border-zinc-100 hover:bg-zinc-100 transition-colors" onclick="event.preventDefault();">
                                <span class="material-symbols-outlined text-[16px] text-black">bookmark_border</span>
                            </button>
                        </div>
                        <div class="mt-auto space-y-4 pt-4 border-t border-zinc-50">
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-[10px] text-zinc-500 uppercase tracking-wider mb-1">
                                        {{ $auction->status === 'live' ? 'Highest Bid' : 'Starting Bid' }}
                                    </p>
                                    <p class="text-xl font-bold text-black">₱{{ number_format($auction->current_bid_php ?: $auction->starting_bid_php) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-zinc-500 uppercase tracking-wider mb-1">
                                        {{ $auction->status === 'live' ? 'Time Left' : 'Starts In' }}
                                    </p>
                                    <p class="text-sm font-bold {{ $auction->status === 'live' && $auction->end_at->diffInMinutes(now()) < 60 ? 'text-red-600' : 'text-black' }} tabular-nums">
                                        @if($auction->status === 'live')
                                            {{ $auction->end_at->diffForHumans(['parts' => 2, 'short' => true]) }}
                                        @else
                                            {{ $auction->start_at->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($auction->isReserveMet())
                                    <span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span>
                                    <span class="text-[11px] text-emerald-600 font-bold uppercase tracking-wider">Reserve Met</span>
                                @else
                                    <span class="material-symbols-outlined text-[14px] text-zinc-400">pending</span>
                                    <span class="text-[11px] text-zinc-500 font-bold uppercase tracking-wider">Reserve Not Met</span>
                                @endif
                                <span class="mx-2 text-zinc-200 text-[10px]">â€¢</span>
                                <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">{{ $auction->bids_count ?? 0 }} Bids</span>
                            </div>
                        </div>
                    </div>
                </article>
            </a>
            @endforeach
        </div>
        <div class="mt-8">
            {{ $activeLots->links() }}
        </div>
    </section>
</div>
