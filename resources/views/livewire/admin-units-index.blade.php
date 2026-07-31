@php
    use App\Models\Unit;
    use App\Models\UnitStatusLog;
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="space-y-12 animate-showroom-fade-up">
    <!-- Page Header & Inventory Mode Tabs -->      
    <header class="flex flex-col xl:flex-row justify-between items-start xl:items-end gap-6">
        <div>
            <h2 class="text-5xl font-bold tracking-tighter text-black mb-2">Inventory Registry</h2>
            <p class="text-sm font-medium text-zinc-400">Oversee active inventory, track sales archives, and manage vehicle lifecycles.</p>
        </div>

        <!-- Mode Navigation Tabs -->
        <div class="flex items-center bg-zinc-100 p-1.5 rounded-2xl border border-zinc-200/60 shadow-inner">
            <flux:tooltip content="View active available vehicles in stock" position="top">
                <button 
                    wire:click="setViewMode('active')"
                    class="flex items-center gap-2 px-6 py-3 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all {{ $viewMode === 'active' ? 'bg-white text-black shadow-md' : 'text-zinc-500 hover:text-black' }}"
                >
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Active Inventory
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $viewMode === 'active' ? 'bg-black text-white' : 'bg-zinc-200 text-zinc-600' }}">{{ $activeCount }}</span>
                </button>
            </flux:tooltip>

            <flux:tooltip content="View sold vehicles and buyer handover records" position="top">
                <button 
                    wire:click="setViewMode('sold')"
                    class="flex items-center gap-2 px-6 py-3 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all {{ $viewMode === 'sold' ? 'bg-black text-white shadow-md' : 'text-zinc-500 hover:text-black' }}"
                >
                    <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" stroke="currentColor" stroke-width="2.5"><path d="M5 8h14M5 8a2 2 0 1 1 0-4h14a2 2 0 1 1 0 4M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8m-9 4h4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Sold Archive
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $viewMode === 'sold' ? 'bg-white text-black' : 'bg-zinc-200 text-zinc-600' }}">{{ $soldCount }}</span>
                </button>
            </flux:tooltip>

            @if ($canManageTrash)
                <flux:tooltip content="View soft-deleted decommissioned vehicles" position="top">
                    <button 
                        wire:click="setViewMode('trashed')"
                        class="flex items-center gap-2 px-6 py-3 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all {{ $viewMode === 'trashed' ? 'bg-red-600 text-white shadow-md' : 'text-zinc-500 hover:text-black' }}"
                    >
                        Trash
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $viewMode === 'trashed' ? 'bg-white text-red-600' : 'bg-zinc-200 text-zinc-600' }}">{{ $trashedCount }}</span>
                    </button>
                </flux:tooltip>
            @endif
        </div>
    </header>

    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl text-sm font-bold flex items-center gap-3 animate-showroom-fade-up">
            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-emerald-600" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17L4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('status') }}
        </div>
    @endif
    @if (session('info'))
        <div class="bg-zinc-100 border border-zinc-200 text-zinc-700 px-6 py-4 rounded-2xl text-sm font-bold flex items-center gap-3 animate-showroom-fade-up">
            {{ session('info') }}
        </div>
    @endif

    <!-- Control Bar (Search & Category Filter) -->
    <section class="flex flex-col xl:flex-row gap-6 justify-between items-start xl:items-center">
        <!-- Search -->
        <div class="relative w-full xl:w-96">
            <svg viewBox="0 0 24 24" fill="none" class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-zinc-400" stroke="currentColor" stroke-width="3"><circle cx="11" cy="11" r="7"/><path d="M20 20L16.65 16.65" stroke-linecap="round"/></svg>
            <input 
                wire:model.live.debounce.300ms="search"
                class="w-full bg-white border border-gallery-outline/20 text-black font-medium rounded-2xl pl-12 pr-4 py-4 focus:outline-none focus:ring-2 focus:ring-black/5 transition-all shadow-sm" 
                placeholder="Search name or ID..." 
                type="text"
            />
        </div>

        <!-- Category Filter & Actions -->
        <div class="flex flex-wrap items-center gap-4 w-full xl:w-auto">
            <!-- Category Select -->
            <select wire:model.live="categoryId" class="bg-white border border-gallery-outline/20 rounded-2xl px-6 py-3.5 text-[11px] font-bold uppercase tracking-widest text-black focus:ring-2 focus:ring-black/5">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <button wire:click="resetFilters" class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:text-black transition-colors ml-2">Reset Filters</button>

            <flux:tooltip content="Create a new vehicle entry in inventory" position="left">
                <a href="{{ route('admin.units.create') }}" class="bg-black text-white font-bold text-[11px] uppercase tracking-widest px-6 py-3.5 rounded-2xl hover:opacity-90 transition-all ambient-shadow flex items-center gap-2 ml-auto">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                    New Unit
                </a>
            </flux:tooltip>
        </div>
    </section>

    <!-- Main Data Container -->
    <section class="animate-showroom-fade-up" style="animation-delay: 0.2s;">
        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 gap-6 md:hidden">
            @forelse ($units as $unit)
                <div wire:key="admin-unit-card-{{ $unit->id }}" class="bg-white rounded-[32px] border border-gallery-outline/20 ambient-shadow p-6 flex flex-col gap-6 {{ $unit->trashed() ? 'opacity-50' : '' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-14 rounded-2xl bg-gallery-surface-low overflow-hidden border border-gallery-outline/10 shadow-sm shrink-0">
                            @if ($unit->mainImage)
                                <img src="{{ Storage::url($unit->mainImage->url) }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-black text-lg tracking-tight truncate">{{ $unit->name }}</h3>
                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">{{ $unit->category?->name ?? 'Uncategorized' }} • {{ $unit->year ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center py-4 border-y border-gallery-outline/5">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Price</span>
                            <span class="text-sm font-bold text-black">{{ $unit->formattedPrice() }}</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Status</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold text-[9px] uppercase tracking-widest {{ $unit->isAvailable() ? 'bg-emerald-50 text-emerald-600' : 'bg-zinc-100 text-zinc-600' }}">
                                <span class="w-1 h-1 rounded-full {{ $unit->isAvailable() ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span>
                                {{ $unit->status }}
                            </span>
                        </div>
                    </div>

                    @if ($viewMode === 'sold' && ($unit->guest_name || $unit->guest_contact || $unit->handover_image_path))
                        <div class="p-4 bg-zinc-50 rounded-2xl border border-zinc-100 space-y-2">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest block">Buyer Handover Details</span>
                            @if($unit->guest_name)<p class="text-xs font-bold text-black">Buyer: {{ $unit->guest_name }}</p>@endif
                            @if($unit->guest_contact)<p class="text-xs font-medium text-zinc-500">Contact: {{ $unit->guest_contact }}</p>@endif
                            @if($unit->handover_image_path)
                                <a href="{{ Storage::url($unit->handover_image_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 uppercase tracking-widest hover:underline pt-1">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" stroke="currentColor" stroke-width="2.5"><path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"/></svg>
                                    View Handover Photo
                                </a>
                            @endif
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[11px] font-mono text-zinc-400">#{{ substr($unit->public_id, -8) }}</span>
                        <div class="flex gap-2">
                            @if ($viewMode === 'sold')
                                <flux:tooltip content="Re-list vehicle back to Active Inventory" position="top">
                                    <button wire:click="relistAsAvailable({{ $unit->id }})" class="flex h-12 px-4 items-center justify-center rounded-xl bg-emerald-500 text-white font-bold text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-md">
                                        Re-list Unit
                                    </button>
                                </flux:tooltip>
                            @endif
                            @if (! $unit->trashed())
                                <flux:tooltip content="Edit vehicle specifications and gallery" position="top">
                                    <a href="{{ route('admin.units.edit', $unit) }}" class="flex h-12 px-4 items-center justify-center rounded-xl border border-gallery-outline/30 text-black font-bold text-[10px] uppercase tracking-widest hover:bg-zinc-50 transition-all">Edit</a>
                                </flux:tooltip>
                                <flux:tooltip content="Open signed QR code quick action page" position="top">
                                    <a href="{{ $unit->signedQrUrl() }}" target="_blank" class="flex h-12 w-12 items-center justify-center rounded-xl border border-gallery-outline/30 text-zinc-400 hover:text-black transition-all">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                                    </a>
                                </flux:tooltip>
                            @endif
                            @if ($canManageTrash)
                                @if ($unit->trashed())
                                    <flux:tooltip content="Restore vehicle from trash" position="top">
                                        <button wire:click="restore({{ $unit->id }})" class="flex h-12 px-6 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 font-bold text-[10px] uppercase tracking-widest">Restore</button>
                                    </flux:tooltip>
                                @else
                                    <flux:tooltip content="Decommission vehicle to trash" position="top">
                                        <button wire:click="confirmDelete({{ $unit->id }})" x-on:click="$flux.modal('confirm-unit-deletion').show()" class="flex h-12 w-12 items-center justify-center rounded-xl border border-gallery-outline/30 text-zinc-300 hover:text-red-600 transition-all">
                                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </flux:tooltip>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center bg-white rounded-[32px] border border-gallery-outline/20">
                    <span class="text-[10px] font-bold uppercase tracking-[0.4em] text-zinc-300">
                        {{ $viewMode === 'sold' ? 'Sold Archive Empty' : ($viewMode === 'trashed' ? 'Trash Empty' : 'No Active Vehicles Listed') }}
                    </span>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block bg-white rounded-[40px] border border-gallery-outline/20 ambient-shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gallery-outline/10 text-zinc-400">
                            <th class="p-8 font-bold text-[10px] uppercase tracking-widest">Asset Details</th>
                            <th class="p-8 font-bold text-[10px] uppercase tracking-widest">Ref ID</th>
                            <th class="p-8 font-bold text-[10px] uppercase tracking-widest">Pricing</th>
                            @if ($viewMode === 'sold')
                                <th class="p-8 font-bold text-[10px] uppercase tracking-widest">Handover Record</th>
                            @else
                                <th class="p-8 font-bold text-[10px] uppercase tracking-widest">Status</th>
                            @endif
                            <th class="p-8 font-bold text-[10px] uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach ($units as $unit)
                            <tr wire:key="admin-unit-row-{{ $unit->id }}" class="group hover:bg-gallery-surface-low transition-colors duration-200 {{ $unit->trashed() ? 'opacity-50' : '' }}">
                                <td class="p-8">
                                    <div class="flex items-center gap-6">
                                        <div class="w-20 h-14 rounded-2xl bg-gallery-surface-low overflow-hidden border border-gallery-outline/10 shadow-sm shrink-0">
                                            @if ($unit->mainImage)
                                                <img src="{{ Storage::url($unit->mainImage->url) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-black text-lg tracking-tight flex items-center gap-2">
                                                {{ $unit->name }}
                                                @if($unit->is_featured)
                                                    <span class="bg-black text-white text-[8px] font-bold uppercase tracking-widest px-1.5 py-0.5 rounded-md">Featured</span>
                                                @endif
                                            </div>
                                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-1">{{ $unit->category?->name ?? 'Uncategorized' }} • {{ $unit->year ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-8 font-mono text-[13px] text-zinc-500">#{{ substr($unit->public_id, -8) }}</td>
                                <td class="p-8 font-bold text-black text-base">{{ $unit->formattedPrice() }}</td>
                                
                                @if ($viewMode === 'sold')
                                    <td class="p-8">
                                        @if($unit->guest_name || $unit->guest_contact || $unit->handover_image_path)
                                            <div class="space-y-1">
                                                <p class="text-xs font-bold text-black">{{ $unit->guest_name ?: 'Walk-in Buyer' }}</p>
                                                @if($unit->guest_contact)<p class="text-[10px] text-zinc-400 font-mono">{{ $unit->guest_contact }}</p>@endif
                                                @if($unit->handover_image_path)
                                                    <flux:tooltip content="View handover photo proof in new tab" position="top">
                                                        <a href="{{ Storage::url($unit->handover_image_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 uppercase tracking-widest hover:underline pt-0.5">
                                                            <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" stroke="currentColor" stroke-width="2.5"><path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"/></svg>
                                                            Handover Photo
                                                        </a>
                                                    </flux:tooltip>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Auction / Standard Sale</span>
                                        @endif
                                    </td>
                                @else
                                    <td class="p-8">
                                        <div class="flex flex-col gap-1.5 items-start">
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full font-bold text-[10px] uppercase tracking-widest {{ $unit->isAvailable() ? 'bg-emerald-50 text-emerald-600' : 'bg-zinc-100 text-zinc-500' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $unit->isAvailable() ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-400' }}"></span>
                                                {{ $unit->status }}
                                            </span>
                                            @if ($unit->isAuctionCandidate())
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-bold text-[9px] uppercase tracking-widest bg-amber-50 text-amber-800 border border-amber-200/80 shadow-xs" data-test="auction-candidate-badge">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                    Auction Candidate ({{ $unit->daysInCurrentListing() }}d)
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                @endif

                                <td class="p-8 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        @if ($viewMode === 'sold')
                                            <flux:tooltip content="Re-list vehicle back to Active Inventory" position="top">
                                                <button 
                                                    wire:click="relistAsAvailable({{ $unit->id }})" 
                                                    class="flex h-10 px-4 items-center gap-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white font-bold text-[10px] uppercase tracking-widest transition-all border border-emerald-200"
                                                >
                                                    <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8M3 3v5h5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    Re-list
                                                </button>
                                            </flux:tooltip>
                                        @endif

                                        @if (! $unit->trashed())
                                            <flux:tooltip content="Edit vehicle specs & gallery" position="top">
                                                <a href="{{ route('admin.units.edit', $unit) }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-gallery-outline/30 text-zinc-400 hover:text-black hover:border-black transition-all">
                                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </a>
                                            </flux:tooltip>
                                            <flux:tooltip content="Open signed QR code quick action page" position="top">
                                                <a href="{{ $unit->signedQrUrl() }}" target="_blank" class="flex h-10 w-10 items-center justify-center rounded-full border border-gallery-outline/30 text-zinc-400 hover:text-black hover:border-black transition-all">
                                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                                                </a>
                                            </flux:tooltip>
                                        @endif

                                        @if ($canManageTrash)
                                            @if ($unit->trashed())
                                                <flux:tooltip content="Restore vehicle from trash" position="top">
                                                    <button wire:click="restore({{ $unit->id }})" class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all shadow-sm">
                                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8M3 3v5h5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                </flux:tooltip>
                                            @else
                                                <flux:tooltip content="Decommission vehicle to trash" position="top">
                                                    <button wire:click="confirmDelete({{ $unit->id }})" x-on:click="$flux.modal('confirm-unit-deletion').show()" class="flex h-10 w-10 items-center justify-center rounded-full border border-gallery-outline/30 text-zinc-300 hover:text-red-600 hover:border-red-100 transition-all">
                                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                </flux:tooltip>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($units->isEmpty())
                <div class="p-32 text-center border-t border-gallery-outline/10">
                    <span class="text-[12px] font-bold uppercase tracking-[0.4em] text-zinc-300">
                        {{ $viewMode === 'sold' ? 'Sold Archive Empty' : ($viewMode === 'trashed' ? 'Trash Empty' : 'No Active Vehicles Listed') }}
                    </span>
                </div>
            @endif
        </div>

        <div class="mt-8 px-8 py-6 bg-white rounded-[32px] border border-gallery-outline/20 ambient-shadow">
            {{ $units->links() }}
        </div>
    </section>

    <!-- Status History Timeline Widget -->
    <section class="mt-12 animate-entrance" style="animation-delay: 0.3s;">    
        <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em] mb-8">Recent Status Changes</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8"> 
            @foreach($recentStatusChanges as $log)
                <div class="bg-white p-8 rounded-[32px] border border-gallery-outline/20 ambient-shadow hover-lift">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-10 w-10 rounded-full {{ $log->action === UnitStatusLog::ACTION_SET_AVAILABLE ? 'bg-emerald-50 text-emerald-600' : 'bg-black text-white' }} flex items-center justify-center">
                            @if($log->action === UnitStatusLog::ACTION_SET_AVAILABLE)
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17L4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round"/></svg>
                            @endif
                        </div>
                        <div class="font-bold text-black tracking-tight leading-tight">{{ $log->unit?->name ?? 'System Event' }}</div>
                    </div>
                    <div class="text-[13px] text-zinc-500 font-medium mb-1">Status set to <strong class="text-black">{{ $log->action }}</strong></div>
                    <div class="text-[10px] font-bold text-zinc-300 uppercase tracking-widest">{{ $log->created_at?->diffForHumans() }} • {{ $log->user?->name ?? 'System' }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <flux:modal name="confirm-unit-deletion" class="min-w-[24rem] !p-0 rounded-[40px] border-none shadow-2xl">
        <div class="p-10 space-y-8 bg-white">
            <div class="text-center">
                <div class="mx-auto w-16 h-16 rounded-full bg-red-50 flex items-center justify-center text-red-600 mb-6">
                    <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-black">Decommission Asset</h2>
                <p class="mt-4 text-sm font-medium text-zinc-500 leading-relaxed px-4">
                    Confirm deletion of <strong class="text-black">{{ $unitToDeleteName }}</strong>? This action will archive the vehicle from the public showroom.
                </p>
            </div>

            <div class="flex flex-col gap-3">
                <button type="button" wire:click="executeDelete" x-on:click="$flux.modal('confirm-unit-deletion').close()" class="h-14 w-full rounded-full bg-red-600 text-white font-bold text-[11px] uppercase tracking-widest shadow-xl hover:bg-red-700 transition-all">
                    Archive Permanently
                </button>
                <flux:modal.close>
                    <button type="button" class="h-12 w-full rounded-full font-bold text-[10px] uppercase tracking-widest text-zinc-400 hover:text-black transition-colors">Cancel</button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</div>
