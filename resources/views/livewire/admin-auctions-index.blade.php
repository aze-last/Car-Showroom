<section class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-[12px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Curator Tools</p>
            <h2 class="text-3xl font-bold text-black">Auction Registry</h2>
        </div>
        <a href="{{ route('admin.auctions.create') }}" class="bg-black text-white font-bold text-[12px] uppercase tracking-widest px-8 py-4 rounded-2xl hover:opacity-90 transition-all ambient-shadow flex items-center gap-2">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
            Schedule Lot
        </a>
    </div>

    <!-- Analytical Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl p-6 border border-zinc-100 shadow-sm group hover:border-black transition-colors duration-300">
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Active Floor Value</p>
            <div class="flex items-end justify-between">
                <h3 class="text-2xl font-bold text-black tracking-tight">₱{{ number_format($stats['active_value'] / 1000000, 1) }}M</h3>
                <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Live Now</span>
            </div>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-zinc-100 shadow-sm group hover:border-black transition-colors duration-300">
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Total Bid volume</p>
            <div class="flex items-end justify-between">
                <h3 class="text-2xl font-bold text-black tracking-tight">{{ number_format($stats['total_bids']) }}</h3>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Historical</span>
            </div>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-zinc-100 shadow-sm group hover:border-black transition-colors duration-300">
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Conversion Rate</p>
            <div class="flex items-end justify-between">
                <h3 class="text-2xl font-bold text-black tracking-tight">{{ round($stats['success_rate']) }}%</h3>
                <span class="text-[10px] font-bold text-black uppercase tracking-widest">Efficiency</span>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl text-sm font-bold flex items-center gap-3 animate-showroom-fade-up">
            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17L4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-transparent md:bg-white rounded-[32px] md:border md:border-zinc-100 md:shadow-sm overflow-hidden">
        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 gap-6 md:hidden">
            @forelse ($auctions as $auction)
                <div wire:key="admin-auction-card-{{ $auction->id }}" class="bg-white rounded-[32px] border border-gallery-outline/20 ambient-shadow p-6 flex flex-col gap-6">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-20 rounded-2xl overflow-hidden bg-zinc-100 border border-gallery-outline/10 shadow-sm shrink-0">
                            @if($auction->unit->mainImage)
                                <img src="{{ Storage::url($auction->unit->mainImage->url) }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-bold text-zinc-400">Lot #{{ $auction->lot_number }}</span>
                                @php
                                    $statusClasses = [
                                        'live' => 'bg-red-50 text-red-600 border-red-100',
                                        'scheduled' => 'bg-zinc-50 text-zinc-600 border-zinc-100',
                                        'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'cancelled' => 'bg-orange-50 text-orange-600 border-orange-100',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[8px] font-bold uppercase tracking-widest border {{ $statusClasses[$auction->status] ?? $statusClasses['scheduled'] }}">
                                    {{ $auction->status }}
                                </span>
                            </div>
                            <h3 class="font-bold text-black text-lg tracking-tight truncate">{{ $auction->unit->name }}</h3>
                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">{{ $auction->unit->year }} • {{ $auction->unit->category->name }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 py-4 border-y border-gallery-outline/5">
                        <div>
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest block mb-1">Current Bid</span>
                            <span class="text-sm font-bold text-black">₱{{ number_format($auction->current_bid_php ?: $auction->starting_bid_php) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest block mb-1">Ends At</span>
                            <span class="text-sm font-bold text-black">{{ $auction->end_at->format('M d, H:i') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.auctions.edit', $auction->id) }}" class="flex h-12 px-6 items-center justify-center rounded-xl border border-gallery-outline/30 text-black font-bold text-[10px] uppercase tracking-widest hover:bg-zinc-50 transition-all">
                            Edit Lot
                        </a>
                        <button 
                            wire:click="delete({{ $auction->id }})" 
                            wire:confirm="Permanentely delete this auction lot? This action cannot be undone."
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-400 hover:text-red-600 transition-all border border-red-100"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center bg-white rounded-[32px] border border-gallery-outline/20">
                    <p class="text-sm font-bold text-zinc-400 uppercase tracking-widest">No auction lots registered</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50/50">
                        <th class="px-6 py-5 text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Lot</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Vehicle</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Status</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Current Bid</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Schedule</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50">
                    @foreach ($auctions as $auction)
                        <tr class="hover:bg-zinc-50/30 transition-colors group">
                            <td class="px-6 py-5 text-sm font-bold text-black">#{{ $auction->lot_number }}</td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-16 rounded-xl overflow-hidden bg-zinc-100">
                                        @if($auction->unit->mainImage)
                                            <img src="{{ Storage::url($auction->unit->mainImage->url) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-black">{{ $auction->unit->name }}</p>
                                        <p class="text-[10px] text-zinc-400 uppercase tracking-widest">{{ $auction->unit->year }} • {{ $auction->unit->category->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @php
                                    $statusClasses = [
                                        'live' => 'bg-red-50 text-red-600 border-red-100',
                                        'scheduled' => 'bg-zinc-50 text-zinc-600 border-zinc-100',
                                        'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'cancelled' => 'bg-orange-50 text-orange-600 border-orange-100',
                                    ];
                                @endphp
                                <span class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest border {{ $statusClasses[$auction->status] ?? $statusClasses['scheduled'] }}">
                                    {{ $auction->status }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <p class="text-sm font-bold text-black">₱{{ number_format($auction->current_bid_php ?: $auction->starting_bid_php) }}</p>
                                <p class="text-[10px] text-zinc-400 uppercase tracking-widest">Reserve: ₱{{ number_format($auction->reserve_price_php) }}</p>
                            </td>
                            <td class="px-6 py-5">
                                <p class="text-[11px] font-bold text-black">{{ $auction->start_at->format('M d, H:i') }}</p>
                                <p class="text-[10px] text-zinc-400 uppercase tracking-widest">Ends: {{ $auction->end_at->format('M d, H:i') }}</p>
                            </td>
                            <td class="px-6 py-5 text-right space-x-2">
                                <a href="{{ route('admin.auctions.edit', $auction->id) }}" class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-zinc-50 text-zinc-400 hover:text-black hover:bg-white hover:ambient-shadow transition-all border border-zinc-100">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                                <button 
                                    wire:click="delete({{ $auction->id }})" 
                                    wire:confirm="Permanentely delete this auction lot? This action cannot be undone."
                                    class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-red-50 text-red-400 hover:text-red-600 hover:bg-white hover:ambient-shadow transition-all border border-red-100"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        {{ $auctions->links() }}
    </div>
</section>
