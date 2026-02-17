@php
    use App\Models\Unit;
    use Illuminate\Support\Facades\Storage;
@endphp

<section class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Units</h2>
            <p class="text-sm text-slate-500">Manage unit inventory, status, and QR actions.</p>
        </div>
        <a href="{{ route('admin.units.create') }}" class="admin-btn-primary">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                <path d="M12 5V19M5 12H19" stroke-linecap="round"/>
            </svg>
            Add Unit
        </a>
    </div>

    <article class="admin-card">
        <div class="admin-card-body">
            <div class="grid gap-4 lg:grid-cols-[2fr_1fr_1fr_auto_auto] lg:items-end">
                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Search by name</span>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search unit name"
                        class="admin-input"
                    >
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Category</span>
                    <select wire:model.live="categoryId" class="admin-select">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Status</span>
                    <select wire:model.live="status" class="admin-select">
                        <option value="">All statuses</option>
                        <option value="{{ Unit::STATUS_AVAILABLE }}">Available</option>
                        <option value="{{ Unit::STATUS_SOLD }}">Sold</option>
                    </select>
                </label>

                @if ($canManageTrash)
                    <label class="inline-flex h-10 items-center gap-2 rounded-md border border-slate-300 px-3 text-sm text-slate-700">
                        <input type="checkbox" wire:model.live="includeTrashed" class="rounded border-slate-300 text-slate-900 focus:ring-slate-400">
                        Include Trashed
                    </label>
                @else
                    <div class="hidden lg:block"></div>
                @endif

                <button type="button" wire:click="resetFilters" class="admin-btn-secondary h-10">
                    Reset Filters
                </button>
            </div>
        </div>
    </article>

    <article class="admin-card overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-2 text-xs text-slate-500 sm:px-5">
            <span>Inventory list</span>
            <span wire:loading wire:target="search,categoryId,status,includeTrashed,resetFilters,delete,restore">Updating...</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-4 py-3 sm:px-5">Thumbnail</th>
                        <th scope="col" class="px-4 py-3">Name</th>
                        <th scope="col" class="px-4 py-3">Category</th>
                        <th scope="col" class="px-4 py-3">Price</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                        <th scope="col" class="px-4 py-3">Last Updated</th>
                        <th scope="col" class="px-4 py-3 text-right sm:px-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($units as $unit)
                        <tr wire:key="admin-unit-row-{{ $unit->id }}" class="{{ $unit->trashed() ? 'bg-slate-50/80' : 'odd:bg-white even:bg-slate-50/40' }} hover:bg-slate-100/70">
                            <td class="px-4 py-3 sm:px-5">
                                <div class="h-14 w-20 overflow-hidden rounded-md border border-slate-200 bg-slate-100">
                                    @if ($unit->mainImage)
                                        <img
                                            src="{{ Storage::url($unit->mainImage->url) }}"
                                            alt="{{ $unit->name }}"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                        >
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-900 {{ $unit->trashed() ? 'line-through opacity-70' : '' }}">{{ $unit->name }}</p>
                                <p class="text-xs text-slate-500">#{{ $unit->public_id }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $unit->category?->name ?? 'Uncategorized' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $unit->formattedPrice() }}</td>
                            <td class="px-4 py-3">
                                <span class="{{ $unit->status === Unit::STATUS_AVAILABLE ? 'admin-badge admin-badge-available' : 'admin-badge admin-badge-sold' }}">
                                    {{ $unit->status === Unit::STATUS_AVAILABLE ? 'Available' : 'Sold' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $unit->updated_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-right sm:px-5">
                                <details class="relative inline-block text-left [&_summary::-webkit-details-marker]:hidden">
                                    <summary class="admin-btn-secondary cursor-pointer px-3 py-1.5 text-xs">Actions</summary>
                                    <div class="absolute right-0 z-20 mt-2 w-44 rounded-lg border border-slate-200 bg-white p-1.5 text-sm shadow-lg">
                                        @if (! $unit->trashed())
                                            <a href="{{ route('units.show', $unit) }}" target="_blank" rel="noopener noreferrer" class="block rounded-md px-3 py-2 text-left text-slate-700 hover:bg-slate-100">View</a>
                                            <a href="{{ route('admin.units.edit', $unit) }}" class="block rounded-md px-3 py-2 text-left text-slate-700 hover:bg-slate-100">Edit</a>
                                            <a href="{{ $unit->signedQrUrl() }}" class="block rounded-md px-3 py-2 text-left text-slate-700 hover:bg-slate-100">Change Status</a>
                                            <a href="{{ $unit->signedQrUrl() }}" target="_blank" rel="noopener noreferrer" class="block rounded-md px-3 py-2 text-left text-slate-700 hover:bg-slate-100">Print QR</a>
                                        @endif

                                        @if ($canManageTrash)
                                            @if ($unit->trashed())
                                                <button
                                                    type="button"
                                                    wire:click="restore({{ $unit->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="restore"
                                                    class="block w-full rounded-md px-3 py-2 text-left text-emerald-700 hover:bg-emerald-50"
                                                >
                                                    Restore
                                                </button>
                                            @else
                                                <button
                                                    type="button"
                                                    wire:click="delete({{ $unit->id }})"
                                                    wire:confirm="Soft delete this unit?"
                                                    wire:loading.attr="disabled"
                                                    wire:target="delete"
                                                    class="block w-full rounded-md px-3 py-2 text-left text-red-700 hover:bg-red-50"
                                                >
                                                    Soft Delete
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                No units found for the current filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    <div class="flex justify-end">
        {{ $units->links() }}
    </div>
</section>
