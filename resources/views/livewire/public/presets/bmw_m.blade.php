@use('App\Models\Unit')
<!-- BMW M-Performance Preset -->
<div class="bmw-preset min-h-screen"
     x-data="{ 
         scrollY: 0,
         handleScroll() { this.scrollY = window.scrollY }
     }" 
     @scroll.window="handleScroll">

    <!-- 1. Hero Photo Band (Full Bleed) -->
    @if($featuredUnits->isNotEmpty())
        @php $heroUnit = $featuredUnits->first(); @endphp
        <section class="relative w-full h-[80vh] min-h-[600px] bg-black flex items-center overflow-hidden">
            @if($heroUnit->mainImage)
                <img 
                    src="{{ Storage::url($heroUnit->mainImage->url) }}" 
                    alt="{{ $heroUnit->name }}" 
                    class="absolute inset-0 w-full h-full object-cover opacity-70"
                >
            @endif
            <!-- Subtle gradient to ensure text readability -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
            
            <div class="relative z-10 w-full max-w-[1440px] mx-auto px-4 md:px-16 lg:px-24">
                <div class="max-w-3xl">
                    <h3 class="font-saira display-xl text-white mb-6 leading-none">
                        {{ $heroUnit->name }}
                    </h3>
                    <p class="font-inter body-md text-[#e6e6e6] mb-12 max-w-xl">
                        {{ $designSettings['subtitle'] ?? 'Experience the pinnacle of automotive engineering.' }}
                        <br>
                        <span class="text-[#bbbbbb] block mt-2">{{ $heroUnit->category?->name }} // {{ $heroUnit->formattedPrice() }}</span>
                    </p>
                    
                    <a href="{{ route('units.show', $heroUnit) }}" wire:navigate class="inline-flex items-center justify-center bg-white text-black font-saira label-uppercase px-8 h-12 rounded-none hover:bg-[#e6e6e6] transition-colors border border-white">
                        Discover Detail
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- 2. Sticky Category Navigation -->
    <nav class="sticky top-20 z-40 w-full bg-black/95 backdrop-blur-md border-b border-[#3c3c3c] transition-all duration-300">
        <div class="w-full max-w-[1440px] mx-auto px-4 md:px-16 lg:px-24 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
            
            <!-- Category Tabs -->
            <div class="flex items-center gap-6 overflow-x-auto no-scrollbar w-full md:w-auto">
                <button 
                    wire:click="clearCategoryFilter"
                    class="font-saira label-uppercase transition-colors shrink-0 py-2 relative group {{ $categoryId === null ? 'text-white' : 'text-[#bbbbbb] hover:text-white' }}"
                >
                    All Models
                    @if($categoryId === null)
                        <div class="absolute bottom-0 left-0 w-full h-[2px] bg-white"></div>
                    @else
                        <div class="absolute bottom-0 left-0 w-full h-[2px] m-stripe opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    @endif
                </button>
                @foreach ($categories as $category)
                    <button 
                        wire:click="$set('categoryId', {{ $category->id }})"
                        class="font-saira label-uppercase transition-colors shrink-0 py-2 relative group {{ $categoryId === $category->id ? 'text-white' : 'text-[#bbbbbb] hover:text-white' }}"
                    >
                        {{ $category->name }}
                        @if($categoryId === $category->id)
                            <div class="absolute bottom-0 left-0 w-full h-[2px] bg-white"></div>
                        @else
                            <div class="absolute bottom-0 left-0 w-full h-[2px] m-stripe opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        @endif
                    </button>
                @endforeach
            </div>

            <!-- Search & Sort -->
            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <input 
                        wire:model.live.debounce.300ms="search"
                        type="text" 
                        placeholder="SEARCH MODELS..." 
                        class="w-full bg-[#1a1a1a] border border-[#3c3c3c] rounded-none pl-4 pr-10 h-12 font-saira label-uppercase text-white placeholder:text-[#7e7e7e] focus:border-white focus:ring-0 transition-colors"
                    >
                    <svg viewBox="0 0 24 24" fill="none" class="absolute right-4 top-1/2 -translate-y-1/2 h-4 w-4 text-[#7e7e7e]" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M20 20L16.65 16.65"/></svg>
                </div>
            </div>
        </div>
    </nav>

    <!-- 3. Models Grid (3-up Desktop, 2-up Tablet, 1-up Mobile) -->
    <section class="w-full max-w-[1440px] mx-auto px-4 md:px-16 lg:px-24 py-24">
        
        <div class="mb-16 flex items-center justify-between">
            <h2 class="font-saira display-lg text-white">THE LINEUP.</h2>
            <div class="h-[1px] flex-1 bg-[#3c3c3c] ml-8"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-16">
            @forelse ($units as $unit)
                <article class="group relative flex flex-col bg-black" wire:key="unit-{{ $unit->id }}">
                    <a href="{{ route('units.show', $unit) }}" wire:navigate class="absolute inset-0 z-20"></a>
                    <!-- Edge-to-edge photo with 16:10 ratio -->
                    <div class="relative w-full aspect-[16/10] overflow-hidden bg-[#1a1a1a]">
                        @if($unit->mainImage)
                            <img 
                                src="{{ Storage::url($unit->mainImage->url) }}" 
                                alt="{{ $unit->name }}" 
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 {{ $unit->status === Unit::STATUS_SOLD ? 'grayscale opacity-50' : '' }}"
                                loading="lazy"
                            >
                        @endif
                        
                        <!-- Status Badge -->
                        @if($unit->status === Unit::STATUS_SOLD)
                            <div class="absolute top-0 right-0 bg-[#e22718] text-white font-saira label-uppercase px-4 py-2">
                                Sold
                            </div>
                        @elseif($unit->is_featured)
                            <div class="absolute top-0 right-0 bg-[#1c69d4] text-white font-saira label-uppercase px-4 py-2">
                                Featured
                            </div>
                        @endif
                    </div>

                    <!-- Content below photo -->
                    <div class="pt-6 pb-2 flex flex-col flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-saira display-md text-white group-hover:text-[#bbbbbb] transition-colors leading-none">
                                {{ $unit->name }}
                            </h3>
                        </div>
                        
                        <p class="font-inter body-sm mb-6">
                            {{ $unit->year ?? 'N/A' }} // {{ $unit->transmission ?? 'N/A' }} // {{ $unit->fuel_type ?? 'N/A' }}
                        </p>

                        <div class="mt-auto pt-6 border-t border-[#3c3c3c] flex items-center justify-between">
                            <span class="font-saira title-lg text-white">{{ $unit->formattedPrice() }}</span>
                            <span class="font-saira label-uppercase text-white flex items-center gap-2 group-hover:text-[#0066b1] transition-colors">
                                EXPLORE THIS MODEL <span class="text-xl leading-none">&rarr;</span>
                            </span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-24 text-center border border-[#3c3c3c] bg-[#1a1a1a]">
                    <p class="font-saira display-sm text-[#7e7e7e] mb-4">NO MODELS FOUND.</p>
                    <p class="font-inter body-md text-[#bbbbbb]">Try adjusting your search or filters.</p>
                    <button wire:click="resetFilters" class="mt-8 font-saira label-uppercase text-white border border-white px-8 h-12 hover:bg-white hover:text-black transition-colors">
                        Clear Filters
                    </button>
                </div>
            @endforelse
        </div>
        
        <div class="mt-16 border-t border-[#3c3c3c] pt-8">
            {{ $units->links() }}
        </div>
    </section>

    <!-- 4. CTA Band Photo (Full Bleed) -->
    <section class="relative w-full h-[400px] bg-black flex items-center justify-center overflow-hidden border-t border-[#3c3c3c]">
        @if($featuredUnits->count() > 1 && $featuredUnits->last()->mainImage)
            <img 
                src="{{ Storage::url($featuredUnits->last()->mainImage->url) }}" 
                class="absolute inset-0 w-full h-full object-cover opacity-40 grayscale"
            >
        @endif
        <div class="absolute inset-0 bg-black/60"></div>
        
        <div class="relative z-10 text-center px-4">
            <h2 class="font-saira display-md text-white mb-8">READY TO DRIVE AN M?</h2>
            <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center justify-center border border-white text-white font-saira label-uppercase px-8 h-12 rounded-none hover:bg-white hover:text-black transition-colors">
                START YOUR COLLECTION
            </a>
        </div>
    </section>

</div>
