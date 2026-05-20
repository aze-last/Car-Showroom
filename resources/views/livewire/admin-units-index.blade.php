@php
    use App\Models\Unit;
    use Illuminate\Support\Facades\Storage;
@endphp

<section class="space-y-8">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xs font-black uppercase tracking-[0.4em] text-zinc-900">Inventory</h2>
            <div class="mt-2 h-1 w-8 bg-zinc-900"></div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if ($canManageTrash)
                <button 
                    type="button" 
                    wire:click="runImporter" 
                    wire:loading.attr="disabled"
                    class="admin-btn-secondary"
                >
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2" wire:loading.class="animate-spin" wire:target="runImporter">
                        <path d="M21 12A9 9 0 1 1 3 12A9 9 0 0 1 21 12Z" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 7V12L15 15" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span wire:loading.remove wire:target="runImporter">Sync Catalog</span>
                    <span wire:loading wire:target="runImporter">Syncing...</span>
                </button>
            @endif

            <a href="{{ route('admin.units.create') }}" class="admin-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 5V19M5 12H19" stroke-linecap="round"/>
                </svg>
                New Unit
            </a>
        </div>
    </div>

    <article class="admin-card">
        <div class="admin-card-body">
            <div class="grid gap-6 lg:grid-cols-[2fr_1.5fr_1.5fr_auto] lg:items-end">
                <label class="block">
                    <span class="mb-2.5 block text-[10px] font-black uppercase tracking-widest text-zinc-400">Search Detail</span>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Vehicle name or ID..."
                        class="admin-input"
                    >
                </label>

                <label class="block">
                    <span class="mb-2.5 block text-[10px] font-black uppercase tracking-widest text-zinc-400">Category</span>
                    <select wire:model.live="categoryId" class="admin-select">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-2.5 block text-[10px] font-black uppercase tracking-widest text-zinc-400">Availability</span>
                    <select wire:model.live="status" class="admin-select">
                        <option value="">All Statuses</option>
                        <option value="{{ Unit::STATUS_AVAILABLE }}">Available</option>
                        <option value="{{ Unit::STATUS_SOLD }}">Sold</option>
                    </select>
                </label>

                <div class="flex items-center gap-3">
                    @if ($canManageTrash)
                        <button 
                            type="button" 
                            wire:click="$toggle('includeTrashed')" 
                            class="h-12 px-5 rounded-xl border border-zinc-100 {{ $includeTrashed ? 'bg-zinc-900 text-white' : 'bg-zinc-50 text-zinc-500' }} transition-all"
                            title="Show deleted items"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2">
                                <path d="M3 6H21M19 6V20A2 2 0 0 1 17 22H7A2 2 0 0 1 5 20V6M8 6V4A2 2 0 0 1 10 2H14A2 2 0 0 1 16 4V6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    @endif

                    <button type="button" wire:click="resetFilters" class="h-12 rounded-xl border border-zinc-200 bg-white px-6 text-xs font-bold text-zinc-400 hover:text-zinc-900 transition-colors">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </article>

    <article class="admin-card overflow-hidden">
        <div class="flex items-center justify-between border-b border-zinc-50 bg-zinc-50/50 px-8 py-4">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Inventory Registry</span>
            <span wire:loading class="text-[10px] font-bold text-zinc-400 animate-pulse">Processing...</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left text-[10px] font-black uppercase tracking-widest text-zinc-400 border-b border-zinc-50">
                    <tr>
                        <th scope="col" class="px-8 py-5">Visual</th>
                        <th scope="col" class="px-8 py-5">Vehicle Name</th>
                        <th scope="col" class="px-8 py-5">Price</th>
                        <th scope="col" class="px-8 py-5">Status</th>
                        <th scope="col" class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50">
                    @forelse ($units as $unit)
                        <tr wire:key="admin-unit-row-{{ $unit->id }}" class="{{ $unit->trashed() ? 'bg-zinc-50/50' : '' }} hover:bg-zinc-50/30 transition-colors">
                            <td class="px-8 py-4">
                                <div class="h-16 w-24 overflow-hidden rounded-2xl bg-zinc-100 border border-zinc-100 shadow-inner">
                                    @if ($unit->mainImage)
                                        <img
                                            src="{{ Storage::url($unit->mainImage->url) }}"
                                            alt="{{ $unit->name }}"
                                            class="h-full w-full object-cover {{ $unit->isSold() ? 'grayscale opacity-50' : '' }}"
                                            loading="lazy"
                                        >
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-4">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <p class="font-bold text-zinc-900 {{ $unit->trashed() ? 'line-through opacity-50' : '' }}">{{ $unit->name }}</p>
                                        @if ($unit->is_featured)
                                            <span class="rounded-md bg-zinc-900 px-1.5 py-0.5 text-[8px] font-black text-white uppercase tracking-tighter shadow-sm">Featured</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-0.5">
                                        {{ $unit->category?->name ?? 'Uncategorized' }} • #{{ substr($unit->public_id, -6) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-4 font-black text-zinc-900 tracking-tight">{{ $unit->formattedPrice() }}</td>
                            <td class="px-8 py-4">
                                <span class="{{ $unit->isAvailable() ? 'admin-badge admin-badge-available' : 'admin-badge admin-badge-sold' }}">
                                    {{ $unit->status }}
                                </span>
                            </td>
                            <td class="px-8 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if (! $unit->trashed())
                                        <a href="{{ route('admin.units.edit', $unit) }}" class="flex h-9 w-9 items-center justify-center rounded-xl bg-zinc-50 text-zinc-500 hover:bg-zinc-900 hover:text-white transition-all shadow-sm">
                                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                        <a href="{{ $unit->signedQrUrl() }}" target="_blank" class="flex h-9 w-9 items-center justify-center rounded-xl bg-zinc-50 text-zinc-500 hover:bg-zinc-900 hover:text-white transition-all shadow-sm">
                                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="3" width="7" height="7" rx="1"/>
                                                <rect x="14" y="3" width="7" height="7" rx="1"/>
                                                <rect x="14" y="14" width="7" height="7" rx="1"/>
                                                <rect x="3" y="14" width="7" height="7" rx="1"/>
                                            </svg>
                                        </a>
                                    @endif

                                    @if ($canManageTrash)
                                        @if ($unit->trashed())
                                            <button
                                                type="button"
                                                wire:click="restore({{ $unit->id }})"
                                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all shadow-sm"
                                                title="Restore"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5">
                                                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M3 3v5h5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        @else
                                            <button
                                                type="button"
                                                wire:click="confirmDelete({{ $unit->id }})"
                                                x-on:click="$flux.modal('confirm-unit-deletion').show()"
                                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm"
                                                title="Delete"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5">
                                                    <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v6" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-24 text-center">
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-300">No vehicles match current criteria</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    <div class="flex justify-end pt-4">
        {{ $units->links() }}
    </div>

    <flux:modal name="confirm-unit-deletion" class="min-w-[22rem] rounded-[32px] border-none shadow-2xl">
        <div class="space-y-6 p-4">
            <div>
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-red-600">Delete Unit</h2>
                <p class="mt-4 text-sm font-medium text-zinc-500 leading-relaxed">
                    Are you sure you want to soft delete <strong class="text-zinc-900">{{ $unitToDeleteName }}</strong>? This will hide the unit from the public showroom.
                </p>
            </div>

            <div class="flex gap-3">
                <flux:modal.close>
                    <button type="button" class="h-11 px-6 rounded-xl bg-zinc-50 text-xs font-bold uppercase tracking-widest text-zinc-500 hover:bg-zinc-100 transition-colors">Cancel</button>
                </flux:modal.close>
                <flux:spacer />
                <button type="button" wire:click="executeDelete" x-on:click="$flux.modal('confirm-unit-deletion').close()" class="h-11 px-6 rounded-xl bg-red-600 text-xs font-black uppercase tracking-[0.2em] text-white shadow-lg shadow-red-500/20 hover:bg-red-700 transition-all">
                    Confirm Delete
                </button>
            </div>
        </div>
    </flux:modal>
</section>
