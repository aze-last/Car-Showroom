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

                <!-- Pricing -->
                <div>
                    <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Starting Bid (₱)</label>
                    <input type="number" wire:model="starting_bid_php" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm focus:ring-2 focus:ring-black transition-all">
                    @error('starting_bid_php') <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Reserve Price (₱)</label>
                    <input type="number" wire:model="reserve_price_php" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm focus:ring-2 focus:ring-black transition-all">
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
