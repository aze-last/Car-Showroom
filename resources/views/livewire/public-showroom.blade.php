@php
    use App\Models\Unit;
    use Illuminate\Support\Facades\Storage;
@endphp

<section class="space-y-7">
    <div class="rounded-[28px] border border-zinc-800 bg-zinc-900/45 p-5 shadow-[0_16px_35px_rgba(0,0,0,0.35)] backdrop-blur-sm sm:p-6 showroom-fade-in">
        <div class="space-y-6">
            <div class="grid gap-5 md:grid-cols-[1.3fr_1fr] md:items-end">
                <label class="block">
                    <span class="mb-2 block text-xs font-medium uppercase tracking-[0.12em] text-zinc-300/90">Search Unit Name</span>
                    <div class="relative">
                        <svg viewBox="0 0 24 24" fill="none" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="M20 20L16.65 16.65" stroke-linecap="round"/>
                        </svg>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search by unit name..."
                            class="h-11 w-full rounded-2xl border border-zinc-800 bg-zinc-950/40 px-10 text-sm text-zinc-100 placeholder:text-zinc-500 focus:border-amber-400/60 focus:outline-none focus:ring-2 focus:ring-amber-400/20"
                        >
                    </div>
                </label>

                <div>
                    <span class="mb-2 block text-xs font-medium uppercase tracking-[0.12em] text-zinc-300/90">Filter by Category</span>
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            wire:click="clearCategoryFilter"
                            class="rounded-full border px-4 py-2 text-xs font-medium transition duration-200 {{ $categoryId === null ? 'border-amber-400/40 bg-amber-400/15 text-amber-100' : 'border-zinc-800 bg-zinc-950/25 text-zinc-200 hover:border-zinc-700 hover:bg-zinc-900/70' }}"
                        >
                            All
                        </button>

                        @foreach ($categories as $category)
                            <button
                                type="button"
                                wire:click="$set('categoryId', {{ $category->id }})"
                                class="rounded-full border px-4 py-2 text-xs font-medium transition duration-200 {{ $categoryId === $category->id ? 'border-amber-400/40 bg-amber-400/15 text-amber-100' : 'border-zinc-800 bg-zinc-950/25 text-zinc-200 hover:border-zinc-700 hover:bg-zinc-900/70' }}"
                            >
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid gap-5 border-t border-zinc-800/60 pt-6 md:grid-cols-[1fr_1.3fr_auto]">
                <label class="block">
                    <span class="mb-2 block text-xs font-medium uppercase tracking-[0.12em] text-zinc-300/90">Sort By</span>
                    <select wire:model.live="sortBy" class="h-11 w-full rounded-2xl border border-zinc-800 bg-zinc-950/40 px-4 text-sm text-zinc-100 focus:border-amber-400/60 focus:outline-none focus:ring-2 focus:ring-amber-400/20">
                        <option value="newest">Newest Arrivals</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                    </select>
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="mb-2 block text-xs font-medium uppercase tracking-[0.12em] text-zinc-300/90">Min Price</span>
                        <input type="number" wire:model.live.debounce.500ms="minPrice" placeholder="PHP" class="h-11 w-full rounded-2xl border border-zinc-800 bg-zinc-950/40 px-4 text-sm text-zinc-100 focus:border-amber-400/60 focus:outline-none">
                    </label>
                    <label class="block">
                        <span class="mb-2 block text-xs font-medium uppercase tracking-[0.12em] text-zinc-300/90">Max Price</span>
                        <input type="number" wire:model.live.debounce.500ms="maxPrice" placeholder="PHP" class="h-11 w-full rounded-2xl border border-zinc-800 bg-zinc-950/40 px-4 text-sm text-zinc-100 focus:border-amber-400/60 focus:outline-none">
                    </label>
                </div>

                <div class="flex items-end">
                    <button type="button" wire:click="resetFilters" class="h-11 rounded-2xl border border-zinc-800 bg-zinc-900/40 px-6 text-xs font-medium text-zinc-300 transition hover:bg-zinc-800 hover:text-white">
                        Reset All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div wire:loading wire:target="search,categoryId,clearCategoryFilter" class="rounded-2xl border border-zinc-800 bg-zinc-900/40 px-4 py-3 text-xs text-zinc-300">
        Updating showroom results...
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($units as $unit)
            <a
                href="{{ route('units.show', $unit) }}"
                class="group overflow-hidden rounded-[26px] border border-zinc-800 bg-zinc-900/45 shadow-[0_14px_30px_rgba(0,0,0,0.3)] transition duration-300 hover:-translate-y-1 hover:border-zinc-700 hover:shadow-[0_20px_42px_rgba(0,0,0,0.42)] showroom-lift"
                wire:key="unit-card-{{ $unit->id }}"
            >
                <div class="relative aspect-[4/3] overflow-hidden bg-zinc-800">
                    <div class="absolute inset-0 z-10 bg-gradient-to-t from-zinc-950/85 via-transparent to-transparent"></div>
                    @if ($unit->mainImage)
                        <img
                            src="{{ Storage::url($unit->mainImage->url) }}"
                            alt="{{ $unit->name }}"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03] {{ $unit->status === Unit::STATUS_SOLD ? 'grayscale' : '' }}"
                            loading="lazy"
                        >
                    @else
                        <div class="flex h-full w-full items-center justify-center text-sm text-zinc-400">
                            No image uploaded
                        </div>
                    @endif

                    <div class="absolute left-4 top-4 z-20 flex items-center gap-2">
                        @if ($unit->is_featured)
                            <span class="rounded-md border border-amber-400/40 bg-amber-500/85 px-2.5 py-1 text-[11px] font-bold text-white shadow-lg">
                                FEATURED
                            </span>
                        @endif
                        <span class="rounded-md border border-white/10 bg-black/40 px-2.5 py-1 text-[11px] font-medium text-zinc-100">
                            {{ $unit->category?->name ?? 'Uncategorized' }}
                        </span>
                        <span class="rounded-md border px-2.5 py-1 text-[11px] font-semibold {{ $unit->status === Unit::STATUS_AVAILABLE ? 'border-emerald-300/35 bg-emerald-600/75 text-white' : 'border-red-300/35 bg-red-600/80 text-white' }}">
                            {{ $unit->status === Unit::STATUS_AVAILABLE ? 'Available' : 'Sold' }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold tracking-tight text-zinc-100 transition-colors duration-300 group-hover:text-amber-200">
                                {{ $unit->name }}
                            </h2>
                            <p class="mt-1 text-xs text-zinc-400">Tap to open gallery</p>
                        </div>
                        <p class="text-right text-sm font-semibold text-amber-300">
                            {{ $unit->formattedPrice() }}
                        </p>
                    </div>

                    @if($unit->year || $unit->mileage || $unit->transmission)
                        <div class="flex flex-wrap gap-2 pt-1">
                            @if($unit->year)
                                <span class="flex items-center gap-1 text-[10px] text-zinc-400">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" stroke="currentColor" stroke-width="2">
                                        <path d="M8 7V3M16 7V3M3 11H21M5 5H19C20.1046 5 21 5.89543 21 7V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V7C3 5.89543 3.89543 5 5 5Z" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ $unit->year }}
                                </span>
                            @endif
                            @if($unit->mileage)
                                <span class="flex items-center gap-1 text-[10px] text-zinc-400">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" stroke="currentColor" stroke-width="2">
                                        <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ number_format($unit->mileage) }} km
                                </span>
                            @endif
                            @if($unit->transmission)
                                <span class="flex items-center gap-1 text-[10px] text-zinc-400">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12H19M12 5V19" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ $unit->transmission }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="rounded-[24px] border border-zinc-800 bg-zinc-900/35 p-10 text-center text-sm text-zinc-300 sm:col-span-2 lg:col-span-3">
                No units found for the current filter.
            </div>
        @endforelse
    </div>

    <div class="pt-1">
        {{ $units->links() }}
    </div>
</section>
