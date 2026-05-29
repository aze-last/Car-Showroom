@php
    use App\Models\Unit;
    use App\Models\UnitStatusLog;
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="space-y-12 animate-showroom-fade-up">
    <!-- Page Header -->      
    <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h2 class="text-5xl font-bold tracking-tighter text-black mb-2">Inventory Registry</h2>
            <p class="text-sm font-medium text-zinc-400">Oversee your collection and track vehicle life-cycles with precision.</p>
        </div>
        <div class="flex gap-3">
             @if ($canManageTrash)
                <button 
                    type="button" 
                    wire:click="runImporter" 
                    wire:loading.attr="disabled"
                    class="px-6 py-3 border border-gallery-outline/30 rounded-2xl font-bold text-[11px] uppercase tracking-widest text-zinc-500 hover:text-black hover:bg-gallery-surface-low transition-all duration-300"
                >
                    <span wire:loading.remove wire:target="runImporter">Sync External</span>
                    <span wire:loading wire:target="runImporter">Syncing...</span>
                </button>
            @endif
        </div>
    </header>

    <!-- Control Bar (Filters & Search) -->
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

        <!-- Toggles & Filters -->
        <div class="flex flex-wrap items-center gap-4 w-full xl:w-auto">
            <!-- Status Toggles -->   
            <div class="flex bg-gallery-surface-low p-1.5 rounded-2xl border border-gallery-outline/10">
                <button 
                    wire:click="$set('status', '')"
                    class="px-6 py-2.5 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all {{ $status === '' ? 'bg-white text-black shadow-sm' : 'text-zinc-400 hover:text-zinc-600' }}"
                >
                    All
                </button>
                <button 
                    wire:click="$set('status', '{{ Unit::STATUS_AVAILABLE }}')"
                    class="px-6 py-2.5 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all {{ $status === Unit::STATUS_AVAILABLE ? 'bg-white text-emerald-600 shadow-sm' : 'text-zinc-400 hover:text-zinc-600' }}"
                >
                    Available
                </button>
                <button 
                    wire:click="$set('status', '{{ Unit::STATUS_SOLD }}')"
                    class="px-6 py-2.5 rounded-xl text-[11px] font-bold uppercase tracking-widest transition-all {{ $status === Unit::STATUS_SOLD ? 'bg-white text-zinc-900 shadow-sm' : 'text-zinc-400 hover:text-zinc-600' }}"
                >
                    Sold
                </button>
            </div>

            <!-- Category Select -->
            <select wire:model.live="categoryId" class="bg-white border border-gallery-outline/20 rounded-2xl px-6 py-3.5 text-[11px] font-bold uppercase tracking-widest text-black focus:ring-2 focus:ring-black/5">
                <option value="">Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <button wire:click="resetFilters" class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:text-black transition-colors ml-2">Reset</button>
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
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold text-[9px] uppercase tracking-widest {{ $unit->isAvailable() ? 'bg-emerald-50 text-emerald-600' : 'bg-zinc-100 text-zinc-500' }}">
                                <span class="w-1 h-1 rounded-full {{ $unit->isAvailable() ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span>
                                {{ $unit->status }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[11px] font-mono text-zinc-400">#{{ substr($unit->public_id, -8) }}</span>
                        <div class="flex gap-2">
                            @if (! $unit->trashed())
                                <a href="{{ route('admin.units.edit', $unit) }}" class="flex h-12 px-6 items-center justify-center rounded-xl border border-gallery-outline/30 text-black font-bold text-[10px] uppercase tracking-widest hover:bg-zinc-50 transition-all">Edit</a>
                                <a href="{{ $unit->signedQrUrl() }}" target="_blank" class="flex h-12 w-12 items-center justify-center rounded-xl border border-gallery-outline/30 text-zinc-400 hover:text-black transition-all">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                                </a>
                            @endif
                            @if ($canManageTrash)
                                @if ($unit->trashed())
                                    <button wire:click="restore({{ $unit->id }})" class="flex h-12 px-6 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 font-bold text-[10px] uppercase tracking-widest">Restore</button>
                                @else
                                    <button wire:click="confirmDelete({{ $unit->id }})" x-on:click="$flux.modal('confirm-unit-deletion').show()" class="flex h-12 w-12 items-center justify-center rounded-xl border border-gallery-outline/30 text-zinc-300 hover:text-red-600 transition-all">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center">
                    <span class="text-[10px] font-bold uppercase tracking-[0.4em] text-zinc-300">Inventory Empty</span>
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
                            <th class="p-8 font-bold text-[10px] uppercase tracking-widest">Current Status</th>
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
                                <td class="p-8">
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full font-bold text-[10px] uppercase tracking-widest {{ $unit->isAvailable() ? 'bg-emerald-50 text-emerald-600' : 'bg-zinc-100 text-zinc-500' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $unit->isAvailable() ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-400' }}"></span>
                                        {{ $unit->status }}
                                    </span>
                                </td>
                                <td class="p-8 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        @if (! $unit->trashed())
                                            <a href="{{ route('admin.units.edit', $unit) }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-gallery-outline/30 text-zinc-400 hover:text-black hover:border-black transition-all">
                                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </a>
                                            <a href="{{ $unit->signedQrUrl() }}" target="_blank" class="flex h-10 w-10 items-center justify-center rounded-full border border-gallery-outline/30 text-zinc-400 hover:text-black hover:border-black transition-all">
                                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                                            </a>
                                        @endif
                                        @if ($canManageTrash)
                                            @if ($unit->trashed())
                                                <button wire:click="restore({{ $unit->id }})" class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all shadow-sm">
                                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8M3 3v5h5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </button>
                                            @else
                                                <button wire:click="confirmDelete({{ $unit->id }})" x-on:click="$flux.modal('confirm-unit-deletion').show()" class="flex h-10 w-10 items-center justify-center rounded-full border border-gallery-outline/30 text-zinc-300 hover:text-red-600 hover:border-red-100 transition-all">
                                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </button>
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
                    <span class="text-[12px] font-bold uppercase tracking-[0.4em] text-zinc-200">Inventory Empty</span>
                </div>
            @endif

            <div class="p-8 border-t border-gallery-outline/10 bg-gallery-surface-low/30">
                {{ $units->links() }}
            </div>
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
