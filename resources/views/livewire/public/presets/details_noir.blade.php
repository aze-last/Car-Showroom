{{-- Gallery Noir — Unit Detail. Slideshow state lives entirely in Alpine (no server round-trips per slide). --}}
<div
    class="theme-noir-page -mt-20 bg-noir-canvas text-noir-ink min-h-screen"
    x-data="{
        active: 0,
        count: {{ max($unit->images->count(), 1) }},
        loaded: [],
        touchStartX: null,
        next() { this.active = (this.active + 1) % this.count },
        prev() { this.active = (this.active - 1 + this.count) % this.count },
        onTouchStart(e) { this.touchStartX = e.changedTouches[0].clientX },
        onTouchEnd(e) {
            if (this.touchStartX === null) return;
            const dx = e.changedTouches[0].clientX - this.touchStartX;
            if (Math.abs(dx) > 45) { dx < 0 ? this.next() : this.prev() }
            this.touchStartX = null;
        }
    }"
    @keydown.arrow-right.window="next()"
    @keydown.arrow-left.window="prev()"
>
    <main class="max-w-[1600px] mx-auto flex flex-col lg:flex-row min-h-screen">

        {{-- Slideshow: the focal point --}}
        <section
            class="relative lg:w-[62%] h-[55vh] lg:h-screen lg:sticky lg:top-0 bg-noir-surface overflow-hidden group/stage"
            @touchstart.passive="onTouchStart($event)"
            @touchend.passive="onTouchEnd($event)"
            aria-roledescription="carousel"
            aria-label="{{ $unit->name }} photographs"
        >
            @forelse($unit->images as $index => $img)
                <div
                    x-show="active === {{ $index }}"
                    x-transition:enter="transition-opacity ease-out duration-700"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-in duration-500"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0"
                    role="group"
                    aria-label="Photo {{ $index + 1 }} of {{ $unit->images->count() }}"
                    wire:key="noir-slide-{{ $img->id }}"
                >
                    {{-- Blur-up skeleton holds the frame until the image lands --}}
                    <div
                        class="absolute inset-0 bg-noir-elevated transition-opacity duration-700"
                        :class="loaded.includes({{ $index }}) ? 'opacity-0' : 'opacity-100 animate-pulse'"
                        aria-hidden="true"
                    ></div>
                    <img
                        src="{{ Storage::url($img->url) }}"
                        alt="{{ $unit->name }} — photograph {{ $index + 1 }}"
                        @load="loaded.push({{ $index }})"
                        {{ $index === 0 ? 'fetchpriority=high' : 'loading=lazy' }}
                        class="w-full h-full object-cover transition-[filter] duration-700 {{ $unit->isSold() ? 'grayscale opacity-60' : '' }}"
                        :class="[active === {{ $index }} ? 'noir-ken-burns' : '', loaded.includes({{ $index }}) ? 'blur-0' : 'blur-md']"
                    >
                </div>
            @empty
                <div class="absolute inset-0 flex items-center justify-center text-noir-muted text-[10px] font-bold uppercase tracking-[0.4em]">
                    Imagery Pending
                </div>
            @endforelse

            <div class="noir-grain"></div>
            <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-black/70 to-transparent pointer-events-none"></div>

            {{-- Status pill --}}
            <span class="absolute top-8 left-8 z-20 text-[9px] font-bold uppercase tracking-[0.25em] px-4 py-2 rounded-full {{ $unit->isAvailable() ? 'noir-pill-available' : 'noir-pill-sold' }}">
                {{ $unit->isAvailable() ? 'Available' : 'Sold' }}
            </span>

            @if($unit->images->count() > 1)
                {{-- Desktop arrows, revealed on hover --}}
                <button
                    @click="prev()"
                    class="hidden lg:flex absolute left-6 top-1/2 -translate-y-1/2 z-20 h-12 w-12 items-center justify-center rounded-full border border-noir-line-strong bg-black/40 backdrop-blur-md text-noir-ink opacity-0 group-hover/stage:opacity-100 focus-visible:opacity-100 transition-all duration-300 hover:border-noir-gold hover:text-noir-gold-bright cursor-pointer"
                    aria-label="Previous photograph"
                >
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button
                    @click="next()"
                    class="hidden lg:flex absolute right-6 top-1/2 -translate-y-1/2 z-20 h-12 w-12 items-center justify-center rounded-full border border-noir-line-strong bg-black/40 backdrop-blur-md text-noir-ink opacity-0 group-hover/stage:opacity-100 focus-visible:opacity-100 transition-all duration-300 hover:border-noir-gold hover:text-noir-gold-bright cursor-pointer"
                    aria-label="Next photograph"
                >
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>

                {{-- Thumbnail strip --}}
                <div class="absolute inset-x-0 bottom-0 z-20 p-6 flex gap-3 overflow-x-auto no-scrollbar">
                    @foreach($unit->images as $index => $image)
                        <button
                            @click="active = {{ $index }}"
                            class="shrink-0 w-20 h-14 overflow-hidden border transition-all duration-300 cursor-pointer"
                            :class="active === {{ $index }} ? 'border-noir-gold opacity-100' : 'border-transparent opacity-40 hover:opacity-80'"
                            :aria-current="active === {{ $index }} ? 'true' : 'false'"
                            aria-label="Show photo {{ $index + 1 }}"
                        >
                            <img src="{{ Storage::url($image->url) }}" alt="" loading="lazy" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Info panel — staggers in after the stage --}}
        <section class="lg:w-[38%] px-6 sm:px-10 lg:px-14 py-16 lg:py-28 flex flex-col gap-12 border-l border-noir-line">
            <header class="noir-reveal" style="animation-delay: 0.2s">
                <p class="text-[10px] font-bold uppercase tracking-[0.5em] text-noir-gold mb-6">
                    {{ $unit->category?->name }} @if($unit->year) · {{ $unit->year }} @endif
                </p>
                <h1 class="font-noir-display text-4xl md:text-6xl leading-[1.02] tracking-tight mb-8">{{ $unit->name }}</h1>
                <p class="text-3xl font-light text-noir-gold-bright tabular-nums">{{ $unit->formattedPrice() }}</p>
                <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-noir-muted mt-2">Exclusive of taxes & registration</p>
            </header>

            <div class="noir-reveal flex flex-col gap-4" style="animation-delay: 0.35s">
                @if(App\Models\Setting::get('design_show_inquiries', true))
                    <button
                        @if(auth()->check()) wire:click="$dispatch('open-chat')" @else onclick="window.location.href='{{ route('login') }}'" @endif
                        class="w-full cursor-pointer bg-noir-gold text-black font-bold uppercase tracking-[0.3em] text-[11px] py-5 hover:bg-noir-gold-bright transition-colors duration-300"
                    >
                        Request Information
                    </button>
                @endif
                @if(App\Models\Setting::get('design_show_comparison', true))
                    <button
                        wire:click="toggleCompare({{ $unit->id }})"
                        class="w-full cursor-pointer border border-noir-line-strong text-noir-ink font-bold uppercase tracking-[0.3em] text-[11px] py-5 hover:border-noir-gold hover:text-noir-gold-bright transition-colors duration-300"
                    >
                        {{ in_array($unit->id, $compareIds) ? 'In Comparison' : 'Compare' }}
                    </button>
                @endif
            </div>

            {{-- Specifications --}}
            <dl class="noir-reveal grid grid-cols-2 gap-x-8" style="animation-delay: 0.5s">
                @foreach([
                    'Year' => $unit->year,
                    'Distance' => $unit->mileage ? number_format($unit->mileage).' km' : null,
                    'Transmission' => $unit->transmission,
                    'Fuel' => $unit->fuel_type,
                    'Class' => $unit->category?->name,
                    'Reference' => '#'.substr($unit->public_id, -8),
                ] as $label => $value)
                    <div class="py-4 border-b border-noir-line">
                        <dt class="text-[9px] font-bold uppercase tracking-[0.3em] text-noir-muted mb-1.5">{{ $label }}</dt>
                        <dd class="text-sm font-medium text-noir-ink">{{ $value ?? '—' }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="noir-reveal" style="animation-delay: 0.6s">
                <h2 class="text-[10px] font-bold uppercase tracking-[0.4em] text-noir-gold mb-5">Curator's Note</h2>
                <p class="text-[15px] leading-[1.8] text-noir-body font-light">
                    {{ $unit->description ?: 'This piece represents the pinnacle of its class — meticulously inspected and certified to the gallery\'s standard of excellence.' }}
                </p>
            </div>

            @auth
                @if(App\Models\Setting::get('design_show_inquiries', true))
                    <div class="noir-reveal" style="animation-delay: 0.7s">
                        <livewire:public.chat-inquiry :unit="$unit" />
                    </div>
                @endif
            @endauth
        </section>
    </main>

    {{-- Similar units --}}
    @if($similarUnits->isNotEmpty())
        <section class="max-w-[1600px] mx-auto px-6 sm:px-10 lg:px-14 py-24 border-t border-noir-line">
            <h2 class="font-noir-display text-3xl text-noir-ink mb-12">Also in the Collection</h2>
            <div class="noir-stagger grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($similarUnits as $sUnit)
                    <a href="{{ route('units.show', $sUnit) }}" wire:navigate class="group cursor-pointer" wire:key="noir-similar-{{ $sUnit->id }}">
                        <div class="relative aspect-[4/3] overflow-hidden bg-noir-surface border border-noir-line group-hover:border-noir-line-strong transition-colors duration-500">
                            @if ($sUnit->mainImage)
                                <img src="{{ Storage::url($sUnit->mainImage->url) }}" alt="{{ $sUnit->name }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-[900ms] group-hover:scale-105">
                            @endif
                            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/80 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-5">
                                <h3 class="font-noir-display text-xl text-noir-ink leading-tight">{{ $sUnit->name }}</h3>
                                <p class="text-sm text-noir-gold-bright mt-1 tabular-nums">{{ $sUnit->formattedPrice() }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>
