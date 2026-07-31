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

                <!-- Deposit Security Check -->
                @php
                    $myDeposit = auth()->check() ? auth()->user()->bidDeposits()->where('auction_id', $auction->id)->first() : null;
                    $isStaff = auth()->check() && auth()->user()->isStaff();
                @endphp

                @if(!$isStaff && (!$myDeposit || $myDeposit->status !== 'approved'))
                    <div class="p-6 bg-amber-50/80 rounded-3xl border border-amber-200/80 space-y-3">
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-amber-600 shrink-0" stroke="currentColor" stroke-width="2.5"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <p class="text-xs font-bold text-amber-900 uppercase tracking-wider">
                                {{ $myDeposit?->status === 'pending' ? 'Deposit Pending Approval' : 'Security Deposit Required' }}
                            </p>
                        </div>
                        <p class="text-[11px] text-amber-800 font-medium leading-relaxed">
                            @if($myDeposit?->status === 'pending')
                                Your ₱{{ number_format($myDeposit->amount) }} deposit slip was submitted and is undergoing admin verification.
                            @else
                                A refundable security deposit of ₱{{ number_format($deposit_amount) }} is required to participate in live bidding.
                            @endif
                        </p>
                        @if(!$myDeposit || $myDeposit->status !== 'pending')
                            <button 
                                type="button"
                                x-on:click="$flux.modal('join-auction-modal').show()"
                                class="w-full mt-2 bg-black text-white text-[10px] font-black uppercase tracking-widest py-3.5 px-6 rounded-2xl hover:bg-zinc-800 transition-all shadow-md"
                            >
                                + Upload Security Deposit Slip
                            </button>
                        @endif
                    </div>
                @endif

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

    <!-- Deposit Upload Modal -->
    <flux:modal name="join-auction-modal" class="min-w-[22rem] md:min-w-[36rem] max-h-[85vh] overflow-y-auto rounded-[40px] border-none shadow-2xl">
        <form wire:submit.prevent="submitDeposit" class="space-y-8 p-4">
            <div class="space-y-6">
                <div>
                    <h2 class="text-xs font-black uppercase tracking-[0.3em] text-emerald-600 mb-2">Strict Collector Verification</h2>
                    <p class="text-sm font-medium text-zinc-500 leading-relaxed">
                        To participate in bidding for <strong class="text-black">{{ $auction->unit->name }}</strong>, complete identity verification and submit your refundable deposit.
                    </p>
                </div>

                <div class="p-6 bg-zinc-50 rounded-[30px] border border-zinc-100 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Security Deposit</p>
                        <h4 class="text-3xl font-bold text-black tracking-tighter">₱{{ number_format($deposit_amount) }}</h4>
                    </div>
                    <span class="text-[9px] font-black text-white bg-black px-4 py-2 rounded-full uppercase tracking-widest">100% Refundable</span>
                </div>

                <!-- 1. Full Name & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 block px-1">Collector Name</label>
                        <input wire:model="full_name" type="text" required placeholder="Full Legal Name" class="w-full h-12 bg-zinc-50 border-none rounded-2xl px-4 text-xs font-bold text-black focus:ring-2 focus:ring-black">
                        @error('full_name') <span class="text-red-500 text-[10px] font-bold uppercase tracking-widest">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 block px-1">Email Address</label>
                        <input wire:model="email" type="email" required placeholder="name@domain.com" class="w-full h-12 bg-zinc-50 border-none rounded-2xl px-4 text-xs font-bold text-black focus:ring-2 focus:ring-black">
                        @error('email') <span class="text-red-500 text-[10px] font-bold uppercase tracking-widest">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- 2. Mobile Phone & SMS Verification Code -->
                <div class="space-y-3 p-5 bg-zinc-50 rounded-3xl border border-zinc-100">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Mobile Phone Verification</label>
                        @if($phone_is_verified)
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                ✓ Verified
                            </span>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <input wire:model="phone" type="text" placeholder="+63 9XX XXX XXXX" class="flex-1 h-12 bg-white border-none rounded-2xl px-4 text-xs font-bold text-black focus:ring-2 focus:ring-black" {{ $phone_is_verified ? 'disabled' : '' }}>
                        @if(!$phone_is_verified)
                            <button type="button" wire:click="sendVerificationCode" class="px-5 bg-black text-white text-[10px] font-bold uppercase tracking-widest rounded-2xl hover:bg-zinc-800 transition-all shrink-0">
                                Send Code
                            </button>
                        @endif
                    </div>
                    @error('phone') <p class="text-red-500 text-[10px] font-bold uppercase tracking-widest">{{ $message }}</p> @enderror

                    @if($generated_otp && !$phone_is_verified)
                        <div class="pt-2 flex gap-2 animate-showroom-fade-up">
                            <input wire:model="verification_code" type="text" placeholder="Enter 6-digit SMS code" class="flex-1 h-12 bg-white border-none rounded-2xl px-4 text-xs font-mono font-bold text-black focus:ring-2 focus:ring-black">
                            <button type="button" wire:click="verifyCode" class="px-5 bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-widest rounded-2xl hover:bg-emerald-700 transition-all shrink-0">
                                Verify Code
                            </button>
                        </div>
                        @error('verification_code') <p class="text-red-500 text-[10px] font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                    @endif
                </div>

                <!-- 3. Delivery Address & Interactive Pinpoint Map -->
                <div class="space-y-3">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 block px-1">Physical Address & Map Pinpoint</label>
                    <input wire:model="address" type="text" required placeholder="Full Street Address / City / Postal Code" class="w-full h-12 bg-zinc-50 border-none rounded-2xl px-4 text-xs font-bold text-black focus:ring-2 focus:ring-black">
                    @error('address') <p class="text-red-500 text-[10px] font-bold uppercase tracking-widest">{{ $message }}</p> @enderror

                    <div 
                        wire:ignore
                        x-data="{
                            map: null,
                            marker: null,
                            initMap() {
                                if (typeof L === 'undefined') {
                                    setTimeout(() => this.initMap(), 150);
                                    return;
                                }
                                const container = this.$refs.mapContainer;
                                if (!container || container._leaflet_id) return;
                                
                                const lat = @js($latitude) || 14.5995;
                                const lng = @js($longitude) || 120.9842;
                                
                                this.map = L.map(container, {
                                    center: [lat, lng],
                                    zoom: 14,
                                    zoomControl: true
                                });
                                
                                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    maxZoom: 19,
                                    attribution: '&copy; OpenStreetMap contributors'
                                }).addTo(this.map);
                                
                                this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);
                                
                                this.marker.on('dragend', (e) => {
                                    const pos = this.marker.getLatLng();
                                    $wire.updateCoordinates(pos.lat, pos.lng);
                                });
                                
                                this.map.on('click', (e) => {
                                    this.marker.setLatLng(e.latlng);
                                    $wire.updateCoordinates(e.latlng.lat, e.latlng.lng);
                                });

                                const observer = new IntersectionObserver((entries) => {
                                    entries.forEach(entry => {
                                        if (entry.isIntersecting && this.map) {
                                            setTimeout(() => this.map.invalidateSize(), 100);
                                            setTimeout(() => this.map.invalidateSize(), 300);
                                            setTimeout(() => this.map.invalidateSize(), 600);
                                        }
                                    });
                                }, { threshold: 0.1 });
                                observer.observe(container);
                            }
                        }"
                        x-init="initMap()"
                        class="space-y-1"
                    >
                        <div x-ref="mapContainer" class="h-52 w-full rounded-2xl overflow-hidden border border-zinc-200 relative z-0 shadow-inner" style="min-height: 208px; background-color: #e5e7eb;"></div>
                        <div class="flex justify-between items-center px-1 text-[9px] font-mono text-zinc-400">
                            <span>📍 GPS: {{ number_format($latitude, 5) }}, {{ number_format($longitude, 5) }}</span>
                            <span class="font-sans font-bold text-zinc-400 uppercase tracking-wider">Drag marker or tap map</span>
                        </div>
                    </div>
                </div>

                <!-- 4. Deposit Slip Receipt Upload -->
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest px-2">Upload GCash / Bank Receipt</label>
                    <div class="relative group">
                        <input type="file" wire:model="proof_image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="p-8 border-2 border-dashed border-zinc-200 rounded-[30px] text-center group-hover:border-black transition-all bg-white relative">
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
                                <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8 mx-auto text-zinc-300 group-hover:text-black mb-2 transition-transform group-hover:-translate-y-1" stroke="currentColor" stroke-width="2"><path d="M12 16V6M12 6L8 10M12 6L16 10" stroke-linecap="round"/><path d="M5 15V17A2 2 0 0 0 7 19H17A2 2 0 0 0 19 17V15" stroke-linecap="round"/></svg>
                                <p class="text-xs font-bold text-zinc-400 group-hover:text-black transition-colors">Tap to select or drag document</p>
                            @endif
                        </div>
                    </div>
                    @error('proof_image') <span class="text-red-600 text-[10px] font-black uppercase tracking-widest px-2">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex gap-4 pt-4 border-t border-zinc-100">
                <flux:modal.close>
                    <button type="button" class="flex-1 bg-zinc-50 text-zinc-500 font-black uppercase tracking-widest text-[10px] py-5 px-8 rounded-2xl">Cancel</button>
                </flux:modal.close>
                <flux:spacer />
                <button type="submit" wire:loading.attr="disabled" wire:target="submitDeposit" class="flex-2 bg-black text-white font-black uppercase tracking-widest text-[10px] py-5 px-12 rounded-2xl shadow-xl shadow-black/10 active:scale-95 disabled:opacity-50 transition-all">
                    <span wire:loading.remove wire:target="submitDeposit">Submit Verification</span>
                    <span wire:loading wire:target="submitDeposit">Processing...</span>
                </button>
            </div>
        </form>
    </flux:modal>
</div>
