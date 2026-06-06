<div class="px-6 md:px-container-padding py-12 md:py-24 max-w-7xl mx-auto space-y-24">
    <!-- 1. Hero Spotlight (Featured Live Auction) -->
    @if($featuredAuction)
        <section class="showroom-item relative rounded-[30px] md:rounded-[50px] overflow-hidden bg-zinc-900 aspect-auto md:aspect-[21/9] min-h-[400px] md:min-h-[500px] flex items-center group shadow-2xl">
            @if($featuredAuction->unit->mainImage)
                <img src="{{ Storage::url($featuredAuction->unit->mainImage->url) }}" class="hero-parallax-img absolute inset-0 w-full h-full object-cover opacity-50 transition-transform duration-1000 group-hover:scale-110">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-r from-black via-black/40 to-transparent"></div>
            
            <div class="relative z-10 p-6 md:p-20 space-y-6 md:space-y-8 max-w-2xl">
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
                            <p class="text-2xl md:text-3xl font-bold text-emerald-400 tabular-nums" x-text="remaining" wire:poll.5s>
                                {{ now()->diff($featuredAuction->end_at)->format('%H:%I:%S') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-2 md:pt-4">
                    <a href="{{ route('auction.room', $featuredAuction) }}" wire:navigate class="w-full md:w-auto text-center bg-white text-black font-black uppercase tracking-widest text-[10px] md:text-[11px] px-8 md:px-12 py-4 md:py-5 rounded-2xl hover:scale-105 transition-all shadow-xl">
                        Enter Auction Room
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- 2. Upcoming & Active Lots (Bento Grid) -->
    <section class="space-y-8 md:space-y-12">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 md:gap-0">
            <div>
                <h3 class="text-3xl md:text-4xl font-bold text-black tracking-tighter">Auction Registry</h3>
                <p class="text-zinc-500 font-medium mt-1 md:mt-2 uppercase text-[10px] md:text-xs tracking-widest">Available Lots & Upcoming Schedules</p>
            </div>
            <div class="hidden md:flex gap-4">
                 {{-- Filters could go here --}}
            </div>
        </div>

        <div class="showroom-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @forelse($activeLots as $lot)
                <article wire:key="lot-{{ $lot->id }}" class="showroom-item animate-showroom-fade-up group bg-white rounded-[30px] md:rounded-[40px] border border-zinc-100 overflow-hidden hover:shadow-2xl transition-all duration-500">
                    <div class="relative h-48 md:h-64 overflow-hidden bg-zinc-50">
                        @if($lot->unit->mainImage)
                            <img src="{{ Storage::url($lot->unit->mainImage->url) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        @endif
                        <div class="absolute top-4 md:top-6 left-4 md:left-6">
                            <span class="bg-white/90 backdrop-blur-md text-black text-[8px] md:text-[9px] font-black uppercase tracking-[0.2em] px-3 md:px-4 py-1 md:py-1.5 rounded-full shadow-lg">
                                Lot #{{ $lot->lot_number }}
                            </span>
                        </div>
                        <div class="absolute bottom-4 md:bottom-6 right-4 md:right-6">
                            @if($lot->status === 'live')
                                <span class="bg-emerald-500 text-white text-[8px] md:text-[9px] font-black uppercase tracking-[0.2em] px-3 md:px-4 py-1 md:py-1.5 rounded-full shadow-lg">ACTIVE</span>
                            @else
                                <span class="bg-zinc-900 text-white text-[8px] md:text-[9px] font-black uppercase tracking-[0.2em] px-3 md:px-4 py-1 md:py-1.5 rounded-full shadow-lg">SCHEDULED</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-6 md:p-10 space-y-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-xl md:text-2xl font-bold text-black tracking-tighter">{{ $lot->unit->name }}</h4>
                                <p class="text-[9px] md:text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">{{ $lot->unit->category->name }} • {{ $lot->unit->year }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] md:text-[10px] text-zinc-400 uppercase tracking-widest mb-1">Starting</p>
                                <p class="text-base md:text-lg font-bold text-black">₱{{ number_format($lot->starting_bid_php) }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 py-4 md:py-6 border-y border-zinc-50">
                            <div>
                                <p class="text-[8px] md:text-[9px] text-zinc-400 uppercase tracking-widest mb-1">Bidders</p>
                                <p class="text-xs md:text-sm font-bold text-black">{{ $lot->bids_count }} Joined</p>
                            </div>
                            <div>
                                <p class="text-[8px] md:text-[9px] text-zinc-400 uppercase tracking-widest mb-1">Schedule</p>
                                <p class="text-xs md:text-sm font-bold text-black">{{ $lot->start_at->format('M d, H:i') }}</p>
                            </div>
                        </div>

                        <div class="pt-2 md:pt-4">
                            @php
                                $userDeposit = auth()->check() ? auth()->user()->bidDeposits()->where('auction_id', $lot->id)->first() : null;
                            @endphp

                            @if($userDeposit && $userDeposit->status === 'approved')
                                <a href="{{ route('auction.room', $lot) }}" wire:navigate class="w-full inline-flex justify-center bg-black text-white font-black uppercase tracking-widest text-[10px] py-4 rounded-2xl hover:bg-zinc-800 transition-all">
                                    Enter Room
                                </a>
                            @elseif($userDeposit && $userDeposit->status === 'pending')
                                <button disabled class="w-full bg-zinc-100 text-zinc-400 font-black uppercase tracking-widest text-[10px] py-4 rounded-2xl cursor-not-allowed">
                                    Approval Pending
                                </button>
                            @else
                                <button 
                                    wire:click="openJoinModal({{ $lot->id }})" 
                                    wire:loading.attr="disabled"
                                    wire:target="openJoinModal({{ $lot->id }})"
                                    x-on:click="$flux.modal('join-auction-modal').show()"
                                    class="w-full bg-emerald-500 text-white font-black uppercase tracking-widest text-[10px] py-4 rounded-2xl hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20"
                                >
                                    <span wire:loading.remove wire:target="openJoinModal({{ $lot->id }})">+ Join Auction</span>
                                    <span wire:loading wire:target="openJoinModal({{ $lot->id }})">Loading...</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-24 md:py-48 text-center bg-white rounded-[30px] md:rounded-[50px] border border-zinc-100 border-dashed">
                    <p class="text-zinc-300 font-bold uppercase tracking-[0.4em] md:tracking-[0.5em] text-xs md:text-sm">No active auctions at the moment</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $activeLots->links() }}
        </div>
    </section>

    <!-- 3. Join Modal (Deposit Workflow) -->
    <flux:modal name="join-auction-modal" class="min-w-[22rem] md:min-w-[32rem] rounded-[40px] border-none shadow-2xl">
        <form wire:submit.prevent="submitDeposit" class="space-y-8 p-4">
            @if (session()->has('status'))
                <div class="p-6 bg-emerald-50 border border-emerald-100 rounded-[30px] animate-showroom-fade-up">
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest text-center">
                        {{ session('status') }}
                    </p>
                </div>
            @endif

            <div class="space-y-6">
                <div>
                    <h2 class="text-xs font-black uppercase tracking-[0.3em] text-emerald-600 mb-2">Auction Entry</h2>
                    <p class="text-sm font-medium text-zinc-500 leading-relaxed">
                        To participate in the bidding for <strong class="text-black">{{ $selectedAuction?->unit->name }}</strong>, a refundable security deposit is required.
                    </p>
                </div>

                <div class="p-8 bg-zinc-50 rounded-[30px] border border-zinc-100">
                    <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-4">Required Deposit</p>
                    <div class="flex justify-between items-end">
                        <h4 class="text-4xl font-bold text-black tracking-tighter">₱{{ number_format($deposit_amount) }}</h4>
                        <span class="text-[9px] font-black text-white bg-black px-3 py-1 rounded-full uppercase tracking-widest">Fully Refundable</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest px-2">Upload GCash / Bank Receipt</label>
                    <div class="relative group">
                        <input type="file" wire:model="proof_image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="p-10 border-2 border-dashed border-zinc-200 rounded-[30px] text-center group-hover:border-black transition-all bg-white relative">
                            <!-- Loading Overlay for File Upload -->
                            <div wire:loading wire:target="proof_image" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 flex flex-col items-center justify-center rounded-[30px]">
                                <div class="w-8 h-8 border-4 border-zinc-200 border-t-black rounded-full animate-spin"></div>
                                <p class="text-[10px] font-black uppercase tracking-widest mt-4">Uploading Document...</p>
                            </div>

                            @if($proof_image)
                                <div class="flex flex-col items-center gap-2">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8 text-emerald-500" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17L4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span class="text-black text-xs font-bold">{{ $proof_image->getClientOriginalName() }}</span>
                                </div>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" class="h-10 w-10 mx-auto text-zinc-300 group-hover:text-black mb-4 transition-transform group-hover:-translate-y-1" stroke="currentColor" stroke-width="2"><path d="M12 16V6M12 6L8 10M12 6L16 10" stroke-linecap="round"/><path d="M5 15V17A2 2 0 0 0 7 19H17A2 2 0 0 0 19 17V15" stroke-linecap="round"/></svg>
                                <p class="text-xs font-bold text-zinc-400 group-hover:text-black transition-colors">Tap to select or drag document</p>
                            @endif
                        </div>
                    </div>
                    @error('proof_image') <span class="text-red-600 text-[10px] font-black uppercase tracking-widest px-2">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex gap-4">
                <flux:modal.close>
                    <button type="button" class="flex-1 bg-zinc-50 text-zinc-500 font-black uppercase tracking-widest text-[10px] py-5 px-8 rounded-2xl">Cancel</button>
                </flux:modal.close>
                <flux:spacer />
                <button type="submit" wire:loading.attr="disabled" wire:target="submitDeposit" class="flex-2 bg-black text-white font-black uppercase tracking-widest text-[10px] py-5 px-12 rounded-2xl shadow-xl shadow-black/10 active:scale-95 disabled:opacity-50 transition-all">
                    <span wire:loading.remove wire:target="submitDeposit">Submit Entry</span>
                    <span wire:loading wire:target="submitDeposit">Processing...</span>
                </button>
            </div>
        </form>
    </flux:modal>
</div>
