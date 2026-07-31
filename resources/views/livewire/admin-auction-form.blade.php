<section class="max-w-4xl space-y-8">
    <div>
        <p class="text-[12px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Lot Configuration</p>
        <h2 class="text-3xl font-bold text-black">{{ $isEdit ? 'Edit Auction Room' : 'Schedule New Lot' }}</h2>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-[32px] border border-zinc-100 shadow-sm p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Vehicle Selection -->
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Select Vehicle</label>
                    <select wire:model="unit_id" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm focus:ring-2 focus:ring-black transition-all">
                        <option value="">Choose a unit...</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->year }})</option>
                        @endforeach
                    </select>
                    @error('unit_id') <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>

                <!-- Lot Number -->
                <div>
                    <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Lot Number</label>
                    <input type="text" wire:model="lot_number" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm focus:ring-2 focus:ring-black transition-all" placeholder="e.g. 042">
                    @error('lot_number') <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Initial Status</label>
                    <select wire:model.live="status" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm focus:ring-2 focus:ring-black transition-all">
                        <option value="scheduled">Scheduled</option>
                        <option value="live">Live Now</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    @error('status') <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>

                <!-- Featured Toggle -->
                <div class="flex items-center gap-4 bg-zinc-50 p-6 rounded-2xl md:col-span-2">
                    <div class="flex-grow">
                        <p class="text-sm font-bold text-black">Feature this Auction</p>
                        <p class="text-[10px] text-zinc-400 font-medium uppercase tracking-widest mt-1">Highlighted on the public Auction Hall hero</p>
                    </div>
                    <input type="checkbox" wire:model="is_featured" class="h-6 w-6 rounded-lg border-zinc-200 text-black focus:ring-black">
                </div>

                @if ($unit_id && ($unitForSuggestion = \App\Models\Unit::find($unit_id)))
                    @php
                        $readiness = $unitForSuggestion->auctionReadiness();
                    @endphp
                    @if ($readiness['is_candidate'])
                        <div class="md:col-span-2 p-5 bg-amber-50 rounded-2xl border border-amber-200/80 flex items-start gap-3.5 shadow-xs" data-test="auction-pricing-suggestion-banner">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" stroke="currentColor" stroke-width="2.5"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <div class="space-y-1">
                                <p class="text-xs font-bold text-amber-900 uppercase tracking-wider">
                                    Suggested Auction Pricing (Pre-filled): Reserve ₱{{ number_format($readiness['suggested_reserve_php']) }} | Start Bid ₱{{ number_format($readiness['suggested_starting_bid_php']) }}
                                </p>
                                <p class="text-[11px] text-amber-800/90 font-medium leading-relaxed">
                                    Based on {{ $readiness['days_listed'] }} days sitting listed ({{ $readiness['benchmark_comparison'] ?? 'no category benchmark' }}).
                                    <span class="font-bold underline">You can override these values anytime.</span>
                                </p>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Pricing -->
                <div>
                    <div class="flex items-center gap-1.5 mb-2">
                        <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-widest">Starting Bid (₱)</label>
                        <flux:tooltip content="The minimum initial amount required for collectors to place an opening bid on this vehicle lot." position="top">
                            <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5 text-zinc-400 hover:text-black transition-colors cursor-pointer" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4m0-4h.01" stroke-linecap="round"/></svg>
                        </flux:tooltip>
                    </div>
                    <input 
                        type="text" 
                        x-data="{ 
                            raw: @entangle('starting_bid_php'),
                            format(val) {
                                if (val === null || val === '') return '';
                                return val.toString().replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                            }
                        }" 
                        x-on:input="
                            let clean = $event.target.value.replace(/,/g, '').replace(/\D/g, '');
                            raw = clean === '' ? 0 : parseInt(clean);
                            $event.target.value = format(clean);
                        "
                        x-init="$el.value = format(raw)"
                        class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm focus:ring-2 focus:ring-black transition-all"
                    >
                    @error('starting_bid_php') <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <div class="flex items-center gap-1.5 mb-2">
                        <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-widest">Reserve Price (₱)</label>
                        <flux:tooltip content="The confidential minimum threshold price required for the seller to successfully complete the sale." position="top">
                            <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5 text-zinc-400 hover:text-black transition-colors cursor-pointer" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4m0-4h.01" stroke-linecap="round"/></svg>
                        </flux:tooltip>
                    </div>
                    <input 
                        type="text" 
                        x-data="{ 
                            raw: @entangle('reserve_price_php'),
                            format(val) {
                                if (val === null || val === '') return '';
                                return val.toString().replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                            }
                        }" 
                        x-on:input="
                            let clean = $event.target.value.replace(/,/g, '').replace(/\D/g, '');
                            raw = clean === '' ? 0 : parseInt(clean);
                            $event.target.value = format(clean);
                        "
                        x-init="$el.value = format(raw)"
                        class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm focus:ring-2 focus:ring-black transition-all"
                    >
                    @error('reserve_price_php') <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>

                <!-- Timing -->
                <div>
                    <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Start Date & Time</label>
                    <input 
                        type="datetime-local" 
                        wire:model="start_at" 
                        @if($status === 'live') disabled @endif
                        class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm focus:ring-2 focus:ring-black transition-all {{ $status === 'live' ? 'opacity-50 cursor-not-allowed' : '' }}"
                    >
                    @if($status === 'live')
                        <p class="mt-2 text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Live Transition: Start time synchronized to now</p>
                    @endif
                    @error('start_at') <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-2">End Date & Time</label>
                    <input type="datetime-local" wire:model="end_at" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm focus:ring-2 focus:ring-black transition-all">
                    @error('end_at') <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.auctions.index') }}" class="text-[12px] font-bold text-zinc-400 uppercase tracking-widest hover:text-black transition-colors">Cancel</a>
            <button type="submit" class="bg-black text-white font-bold text-[12px] uppercase tracking-widest px-12 py-5 rounded-2xl hover:opacity-90 transition-all ambient-shadow">
                {{ $isEdit ? 'Update Auction' : 'Schedule Lot' }}
            </button>
        </div>
    </form>
</section>
