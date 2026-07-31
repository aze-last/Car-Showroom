<div 
    x-data="{ 
        open: @entangle('isOpen'),
        init() {
            window.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    this.open = true;
                    $wire.open();
                }
            });
        }
    }"
>
    <template x-teleport="body">
        <div 
            x-show="open" 
            style="display: none;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-zinc-950/60 backdrop-blur-md z-[9999] flex items-start justify-center pt-20 px-4"
        >
            <div 
                @click.away="open = false; $wire.close()"
                @keydown.escape.window="open = false; $wire.close()"
                class="bg-white rounded-3xl w-full max-w-2xl border border-zinc-200 shadow-2xl overflow-hidden flex flex-col max-h-[80vh]"
            >
                <!-- Search Input Bar -->
                <div class="p-4 border-b border-zinc-100 flex items-center gap-3">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-zinc-400 shrink-0" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="7"/><path d="M20 20L16.65 16.65" stroke-linecap="round"/></svg>
                    <input 
                        wire:model.live.debounce.250ms="query"
                        type="text" 
                        placeholder="Search vehicles, collectors, auctions, staff, inquiries... (Ctrl+K)"
                        class="w-full bg-transparent border-0 text-sm font-bold text-zinc-900 placeholder-zinc-400 focus:ring-0"
                        x-ref="searchInput"
                        x-init="$watch('open', value => { if (value) setTimeout(() => $refs.searchInput.focus(), 100) })"
                    />
                    <kbd class="hidden sm:inline-block px-2 py-1 text-[10px] font-mono text-zinc-400 bg-zinc-100 rounded-md border border-zinc-200">ESC</kbd>
                </div>

                <!-- Search Results -->
                <div class="overflow-y-auto p-4 space-y-6">
                    @if (strlen(trim($query)) < 2)
                        <div class="py-8 text-center text-zinc-400">
                            <p class="text-xs font-bold uppercase tracking-widest">Global Showroom Search</p>
                            <p class="text-[11px] font-medium mt-1">Type at least 2 characters to search across all dealership modules</p>
                        </div>
                    @else
                        @if ($units->isEmpty() && $customers->isEmpty() && $auctions->isEmpty() && $employees->isEmpty() && $inquiries->isEmpty())
                            <div class="py-8 text-center text-zinc-400">
                                <p class="text-xs font-bold uppercase tracking-widest">No Matches Found</p>
                                <p class="text-[11px] font-medium mt-1">No records match "<strong class="text-zinc-700">{{ $query }}</strong>"</p>
                            </div>
                        @else
                            {{-- Inventory Units --}}
                            @if ($units->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2 px-2">Vehicles ({{ $units->count() }})</p>
                                    <div class="space-y-1">
                                        @foreach ($units as $unit)
                                            <a href="{{ route('admin.units.edit', $unit) }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-zinc-50 transition-colors group">
                                                <div>
                                                    <p class="text-xs font-bold text-zinc-900 group-hover:text-black">{{ $unit->name }}</p>
                                                    <p class="text-[10px] text-zinc-400">#{{ substr($unit->public_id, -8) }} • {{ $unit->formattedPrice() }}</p>
                                                </div>
                                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider {{ $unit->isAvailable() ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-700' }}">
                                                    {{ $unit->status }}
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Customers --}}
                            @if ($customers->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2 px-2">Collectors ({{ $customers->count() }})</p>
                                    <div class="space-y-1">
                                        @foreach ($customers as $customer)
                                            <a href="{{ route('admin.customers.index', ['q' => $customer->email]) }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-zinc-50 transition-colors group">
                                                <div>
                                                    <p class="text-xs font-bold text-zinc-900 group-hover:text-black">{{ $customer->name }}</p>
                                                    <p class="text-[10px] text-zinc-400">{{ $customer->email }}</p>
                                                </div>
                                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider {{ $customer->is_blocked ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }}">
                                                    {{ $customer->is_blocked ? 'Blocked' : 'Active' }}
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Auctions --}}
                            @if ($auctions->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2 px-2">Auctions ({{ $auctions->count() }})</p>
                                    <div class="space-y-1">
                                        @foreach ($auctions as $auction)
                                            <a href="{{ route('admin.auctions.edit', $auction) }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-zinc-50 transition-colors group">
                                                <div>
                                                    <p class="text-xs font-bold text-zinc-900 group-hover:text-black">Lot #{{ $auction->lot_number }} • {{ $auction->unit->name }}</p>
                                                    <p class="text-[10px] text-zinc-400">Current Bid: ₱{{ number_format($auction->current_bid_php ?: $auction->starting_bid_php) }}</p>
                                                </div>
                                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider bg-zinc-100 text-zinc-700">
                                                    {{ $auction->status }}
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Employees --}}
                            @if ($employees->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2 px-2">Staff & Admins ({{ $employees->count() }})</p>
                                    <div class="space-y-1">
                                        @foreach ($employees as $emp)
                                            <a href="{{ route('admin.employees.index') }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-zinc-50 transition-colors group">
                                                <div>
                                                    <p class="text-xs font-bold text-zinc-900 group-hover:text-black">{{ $emp->name }}</p>
                                                    <p class="text-[10px] text-zinc-400">{{ $emp->email }}</p>
                                                </div>
                                                <span class="text-[9px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider bg-zinc-100 text-zinc-800">
                                                    {{ $emp->job_title }}
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Inquiries --}}
                            @if ($inquiries->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-2 px-2">Inquiries ({{ $inquiries->count() }})</p>
                                    <div class="space-y-1">
                                        @foreach ($inquiries as $inquiry)
                                            <a href="{{ route('admin.inquiries.index') }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-zinc-50 transition-colors group">
                                                <div>
                                                    <p class="text-xs font-bold text-zinc-900 group-hover:text-black">{{ $inquiry->name }}</p>
                                                    <p class="text-[10px] text-zinc-400 truncate max-w-xs">{{ $inquiry->message }}</p>
                                                </div>
                                                <span class="text-[9px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider bg-zinc-100 text-zinc-700">
                                                    {{ $inquiry->status }}
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endif
                </div>

                <!-- Footer Tips -->
                <div class="p-3 bg-zinc-50 border-t border-zinc-100 flex items-center justify-between text-[10px] font-medium text-zinc-400">
                    <span>Press <kbd class="font-mono bg-white px-1.5 py-0.5 rounded border">ESC</kbd> to close</span>
                    <span>Search vehicles, collectors, auctions & messages</span>
                </div>
            </div>
        </div>
    </template>
</div>
