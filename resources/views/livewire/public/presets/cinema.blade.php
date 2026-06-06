<!-- Cinema Preset -->
<div class="flex flex-col">
    <!-- 1. Cinema Hero Section with Smooth Slider -->
    @if($featuredUnits->isNotEmpty())
        <section 
            x-data="{ 
                active: 0,
                count: {{ $featuredUnits->count() }},
                autoplay: null,
                init() {
                    this.autoplay = setInterval(() => {
                        this.active = (this.active + 1) % this.count
                    }, 5000)
                },
                stopAutoplay() { clearInterval(this.autoplay) }
            }"
            class="cinema-hero w-full relative h-[85vh] min-h-[600px] overflow-hidden bg-black flex items-center justify-center"
        >
            <!-- Slides -->
            @foreach($featuredUnits as $index => $heroUnit)
                <div 
                    x-show="active === {{ $index }}"
                    x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 scale-105"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-1000"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute inset-0 z-0"
                    wire:key="hero-slide-{{ $heroUnit->id }}"
                >
                    @if($heroUnit->mainImage)
                        <img 
                            src="{{ Storage::url($heroUnit->mainImage->url) }}" 
                            alt="{{ $heroUnit->name }}" 
                            class="hero-parallax-img w-full h-full object-cover opacity-60"
                        >
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/20 to-black"></div>

                    <!-- Hero Content (Inside Slide for animation grouping) -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-full max-w-7xl mx-auto px-6 text-center lg:text-left flex flex-col items-center lg:items-start">
                            <span 
                                x-show="active === {{ $index }}"
                                x-transition:enter="transition ease-out duration-1000 delay-300"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="cinema-hero-eyebrow inline-block px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-[10px] font-bold text-white uppercase tracking-[0.5em] mb-6"
                            >
                                Featured Masterpiece
                            </span>
                            <h1 
                                x-show="active === {{ $index }}"
                                x-transition:enter="transition ease-out duration-1000 delay-500"
                                x-transition:enter-start="opacity-0 translate-y-8"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="cinema-hero-h1 text-4xl md:text-8xl font-bold leading-tight text-white tracking-tighter mb-6 drop-shadow-2xl"
                            >
                                {{ $heroUnit->name }}
                            </h1>
                            <div 
                                x-show="active === {{ $index }}"
                                x-transition:enter="transition ease-out duration-1000 delay-700"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="cinema-hero-content flex flex-col md:flex-row items-center gap-8 text-white/70"
                            >
                                <p class="text-lg md:text-xl font-medium tracking-wide border-l-2 border-brand-primary pl-6">
                                    {{ $heroUnit->category?->name }} • {{ $heroUnit->formattedPrice() }}
                                </p>
                                <div class="cinema-hero-btn flex gap-4">
                                    <a href="{{ route('units.show', $heroUnit) }}" wire:navigate class="bg-white text-black font-bold uppercase tracking-widest text-[11px] px-10 py-4 rounded-xl hover:scale-105 transition-all duration-300 shadow-2xl shadow-white/10">
                                        Discover Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Slider Pagination Dots -->
            <div class="absolute bottom-32 left-1/2 -translate-x-1/2 z-30 flex gap-3">
                @foreach($featuredUnits as $index => $heroUnit)
                    <button 
                        @click="active = {{ $index }}; stopAutoplay()"
                        class="w-2 h-2 rounded-full transition-all duration-500"
                        :class="active === {{ $index }} ? 'bg-white w-8' : 'bg-white/30 hover:bg-white/50'"
                    ></button>
                @endforeach
            </div>

            <!-- Scroll Indicator -->
            <div class="absolute bottom-12 left-1/2 -translate-x-1/2 z-20 flex flex-col items-center gap-4 opacity-40">
                <span class="text-[9px] font-bold text-white uppercase tracking-[0.4em]">Scroll</span>
                <div class="w-px h-12 bg-gradient-to-b from-white to-transparent"></div>
            </div>
        </section>
    @endif

    <!-- 2. Sticky Glassmorphism Controls -->
    <nav class="sticky top-20 z-40 w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto -mt-16 transition-all duration-500"
         :class="scrollY > 400 ? 'pt-4' : 'pt-0'">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6 bg-white/70 backdrop-blur-2xl p-4 md:p-6 rounded-[32px] border border-white shadow-[0_32px_64px_-12px_rgba(0,0,0,0.1)] transition-all"
             :class="scrollY > 400 ? 'rounded-2xl scale-[0.98]' : 'rounded-[32px]'">
            
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-2 md:pb-0 md:flex-wrap md:justify-center w-full md:w-auto">
                <button 
                    wire:click="clearCategoryFilter"
                    class="category-pill shrink-0 text-[12px] font-bold uppercase tracking-[0.15em] px-6 py-3 rounded-full transition-all duration-300 {{ $categoryId === null ? 'bg-black text-white shadow-xl scale-105' : 'text-zinc-400 hover:text-black hover:bg-zinc-50' }}"
                >
                    All
                </button>
                @foreach ($categories as $category)
                    <button 
                        wire:click="$set('categoryId', {{ $category->id }})"
                        class="category-pill shrink-0 text-[12px] font-bold uppercase tracking-[0.15em] px-5 py-3 rounded-full transition-all duration-300 {{ $categoryId === $category->id ? 'bg-black text-white shadow-xl scale-105' : 'text-zinc-400 hover:text-black hover:bg-zinc-50' }}"
                    >
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="relative flex-1 md:w-64 group">
                    <svg viewBox="0 0 24 24" fill="none" class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-zinc-400 transition-colors group-focus-within:text-black" stroke="currentColor" stroke-width="3"><circle cx="11" cy="11" r="7"/><path d="M20 20L16.65 16.65" stroke-linecap="round"/></svg>
                    <input 
                        wire:model.live.debounce.300ms="search"
                        type="text" 
                        placeholder="Search collection..." 
                        class="w-full bg-zinc-100/50 border-none rounded-2xl pl-12 pr-6 py-3 text-[14px] font-bold uppercase tracking-widest placeholder:text-zinc-400 focus:ring-2 focus:ring-black/5 focus:bg-white transition-all"
                    >
                </div>
                <div class="h-8 w-px bg-zinc-200 hidden md:block"></div>
                <div class="flex items-center gap-3">
                    <select wire:model.live="sortBy" class="bg-transparent border-none text-[12px] font-black text-black focus:ring-0 p-0 cursor-pointer uppercase tracking-[0.15em]">
                        <option value="newest">Recent</option>    
                        <option value="price_desc">Premium</option> 
                        <option value="price_asc">Essential</option> 
                    </select>
                </div>
            </div>
        </div>
    </nav>

    <!-- 3. Dynamic Bento Grid Showcase -->
    <section class="w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto py-24 mb-32">   
        <div class="showroom-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-8 auto-rows-[420px]"> 
            @forelse ($units as $index => $unit)
                @php
                    $isLarge = $index === 0 && $categoryId === null && $search === '';
                    $colSpan = $isLarge ? 'lg:col-span-4' : 'lg:col-span-2';
                @endphp
                <article 
                    class="{{ $colSpan }} showroom-item relative group bg-transparent rounded-[40px] overflow-visible transition-all duration-700"
                    wire:key="unit-{{ $unit->id }}"
                >
                    <!-- Main Card Content (Tilts) -->
                    <div class="showroom-item-inner h-full flex flex-col bg-white rounded-[40px] overflow-hidden border border-zinc-100 hover:border-zinc-200 shadow-sm transition-all duration-500">
                        <div class="relative flex-1 overflow-hidden bg-zinc-50">    
                            @if($unit->mainImage)
                                <img 
                                    src="{{ Storage::url($unit->mainImage->url) }}" 
                                    alt="{{ $unit->name }}" 
                                    class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110 {{ $unit->status === App\Models\Unit::STATUS_SOLD ? 'grayscale opacity-60' : '' }}"
                                    loading="lazy"
                                >
                            @endif

                            <!-- Quick-Specs Reveal on Hover -->
                            <div class="absolute inset-0 z-20 bg-black/80 backdrop-blur-md opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-center p-12 text-white pointer-events-none">
                                <div class="space-y-6 transform translate-y-8 group-hover:translate-y-0 transition-transform duration-500">
                                    <h4 class="text-xs font-bold uppercase tracking-[0.4em] text-brand-primary">Technical Brief</h4>
                                    <div class="grid grid-cols-2 gap-y-6 gap-x-8">
                                        <div>
                                            <p class="text-[9px] uppercase tracking-widest text-white/40 mb-1">Year</p>
                                            <p class="text-sm font-bold">{{ $unit->year ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] uppercase tracking-widest text-white/40 mb-1">Transmission</p>
                                            <p class="text-sm font-bold">{{ $unit->transmission ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] uppercase tracking-widest text-white/40 mb-1">Fuel Type</p>
                                            <p class="text-sm font-bold">{{ $unit->fuel_type ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] uppercase tracking-widest text-white/40 mb-1">Distance</p>
                                            <p class="text-sm font-bold">{{ $unit->mileage ? number_format($unit->mileage).' KM' : 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Floating Badge -->
                            <div class="absolute top-8 left-8 flex flex-col gap-2 z-10">
                                <span class="bg-white/90 backdrop-blur-md text-black text-[9px] font-black uppercase tracking-[0.2em] px-4 py-1.5 rounded-full shadow-xl">
                                    {{ $unit->category?->name }}
                                </span>
                            </div>

                            <div class="absolute bottom-8 right-8 z-10">
                                <span class="text-[9px] font-bold uppercase tracking-widest px-4 py-1.5 rounded-full shadow-xl {{ $unit->status === App\Models\Unit::STATUS_AVAILABLE ? 'bg-brand-primary text-white' : 'bg-zinc-950 text-white' }}">
                                    {{ $unit->status === App\Models\Unit::STATUS_AVAILABLE ? 'Available' : 'Archived' }}
                                </span>
                            </div>      
                        </div>

                        <div class="p-10 flex flex-col bg-white">
                            <div class="flex justify-between items-start gap-4">
                                <div class="min-w-0">
                                    <h3 class="text-2xl md:text-3xl font-bold text-black tracking-tighter truncate mb-1">{{ $unit->name }}</h3>
                                    <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-zinc-400">
                                        Ref: #{{ substr($unit->public_id, -6) }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-xl md:text-2xl font-bold text-black tracking-tight block">{{ $unit->formattedPrice() }}</span>
                                    <span class="text-[8px] font-bold uppercase tracking-widest text-zinc-300">Listed Price</span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center mt-10">
                                <div class="flex gap-2 relative z-30">
                                    <button 
                                        wire:click.stop="toggleSave({{ $unit->id }})"
                                        class="w-12 h-12 rounded-2xl border border-zinc-100 flex items-center justify-center transition-all duration-300 {{ auth()->check() && auth()->user()->savedUnits()->where('unit_id', $unit->id)->exists() ? 'bg-red-50 border-red-100 text-red-600' : 'text-zinc-300 hover:text-black hover:bg-zinc-50' }}"
                                    >
                                        <svg viewBox="0 0 24 24" fill="{{ auth()->check() && auth()->user()->savedUnits()->where('unit_id', $unit->id)->exists() ? 'currentColor' : 'none' }}" class="h-5 w-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                    </button>
                                    @if($designSettings['showComparison'])
                                        <button 
                                            wire:click.stop="toggleCompare({{ $unit->id }})"
                                            class="w-12 h-12 rounded-2xl border border-zinc-100 flex items-center justify-center transition-all duration-300 {{ in_array($unit->id, $compareIds) ? 'bg-black border-black text-white' : 'text-zinc-300 hover:text-black hover:bg-zinc-50' }}"
                                        >
                                            @if(in_array($unit->id, $compareIds))
                                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            @else
                                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                            @endif
                                        </button>
                                    @endif
                                </div>
                                <a href="{{ route('units.show', $unit) }}" wire:navigate class="group/btn relative overflow-hidden bg-black text-white px-8 py-4 rounded-2xl font-bold uppercase tracking-widest text-[10px] transition-all hover:scale-105 active:scale-95 after:absolute after:inset-0 after:z-10">
                                    <span class="relative z-20 flex items-center gap-2">
                                        View Asset
                                        <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5 transform transition-transform group-hover/btn:translate-x-1" stroke="currentColor" stroke-width="3"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-48 text-center bg-white rounded-[40px] border border-zinc-100 border-dashed">
                    <span class="text-[14px] font-bold uppercase tracking-[0.6em] text-zinc-300 block mb-6">Collection Empty</span>
                    <button wire:click="resetFilters" class="text-[10px] font-black uppercase tracking-widest text-black underline underline-offset-8 decoration-zinc-200 hover:decoration-black transition-all">Clear Search Filters</button>
                </div>
            @endforelse
        </div>

        <div class="mt-24 px-12 py-8 bg-zinc-50 rounded-[40px] border border-zinc-100">
            {{ $units->links() }}
        </div>
    </section>
</div>
