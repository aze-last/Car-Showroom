{{-- Gallery Noir Preset — dark cinematic dealership. Champagne gold accent, serif display type. --}}
<div class="theme-noir-page -mt-20 bg-noir-canvas text-noir-ink min-h-screen">

    {{-- 1. Full-bleed dark hero --}}
    <section class="relative w-full min-h-[92vh] flex items-end overflow-hidden bg-noir-canvas">
        {{-- Backdrop: featured image w/ Ken Burns, or gradient texture fallback --}}
        @php $heroUnit = $featuredUnits->first(); @endphp
        <div class="absolute inset-0">
            @if($heroUnit?->mainImage)
                <img
                    src="{{ Storage::url($heroUnit->mainImage->url) }}"
                    alt=""
                    aria-hidden="true"
                    class="noir-ken-burns w-full h-full object-cover opacity-40"
                >
            @else
                <div class="w-full h-full bg-[radial-gradient(ellipse_at_top,rgba(201,169,97,0.10),transparent_55%),radial-gradient(ellipse_at_bottom_left,rgba(255,255,255,0.04),transparent_50%)]"></div>
            @endif
            <div class="noir-grain"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-noir-canvas via-noir-canvas/55 to-noir-canvas/25"></div>
        </div>

        <div class="relative z-10 w-full max-w-7xl mx-auto px-6 lg:px-8 pb-24 pt-40">
            <p class="noir-reveal text-[10px] font-bold uppercase tracking-[0.6em] text-noir-gold mb-8" style="animation-delay: 0.1s">
                {{ \App\Models\Setting::get('shop_name', 'The Gallery') }}
            </p>
            <h1 class="noir-reveal font-noir-display text-5xl md:text-8xl leading-[0.95] tracking-tight text-noir-ink max-w-4xl mb-6" style="animation-delay: 0.25s">
                {{ $designSettings['headline'] }}
            </h1>
            <p class="noir-reveal text-base md:text-lg text-noir-body max-w-xl font-light leading-relaxed" style="animation-delay: 0.4s">
                {{ $designSettings['subtitle'] }}
            </p>

            @if($heroUnit)
                <a href="{{ route('units.show', $heroUnit) }}" wire:navigate
                   class="noir-reveal inline-flex items-center gap-4 mt-12 group cursor-pointer" style="animation-delay: 0.55s">
                    <span class="h-px w-16 bg-noir-gold transition-all duration-500 group-hover:w-24"></span>
                    <span class="text-[11px] font-bold uppercase tracking-[0.35em] text-noir-ink group-hover:text-noir-gold-bright transition-colors duration-300">
                        View {{ $heroUnit->name }}
                    </span>
                </a>
            @endif
        </div>
    </section>

    {{-- 2. Filter bar: category pills + search --}}
    <nav class="sticky top-20 z-40 bg-noir-canvas/85 backdrop-blur-xl border-y border-noir-line" aria-label="Collection filters">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-5 flex flex-col md:flex-row items-center gap-5 justify-between">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar w-full md:w-auto">
                <button
                    wire:click="clearCategoryFilter"
                    class="shrink-0 cursor-pointer text-[10px] font-bold uppercase tracking-[0.25em] px-5 py-2.5 rounded-full border transition-all duration-300 {{ $categoryId === null ? 'border-noir-gold text-noir-gold-bright bg-noir-gold/10' : 'border-transparent text-noir-muted hover:text-noir-ink hover:border-noir-line-strong' }}"
                >
                    All
                </button>
                @foreach ($categories as $category)
                    <button
                        wire:click="$set('categoryId', {{ $category->id }})"
                        class="shrink-0 cursor-pointer text-[10px] font-bold uppercase tracking-[0.25em] px-5 py-2.5 rounded-full border transition-all duration-300 {{ $categoryId === $category->id ? 'border-noir-gold text-noir-gold-bright bg-noir-gold/10' : 'border-transparent text-noir-muted hover:text-noir-ink hover:border-noir-line-strong' }}"
                    >
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <div class="flex items-center gap-5 w-full md:w-auto">
                <div class="relative flex-1 md:w-72 group">
                    <svg viewBox="0 0 24 24" fill="none" class="absolute left-0 top-1/2 -translate-y-1/2 h-4 w-4 text-noir-muted transition-colors group-focus-within:text-noir-gold" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20L16.65 16.65" stroke-linecap="round"/></svg>
                    <label for="noir-search" class="sr-only">Search collection</label>
                    <input
                        id="noir-search"
                        wire:model.live.debounce.300ms="search"
                        type="search"
                        placeholder="Search the collection"
                        class="w-full bg-transparent border-0 border-b border-noir-line rounded-none pl-8 pr-2 py-2.5 text-sm text-noir-ink placeholder:text-noir-muted focus:border-noir-gold focus:ring-0 transition-colors duration-300"
                    >
                </div>
                <label for="noir-sort" class="sr-only">Sort collection</label>
                <select id="noir-sort" wire:model.live="sortBy" class="cursor-pointer bg-transparent border-none text-[10px] font-bold text-noir-body uppercase tracking-[0.25em] focus:ring-0 p-0 [&>option]:bg-noir-surface [&>option]:text-noir-ink">
                    <option value="newest">Latest</option>
                    <option value="price_desc">Premium</option>
                    <option value="price_asc">Essential</option>
                </select>
            </div>
        </div>
    </nav>

    {{-- 3. Gallery grid --}}
    <section class="max-w-7xl mx-auto px-6 lg:px-8 py-20 lg:py-28">
        <div class="flex items-baseline justify-between mb-12">
            <h2 class="font-noir-display text-3xl md:text-4xl text-noir-ink">The Collection</h2>
            <span class="text-[10px] font-bold uppercase tracking-[0.35em] text-noir-muted">{{ $units->total() }} {{ Str::plural('piece', $units->total()) }}</span>
        </div>

        <div class="noir-stagger grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-8 gap-y-16">
            @forelse ($units as $unit)
                <article class="group relative" wire:key="noir-unit-{{ $unit->id }}">
                    <a href="{{ route('units.show', $unit) }}" wire:navigate
                       class="block cursor-pointer"
                       aria-label="View {{ $unit->name }}">
                        {{-- Image-dominant card --}}
                        <div class="relative aspect-[4/3] overflow-hidden bg-noir-surface border border-noir-line transition-colors duration-500 group-hover:border-noir-line-strong">
                            @if($unit->mainImage)
                                <img
                                    src="{{ Storage::url($unit->mainImage->url) }}"
                                    alt="{{ $unit->name }}"
                                    loading="lazy"
                                    class="w-full h-full object-cover transition-transform duration-[900ms] ease-out group-hover:scale-105 {{ $unit->isSold() ? 'grayscale opacity-50' : '' }}"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center text-noir-muted text-[10px] uppercase tracking-[0.35em]">No Imagery</div>
                            @endif

                            {{-- Legibility gradient, no solid box --}}
                            <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>

                            {{-- Category tag --}}
                            <span class="absolute top-5 left-5 text-[9px] font-bold uppercase tracking-[0.3em] text-noir-ink/70">
                                {{ $unit->category?->name }}
                            </span>

                            {{-- Overlaid name / price / status --}}
                            <div class="absolute inset-x-0 bottom-0 p-6 flex items-end justify-between gap-4">
                                <div class="min-w-0">
                                    <h3 class="font-noir-display text-2xl md:text-[1.7rem] leading-tight text-noir-ink truncate">{{ $unit->name }}</h3>
                                    <p class="text-sm font-medium text-noir-gold-bright mt-1 tabular-nums">{{ $unit->formattedPrice() }}</p>
                                </div>
                                <span class="shrink-0 text-[9px] font-bold uppercase tracking-[0.25em] px-3.5 py-1.5 rounded-full {{ $unit->isAvailable() ? 'noir-pill-available' : 'noir-pill-sold' }}">
                                    {{ $unit->isAvailable() ? 'Available' : 'Sold' }}
                                </span>
                            </div>
                        </div>
                    </a>

                    {{-- Secondary actions below card --}}
                    <div class="flex items-center justify-between mt-4">
                        <span class="text-[9px] font-bold uppercase tracking-[0.3em] text-noir-muted">Ref #{{ substr($unit->public_id, -6) }}</span>
                        <div class="flex gap-3">
                            <button
                                wire:click="toggleSave({{ $unit->id }})"
                                class="cursor-pointer p-2 transition-colors duration-300 {{ auth()->check() && auth()->user()->savedUnits()->where('unit_id', $unit->id)->exists() ? 'text-noir-gold' : 'text-noir-muted hover:text-noir-ink' }}"
                                aria-label="Save {{ $unit->name }}"
                            >
                                <svg viewBox="0 0 24 24" fill="{{ auth()->check() && auth()->user()->savedUnits()->where('unit_id', $unit->id)->exists() ? 'currentColor' : 'none' }}" class="h-4 w-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                            </button>
                            @if($designSettings['showComparison'])
                                <button
                                    wire:click="toggleCompare({{ $unit->id }})"
                                    class="cursor-pointer p-2 transition-colors duration-300 {{ in_array($unit->id, $compareIds) ? 'text-noir-gold' : 'text-noir-muted hover:text-noir-ink' }}"
                                    aria-label="{{ in_array($unit->id, $compareIds) ? 'Remove from comparison' : 'Add to comparison' }}"
                                >
                                    @if(in_array($unit->id, $compareIds))
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    @endif
                                </button>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                {{-- Empty state, on-theme --}}
                <div class="col-span-full py-40 text-center border border-dashed border-noir-line">
                    <p class="font-noir-display text-3xl text-noir-body mb-3">Nothing in view</p>
                    <p class="text-[10px] font-bold uppercase tracking-[0.4em] text-noir-muted mb-10">The collection holds no match for this search</p>
                    <button wire:click="resetFilters" class="cursor-pointer text-[10px] font-bold uppercase tracking-[0.3em] text-noir-gold-bright border-b border-noir-gold/40 pb-1 hover:border-noir-gold transition-colors duration-300">
                        Clear all filters
                    </button>
                </div>
            @endforelse
        </div>

        <div class="mt-20 pt-10 border-t border-noir-line">
            {{ $units->links() }}
        </div>
    </section>
</div>
