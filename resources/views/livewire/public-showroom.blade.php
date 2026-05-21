@php
    use App\Models\Unit;
    use Illuminate\Support\Facades\Storage;
@endphp

<section class="space-y-16">
    <!-- 1. Search & Filter Card (Primary Action) -->
    <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8 transition-all duration-700 ease-out motion-reduce:transition-none motion-reduce:opacity-100 motion-reduce:translate-y-0">
        <div class="space-y-8">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div>
                    <span class="mb-2.5 block text-[10px] font-bold uppercase tracking-widest text-zinc-400">Categories</span>
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            wire:click="clearCategoryFilter"
                            class="rounded-full border px-5 py-2 text-xs font-semibold transition duration-200 {{ $categoryId === null ? 'border-zinc-900 bg-zinc-900 text-white shadow-md' : 'border-zinc-200 bg-white text-zinc-600 hover:border-zinc-300 hover:bg-zinc-50' }}"
                        >
                            All
                        </button>

                        @foreach ($categories as $category)
                            <button
                                type="button"
                                wire:click="$set('categoryId', {{ $category->id }})"
                                class="rounded-full border px-5 py-2 text-xs font-semibold transition duration-200 {{ $categoryId === $category->id ? 'border-zinc-900 bg-zinc-900 text-white shadow-md' : 'border-zinc-200 bg-white text-zinc-600 hover:border-zinc-300 hover:bg-zinc-50' }}"
                            >
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-1 min-w-[280px] max-w-md items-end">
                    <label class="block w-full">
                        <span class="mb-2.5 block text-[10px] font-bold uppercase tracking-widest text-zinc-400">Search Catalog</span>
                        <div class="relative">
                            <svg viewBox="0 0 24 24" fill="none" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M20 20L16.65 16.65" stroke-linecap="round"/>
                            </svg>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Type vehicle name..."
                                class="h-12 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-11 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-zinc-900/5 transition-all"
                            >
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid gap-6 border-t border-zinc-100 pt-8 md:grid-cols-[1fr_1.3fr_auto]">
                <label class="block">
                    <span class="mb-2.5 block text-[10px] font-bold uppercase tracking-widest text-zinc-400">Sort By</span>
                    <select wire:model.live="sortBy" class="h-12 w-full appearance-none rounded-xl border border-zinc-200 bg-zinc-50 px-4 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-zinc-900/5 transition-all">
                        <option value="newest">Newest Arrivals</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                    </select>
                </label>

                <div class="grid grid-cols-2 gap-4">
                    <label class="block">
                        <span class="mb-2.5 block text-[10px] font-bold uppercase tracking-widest text-zinc-400">Min Price</span>
                        <input type="number" wire:model.live.debounce.500ms="minPrice" placeholder="PHP" class="h-12 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none transition-all">
                    </label>
                    <label class="block">
                        <span class="mb-2.5 block text-[10px] font-bold uppercase tracking-widest text-zinc-400">Max Price</span>
                        <input type="number" wire:model.live.debounce.500ms="maxPrice" placeholder="PHP" class="h-12 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none transition-all">
                    </label>
                </div>

                <div class="flex items-end">
                    <button type="button" wire:click="resetFilters" class="h-12 rounded-xl border border-zinc-200 bg-white px-8 text-xs font-bold text-zinc-600 transition hover:bg-zinc-50 hover:text-zinc-900">
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Featured Spotlight (Secondary, Scrollable) -->
    @if($featuredUnits->isNotEmpty())
        <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="space-y-6 transition-all duration-700 ease-out delay-150 motion-reduce:transition-none motion-reduce:opacity-100 motion-reduce:translate-y-0">
            <header class="flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-black uppercase tracking-[0.4em] text-zinc-900">Featured Spotlight</h2>
                    <div class="mt-2 h-1 w-8 bg-zinc-900"></div>
                </div>
            </header>

            <!-- Horizontal Scroll Slider for better handling of many units -->
            <div class="relative -mx-4 flex gap-6 overflow-x-auto px-4 pb-4 no-scrollbar scroll-smooth">
                @foreach($featuredUnits as $fUnit)
                    <div class="relative w-[280px] shrink-0 sm:w-[360px]" wire:key="featured-container-{{ $fUnit->id }}">
                        <!-- Comparison Toggle -->
                        <button 
                            wire:click.stop="toggleCompare({{ $fUnit->id }})"
                            class="absolute right-4 top-4 z-40 flex h-10 w-10 items-center justify-center rounded-full border shadow-lg transition-all duration-300 {{ in_array($fUnit->id, $compareIds) ? 'bg-zinc-900 border-zinc-900 text-white' : 'bg-white/90 border-zinc-200 text-zinc-400 backdrop-blur-md hover:bg-white hover:text-zinc-900 hover:border-zinc-300' }}"
                        >
                            @if(in_array($fUnit->id, $compareIds))
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 animate-showroom-fade-up" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            @endif
                        </button>

                        <a 
                            href="{{ route('units.show', $fUnit) }}" 
                            wire:navigate
                            class="group relative block aspect-[16/10] overflow-hidden rounded-[32px] bg-zinc-100 shadow-xl shadow-zinc-200/50 transition-all duration-500 hover:-translate-y-1 hover:shadow-2xl hover:shadow-zinc-300/50"
                            wire:key="featured-{{ $fUnit->id }}"
                            x-data="{ loaded: false }"
                        >
                            <!-- Skeleton Loader -->
                            <div x-show="!loaded" class="absolute inset-0 animate-pulse bg-zinc-200/50"></div>
                            
                            @if($fUnit->mainImage)
                                <img 
                                    src="{{ Storage::url($fUnit->mainImage->url) }}" 
                                    alt="{{ $fUnit->name }}" 
                                    @load="loaded = true"
                                    :class="loaded ? 'opacity-100' : 'opacity-0'"
                                    style="view-transition-name: unit-image-{{ $fUnit->id }}"
                                    class="h-full w-full object-cover transition-all duration-1000 group-hover:scale-110 motion-safe:transition-all motion-reduce:transition-none"
                                >
                            @else
                               <div x-init="loaded = true" class="absolute inset-0 flex items-center justify-center bg-zinc-100"></div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
                            <div class="absolute bottom-6 left-6 right-6">
                                <span class="rounded-lg bg-white/20 backdrop-blur-md px-2 py-0.5 text-[9px] font-black uppercase tracking-[0.2em] text-white">
                                    {{ $fUnit->category?->name ?? 'Vehicle' }}
                                </span>
                                <h3 class="mt-2 text-lg font-black tracking-tight text-white">{{ $fUnit->name }}</h3>
                                <p class="text-xs font-bold text-zinc-300">{{ $fUnit->formattedPrice() }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 3. Main Catalog Grid -->
    <div x-data="{ shown: false }" x-intersect.once.margin.-100px="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="space-y-8 transition-all duration-700 ease-out delay-300 motion-reduce:transition-none motion-reduce:opacity-100 motion-reduce:translate-y-0">
        <div wire:loading wire:target="search,categoryId,clearCategoryFilter" class="flex items-center gap-3 px-2 text-[10px] font-bold uppercase tracking-widest text-zinc-400">
            <svg class="h-3 w-3 animate-spin text-zinc-300" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Refreshing showroom...
        </div>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($units as $unit)
                <div class="relative group" wire:key="unit-container-{{ $unit->id }}">
                    <!-- Comparison Toggle -->
                    <button 
                        wire:click.stop="toggleCompare({{ $unit->id }})"
                        class="absolute right-4 top-4 z-40 flex h-10 w-10 items-center justify-center rounded-full border shadow-lg transition-all duration-300 {{ in_array($unit->id, $compareIds) ? 'bg-zinc-900 border-zinc-900 text-white' : 'bg-white/90 border-zinc-200 text-zinc-400 backdrop-blur-md hover:bg-white hover:text-zinc-900 hover:border-zinc-300' }}"
                        title="Add to compare"
                    >
                        @if(in_array($unit->id, $compareIds))
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 animate-showroom-fade-up" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        @endif
                    </button>

                    <a
                        href="{{ route('units.show', $unit) }}"
                        wire:navigate
                        class="flex flex-col overflow-hidden rounded-3xl border border-zinc-100 bg-white shadow-sm transition-all duration-300 group-hover:-translate-y-1.5 group-hover:shadow-xl group-hover:shadow-zinc-200/50"
                    >
                        <div class="relative aspect-[4/3] overflow-hidden bg-zinc-50">
                            @if ($unit->mainImage)
                                <img
                                    src="{{ Storage::url($unit->mainImage->url) }}"
                                    alt="{{ $unit->name }}"
                                    class="h-full w-full object-cover transition duration-700 group-hover:scale-105 {{ $unit->status === Unit::STATUS_SOLD ? 'grayscale opacity-60' : '' }}"
                                    loading="lazy"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-zinc-50 text-[10px] font-bold uppercase tracking-widest text-zinc-300">
                                    No Image
                                </div>
                            @endif

                            <div class="absolute left-4 top-4 z-20 flex flex-wrap items-center gap-2">
                                @if ($unit->is_featured)
                                    <span class="rounded-lg bg-zinc-900 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-white shadow-lg">
                                        Featured
                                    </span>
                                @endif
                                <span class="rounded-lg bg-white/90 backdrop-blur-md px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-zinc-600 shadow-sm">
                                    {{ $unit->category?->name ?? 'Vehicle' }}
                                </span>
                            </div>

                            <div class="absolute bottom-4 right-4 z-20">
                                <span class="rounded-lg px-3 py-1.5 text-[10px] font-black uppercase tracking-widest shadow-sm {{ $unit->status === Unit::STATUS_AVAILABLE ? 'bg-emerald-500 text-white' : 'bg-zinc-400 text-white' }}">
                                    {{ $unit->status === Unit::STATUS_AVAILABLE ? 'Available' : 'Sold' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex items-start justify-between gap-4">
                                <h2 class="text-xl font-bold tracking-tight text-zinc-900 group-hover:text-zinc-600 transition-colors">
                                    {{ $unit->name }}
                                </h2>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2">
                                @if($unit->year)
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">{{ $unit->year }}</span>
                                @endif
                                @if($unit->transmission)
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">{{ $unit->transmission }}</span>
                                @endif
                                @if($unit->mileage)
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">{{ number_format($unit->mileage) }} KM</span>
                                @endif
                            </div>

                            <div class="mt-auto pt-6">
                                <div class="flex items-center justify-between border-t border-zinc-50 pt-5">
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Price</span>
                                    <span class="text-lg font-black tracking-tight text-zinc-900">
                                        {{ $unit->formattedPrice() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="rounded-3xl border-2 border-dashed border-zinc-100 bg-zinc-50/50 p-16 text-center sm:col-span-2 lg:col-span-3">
                    <p class="text-sm font-bold uppercase tracking-widest text-zinc-400">No vehicles match your criteria</p>
                    <button wire:click="resetFilters" class="mt-4 text-xs font-bold text-zinc-900 underline underline-offset-4">Clear all filters</button>
                </div>
            @endforelse
        </div>

        <div class="pt-8">
            {{ $units->links() }}
        </div>
    </div>

    <!-- 4. Comparison Floating Tray -->
    @if(count($compareIds) > 0)
        <div 
            class="fixed bottom-8 left-1/2 z-50 -translate-x-1/2 animate-showroom-fade-up"
            x-data
        >
            <div class="flex items-center gap-6 rounded-full border border-zinc-800 bg-zinc-900 px-6 py-3 shadow-2xl shadow-zinc-900/40 backdrop-blur-md">
                <div class="flex -space-x-3 overflow-hidden">
                    @foreach($this->selectedUnits as $sUnit)
                        <div class="h-10 w-10 overflow-hidden rounded-full border-2 border-zinc-900 bg-zinc-800 shadow-sm" wire:key="tray-{{ $sUnit->id }}">
                            @if($sUnit->mainImage)
                                <img src="{{ Storage::url($sUnit->mainImage->url) }}" alt="" class="h-full w-full object-cover">
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="h-6 w-px bg-zinc-800"></div>

                <div class="flex items-center gap-4">
                    <flux:modal.trigger name="vehicle-compare">
                        <button 
                            class="text-[10px] font-black uppercase tracking-widest text-white hover:text-zinc-300 transition-colors"
                        >
                            Compare Now ({{ count($compareIds) }}/3)
                        </button>
                    </flux:modal.trigger>
                    
                    <button 
                        wire:click="clearCompare"
                        class="group flex h-6 w-6 items-center justify-center rounded-full bg-zinc-800 text-zinc-400 hover:bg-zinc-700 hover:text-white transition-all"
                        title="Clear all"
                    >
                        <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 5. Comparison Modal -->
    <flux:modal name="vehicle-compare" class="max-w-7xl !p-0">
        <div class="flex h-full flex-col bg-white">
            <header class="flex items-center justify-between border-b border-zinc-100 p-8">
                <div>
                    <h2 class="text-xs font-black uppercase tracking-[0.4em] text-zinc-900">Vehicle Comparison</h2>
                    <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400">Side-by-side neutral breakdown</p>
                </div>
                <flux:modal.close>
                    <button class="rounded-xl border border-zinc-200 bg-white p-2.5 text-zinc-400 hover:bg-zinc-50 hover:text-zinc-900 transition-all">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </flux:modal.close>
            </header>

            <div class="flex-1 overflow-y-auto p-8">
                <div class="grid grid-cols-3 gap-8">
                    @php 
                        $selected = $this->selectedUnits;
                        $slots = 3;
                    @endphp

                    @for($i = 0; $i < $slots; $i++)
                        @php $unit = $selected->values()[$i] ?? null; @endphp
                        
                        <div class="flex flex-col space-y-8">
                            @if($unit)
                                <!-- Unit Slot -->
                                <div class="space-y-6">
                                    <div class="relative aspect-[16/10] overflow-hidden rounded-3xl bg-zinc-50 border border-zinc-100">
                                        @if($unit->mainImage)
                                            <img src="{{ Storage::url($unit->mainImage->url) }}" alt="" class="h-full w-full object-cover">
                                        @endif
                                        <button 
                                            wire:click="toggleCompare({{ $unit->id }})"
                                            class="absolute right-4 top-4 rounded-full bg-white/90 p-2 text-zinc-400 hover:text-red-600 backdrop-blur-sm transition-colors shadow-sm"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                                        </button>
                                    </div>

                                    <div>
                                        <h3 class="text-xl font-bold tracking-tight text-zinc-900">{{ $unit->name }}</h3>
                                        <p class="text-sm font-black text-zinc-900 mt-1">{{ $unit->formattedPrice() }}</p>
                                    </div>
                                </div>

                                <div class="space-y-6 divide-y divide-zinc-50 pt-4">
                                    <div class="flex flex-col gap-1.5 pt-4">
                                        <span class="text-[9px] font-bold uppercase tracking-widest text-zinc-400">Year</span>
                                        <span class="text-sm font-bold text-zinc-900">{{ $unit->year ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1.5 pt-4">
                                        <span class="text-[9px] font-bold uppercase tracking-widest text-zinc-400">Mileage</span>
                                        <span class="text-sm font-bold text-zinc-900">{{ $unit->mileage ? number_format($unit->mileage) . ' KM' : 'N/A' }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1.5 pt-4">
                                        <span class="text-[9px] font-bold uppercase tracking-widest text-zinc-400">Transmission</span>
                                        <span class="text-sm font-bold text-zinc-900">{{ $unit->transmission ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1.5 pt-4">
                                        <span class="text-[9px] font-bold uppercase tracking-widest text-zinc-400">Fuel Type</span>
                                        <span class="text-sm font-bold text-zinc-900">{{ $unit->fuel_type ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1.5 pt-4">
                                        <span class="text-[9px] font-bold uppercase tracking-widest text-zinc-400">Category</span>
                                        <span class="text-sm font-bold text-zinc-900">{{ $unit->category?->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                
                                <div class="pt-8">
                                    <a href="{{ route('units.show', $unit) }}" wire:navigate class="flex h-12 w-full items-center justify-center rounded-xl bg-zinc-900 text-xs font-black uppercase tracking-widest text-white hover:bg-zinc-800 transition-colors">
                                        View Full Details
                                    </a>
                                </div>
                            @else
                                <!-- Empty Slot (Plus Button) -->
                                <flux:modal.close>
                                    <button 
                                        class="group flex flex-1 flex-col items-center justify-center rounded-[40px] border-2 border-dashed border-zinc-100 bg-zinc-50/50 p-12 transition-all hover:border-zinc-300 hover:bg-zinc-100/50"
                                    >
                                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white text-zinc-300 shadow-sm transition-all group-hover:scale-110 group-hover:text-zinc-900">
                                            <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                        </div>
                                        <span class="mt-6 text-[10px] font-black uppercase tracking-widest text-zinc-400 transition-colors group-hover:text-zinc-900">Add more to compare</span>
                                    </button>
                                </flux:modal.close>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </flux:modal>
</section>
