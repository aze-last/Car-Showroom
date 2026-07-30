<section x-data="{ showConfirmModal: false, targetCustomer: null }" class="space-y-8 animate-showroom-fade-up">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
        <div>
            <p class="text-[12px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Collector Registry</p>
            <h2 class="text-3xl font-bold text-black">Customer Management & Security</h2>
        </div>
    </div>

    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl text-sm font-bold flex items-center gap-3 animate-showroom-fade-up">
            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17L4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-100 text-red-700 px-6 py-4 rounded-2xl text-sm font-bold flex items-center gap-3 animate-showroom-fade-up">
            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-[32px] p-6 border border-zinc-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Total Collectors</p>
                <p class="text-3xl font-bold text-black tracking-tight">{{ number_format($totalCustomers) }}</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-zinc-50 text-black flex items-center justify-center font-bold">
                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-[32px] p-6 border border-zinc-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Active Accounts</p>
                <p class="text-3xl font-bold text-emerald-600 tracking-tight">{{ number_format($activeCount) }}</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-[32px] p-6 border border-zinc-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Suspended / Blocked</p>
                <p class="text-3xl font-bold text-red-600 tracking-tight">{{ number_format($blockedCount) }}</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center font-bold">
                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
            </div>
        </div>
    </div>

    <!-- Controls Bar (Search + Filter) -->
    <div class="bg-white rounded-[32px] border border-zinc-100 shadow-sm p-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="relative w-full md:w-96">
            <svg viewBox="0 0 24 24" fill="none" class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-zinc-400" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="7"/><path d="M20 20L16.65 16.65" stroke-linecap="round"/></svg>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Search collector name or email..." 
                class="w-full bg-zinc-50 border-none rounded-2xl pl-11 pr-5 py-3 font-bold text-sm focus:ring-2 focus:ring-black transition-all"
            >
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto overflow-x-auto no-scrollbar">
            <button 
                wire:click="$set('statusFilter', 'all')"
                class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all {{ $statusFilter === 'all' ? 'bg-black text-white shadow-md' : 'bg-zinc-100 text-zinc-500 hover:text-black' }}"
            >
                All ({{ $totalCustomers }})
            </button>
            <button 
                wire:click="$set('statusFilter', 'active')"
                class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all {{ $statusFilter === 'active' ? 'bg-emerald-600 text-white shadow-md' : 'bg-zinc-100 text-zinc-500 hover:text-black' }}"
            >
                Active ({{ $activeCount }})
            </button>
            <button 
                wire:click="$set('statusFilter', 'blocked')"
                class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all {{ $statusFilter === 'blocked' ? 'bg-red-600 text-white shadow-md' : 'bg-zinc-100 text-zinc-500 hover:text-black' }}"
            >
                Blocked ({{ $blockedCount }})
            </button>
        </div>
    </div>

    <!-- Customers List -->
    <div class="bg-white rounded-[32px] border border-zinc-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-zinc-50 flex justify-between items-center">
            <h3 class="text-sm font-bold text-black uppercase tracking-widest">Collector Directory</h3>
            <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">Page {{ $customers->currentPage() }} of {{ $customers->lastPage() }}</p>
        </div>

        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 gap-1 md:hidden">
            @forelse ($customers as $customer)
                <div wire:key="customer-card-{{ $customer->id }}" class="p-6 bg-white flex flex-col gap-4 border-b border-zinc-50 last:border-none">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-black text-white flex items-center justify-center font-bold text-xs shrink-0">
                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-black truncate">{{ $customer->name }}</p>
                                <p class="text-[10px] text-zinc-400 font-bold truncate">{{ $customer->email }}</p>
                            </div>
                        </div>
                        <span class="text-[9px] font-bold px-3 py-1 rounded-full uppercase tracking-wider {{ $customer->is_blocked ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }}">
                            {{ $customer->is_blocked ? 'Blocked' : 'Active' }}
                        </span>
                    </div>

                    <!-- Activity Badges -->
                    <div class="grid grid-cols-4 gap-2 bg-zinc-50 p-3 rounded-2xl text-center">
                        <div>
                            <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Saved</p>
                            <p class="text-xs font-bold text-black">{{ $customer->saved_units_count }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Bids</p>
                            <p class="text-xs font-bold text-black">{{ $customer->bids_count }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Deposits</p>
                            <p class="text-xs font-bold text-black">{{ $customer->bid_deposits_count }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Chats</p>
                            <p class="text-xs font-bold text-black">{{ $customer->chat_messages_count }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-bold text-zinc-300 uppercase tracking-widest">Joined {{ $customer->created_at?->format('M d, Y') }}</span>
                        <button 
                            @click="targetCustomer = { id: {{ $customer->id }}, name: '{{ addslashes($customer->name) }}', is_blocked: {{ $customer->is_blocked ? 'true' : 'false' }} }; showConfirmModal = true"
                            class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ $customer->is_blocked ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-red-50 text-red-600 hover:bg-red-100 border border-red-200' }}"
                        >
                            {{ $customer->is_blocked ? 'Unblock' : 'Block Access' }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-[10px] text-zinc-300 font-bold uppercase tracking-widest">No customers found</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-zinc-50/50">
                        <th class="px-8 py-4 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Collector</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Activity Summary</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Joined</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-zinc-400 uppercase tracking-widest text-right">Security Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-zinc-50/30 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-black text-white flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold text-black truncate">{{ $customer->name }}</p>
                                            @if($customer->hasGoogleAccount())
                                                <span class="text-[8px] font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full uppercase tracking-wider border border-blue-100">Google</span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-zinc-400 font-bold truncate">{{ $customer->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-zinc-100 text-zinc-700 px-3 py-1 rounded-full uppercase tracking-wider" title="Saved Vehicles">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3 text-red-500" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                        {{ $customer->saved_units_count }} Saved
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-zinc-100 text-zinc-700 px-3 py-1 rounded-full uppercase tracking-wider" title="Auction Bids">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3 text-amber-600" stroke="currentColor" stroke-width="2.5"><path d="M14 2l4 4L7 17H3v-4L14 2z"/><path d="M13 3l4 4"/></svg>
                                        {{ $customer->bids_count }} Bids
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-zinc-100 text-zinc-700 px-3 py-1 rounded-full uppercase tracking-wider" title="Bid Deposits">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3 text-emerald-600" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                        {{ $customer->bid_deposits_count }} Deposits
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-zinc-100 text-zinc-700 px-3 py-1 rounded-full uppercase tracking-wider" title="Inquiries & Messages">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3 text-blue-600" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                        {{ $customer->chat_messages_count }} Chats
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-[11px] font-bold text-zinc-400">
                                {{ $customer->created_at?->format('M d, Y') }}
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider {{ $customer->is_blocked ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }}">
                                    {{ $customer->is_blocked ? 'Blocked' : 'Active' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button 
                                    @click="targetCustomer = { id: {{ $customer->id }}, name: '{{ addslashes($customer->name) }}', is_blocked: {{ $customer->is_blocked ? 'true' : 'false' }} }; showConfirmModal = true"
                                    class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all cursor-pointer {{ $customer->is_blocked ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-red-50 text-red-600 hover:bg-red-100 border border-red-200' }}"
                                >
                                    {{ $customer->is_blocked ? 'Unblock Account' : 'Block Access' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center text-[10px] text-zinc-300 font-bold uppercase tracking-widest">
                                No customer accounts found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-8 py-6 border-t border-zinc-50">
            {{ $customers->links() }}
        </div>
    </div>

    <!-- Custom Confirmation Modal (Teleported to Body for true full-screen overlay) -->
    <template x-teleport="body">
        <div 
            x-show="showConfirmModal" 
            style="display: none;"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-zinc-950/60 backdrop-blur-md z-[9999] flex items-center justify-center p-4"
        >
            <div 
                @click.away="showConfirmModal = false"
                @keydown.escape.window="showConfirmModal = false"
                x-show="showConfirmModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="bg-white rounded-[32px] p-8 max-w-md w-full border border-zinc-100 shadow-[0_32px_64px_-12px_rgba(0,0,0,0.3)] text-center space-y-6 relative overflow-hidden"
            >
                <!-- Top Icon Indicator -->
                <div class="mx-auto h-16 w-16 rounded-full flex items-center justify-center shadow-inner" :class="targetCustomer?.is_blocked ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'">
                    <template x-if="!targetCustomer?.is_blocked">
                        <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                    </template>
                    <template x-if="targetCustomer?.is_blocked">
                        <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </template>
                </div>

                <div>
                    <h3 class="text-xl font-bold text-black" x-text="targetCustomer?.is_blocked ? 'Unblock Customer Account?' : 'Block & Suspend Customer?'"></h3>
                    <p class="text-xs text-zinc-500 font-medium mt-2 leading-relaxed">
                        <span x-text="targetCustomer?.is_blocked ? 'Are you sure you want to reactivate access for ' : 'Are you sure you want to suspend access for '"></span>
                        <strong class="text-black font-bold" x-text="targetCustomer?.name"></strong>?
                        <span x-text="targetCustomer?.is_blocked ? ' They will be granted full access back to garage & bidding.' : ' They will be immediately logged out and restricted from placing bids or sending inquiries.'"></span>
                    </p>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button 
                        @click="showConfirmModal = false"
                        class="flex-1 bg-zinc-100 hover:bg-zinc-200 text-black font-bold text-xs uppercase tracking-widest py-3.5 rounded-2xl transition-all cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="$wire.toggleBlock(targetCustomer.id); showConfirmModal = false"
                        class="flex-1 font-bold text-xs uppercase tracking-widest py-3.5 rounded-2xl shadow-lg transition-all text-white cursor-pointer"
                        :class="targetCustomer?.is_blocked ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700'"
                        x-text="targetCustomer?.is_blocked ? 'Confirm Unblock' : 'Confirm Block'"
                    >
                    </button>
                </div>
            </div>
        </div>
    </template>
</section>
