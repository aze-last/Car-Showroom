@php
    use App\Models\Unit;
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="flex flex-col">
    <!-- 1. Editorial Hero Section -->
    @if($featuredUnits->isNotEmpty())
        @php $heroUnit = $featuredUnits->first(); @endphp
        <section class="w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto mt-6 mb-12 animate-showroom-fade-up">
            <div class="relative w-full h-[600px] min-h-[400px] rounded-[32px] overflow-hidden ambient-shadow group">
                @if($heroUnit->mainImage)
                    <img 
                        src="{{ Storage::url($heroUnit->mainImage->url) }}" 
                        alt="{{ $heroUnit->name }}" 
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
                    >
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-8 sm:p-12 flex flex-col gap-2">      
                    <span class="text-[12px] font-bold text-white uppercase tracking-[0.4em]">Featured Release</span>
                    <h1 class="text-5xl sm:text-7xl font-bold leading-none text-white tracking-tighter">{{ $heroUnit->name }}</h1>
                    <p class="text-lg text-white/80 max-w-md mt-4 font-medium">{{ $heroUnit->category?->name }} • {{ $heroUnit->formattedPrice() }}</p>
                    <div class="mt-8 flex gap-4">
                        <a href="{{ route('units.show', $heroUnit) }}" wire:navigate class="bg-white text-black font-bold uppercase tracking-widest text-[12px] px-8 py-3 rounded-full hover:bg-zinc-100 transition-colors">View Detail</a>
                        <button wire:click="toggleCompare({{ $heroUnit->id }})" class="border border-white/30 backdrop-blur-md text-white font-bold uppercase tracking-widest text-[12px] px-8 py-3 rounded-full hover:bg-white/10 transition-colors">
                            {{ in_array($heroUnit->id, $compareIds) ? 'Selected' : 'Compare' }}
                        </button>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- 2. Catalog Controls (Filters) -->
    <section class="w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto mb-12 animate-showroom-fade-up" style="animation-delay: 0.1s;">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 bg-white/50 backdrop-blur-sm p-6 rounded-[32px] border border-gallery-outline/20">
            <div class="flex flex-wrap gap-2">
                <button 
                    wire:click="clearCategoryFilter"
                    class="text-[11px] font-bold uppercase tracking-widest px-6 py-2.5 rounded-full transition-all {{ $categoryId === null ? 'bg-black text-white shadow-lg' : 'bg-gallery-surface-low text-zinc-500 hover:text-black hover:bg-gallery-surface-high' }}"
                >
                    All
                </button>
                @foreach ($categories as $category)
                    <button 
                        wire:click="$set('categoryId', {{ $category->id }})"
                        class="text-[11px] font-bold uppercase tracking-widest px-6 py-2.5 rounded-full transition-all {{ $categoryId === $category->id ? 'bg-black text-white shadow-lg' : 'bg-gallery-surface-low text-zinc-500 hover:text-black hover:bg-gallery-surface-high' }}"
                    >
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <div class="flex items-center gap-6 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <input 
                        wire:model.live.debounce.300ms="search"
                        type="text" 
                        placeholder="Search models..." 
                        class="w-full bg-gallery-surface-low border-none rounded-full px-6 py-2.5 text-sm font-medium focus:ring-2 focus:ring-black/5 transition-all"
                    >
                </div>
                <div class="flex items-center gap-3 text-zinc-400">
                    <span class="text-[10px] font-bold uppercase tracking-widest">Sort</span>
                    <select wire:model.live="sortBy" class="bg-transparent border-none text-[12px] font-bold text-black focus:ring-0 p-0 cursor-pointer uppercase tracking-widest">
                        <option value="newest">Newest</option>    
                        <option value="price_desc">Price: High</option> 
                        <option value="price_asc">Price: Low</option> 
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Main Vehicle Grid -->
    <section class="w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto mb-32">   
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"> 
            @forelse ($units as $unit)
                <article 
                    class="bg-gallery-surface-lowest rounded-[32px] border border-gallery-outline/30 overflow-hidden hover-scale ambient-shadow flex flex-col group animate-showroom-fade-up"
                    wire:key="unit-{{ $unit->id }}"
                >
                    <div class="relative h-72 overflow-hidden bg-gallery-surface-low">    
                        @if($unit->mainImage)
                            <img 
                                src="{{ Storage::url($unit->mainImage->url) }}" 
                                alt="{{ $unit->name }}" 
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 {{ $unit->status === Unit::STATUS_SOLD ? 'grayscale opacity-60' : '' }}"
                                loading="lazy"
                            >
                        @endif
                        
                        <div class="absolute top-4 left-4 flex gap-2">
                            @if($unit->is_featured)
                                <span class="bg-black text-white text-[9px] font-bold uppercase tracking-widest px-3 py-1 rounded-full shadow-lg">Featured</span>
                            @endif
                            <span class="bg-white/90 backdrop-blur-md text-zinc-900 text-[9px] font-bold uppercase tracking-widest px-3 py-1 rounded-full shadow-sm">{{ $unit->category?->name }}</span>
                        </div>

                        <div class="absolute top-4 right-4">
                            <span class="text-[9px] font-bold uppercase tracking-widest px-3 py-1 rounded-full shadow-sm {{ $unit->status === Unit::STATUS_AVAILABLE ? 'bg-emerald-500 text-white' : 'bg-zinc-400 text-white' }}">
                                {{ $unit->status === Unit::STATUS_AVAILABLE ? 'Available' : 'Sold' }}
                            </span>
                        </div>      
                    </div>

                    <div class="p-8 flex flex-col flex-grow">
                        <h3 class="text-2xl font-bold text-black mb-1 tracking-tight group-hover:text-zinc-600 transition-colors">{{ $unit->name }}</h3>
                        <div class="flex gap-4 mt-1">
                             @if($unit->year)
                                <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">{{ $unit->year }}</span>
                            @endif
                            @if($unit->transmission)
                                <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">{{ $unit->transmission }}</span>
                            @endif
                        </div>

                        <div class="flex justify-between items-end mt-8 border-t border-gallery-outline/10 pt-6">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-1">Price</span>
                                <span class="text-xl font-bold text-black tracking-tight">{{ $unit->formattedPrice() }}</span>
                            </div>
                            <div class="flex gap-2">
                                <button 
                                    wire:click="toggleSave({{ $unit->id }})"
                                    class="w-10 h-10 rounded-full border border-gallery-outline/30 flex items-center justify-center transition-all duration-300 {{ auth()->check() && auth()->user()->savedUnits()->where('unit_id', $unit->id)->exists() ? 'bg-red-50 border-red-100 text-red-600' : 'text-zinc-400 hover:border-black hover:text-black' }}"
                                    title="Save to Gallery"
                                >
                                    <svg viewBox="0 0 24 24" fill="{{ auth()->check() && auth()->user()->savedUnits()->where('unit_id', $unit->id)->exists() ? 'currentColor' : 'none' }}" class="h-4 w-4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                </button>
                                <button 
                                    wire:click="toggleCompare({{ $unit->id }})"
                                    class="w-10 h-10 rounded-full border border-gallery-outline/30 flex items-center justify-center transition-all duration-300 {{ in_array($unit->id, $compareIds) ? 'bg-black border-black text-white' : 'text-zinc-400 hover:border-black hover:text-black' }}"
                                    title="Add to Compare"
                                >
                                    @if(in_array($unit->id, $compareIds))
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    @endif
                                </button>
                                <a href="{{ route('units.show', $unit) }}" wire:navigate class="w-10 h-10 rounded-full bg-black flex items-center justify-center text-white hover:opacity-80 transition-opacity">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-32 text-center">
                    <span class="text-[12px] font-bold uppercase tracking-[0.4em] text-zinc-300">No vehicles found matching your criteria</span>
                    <button wire:click="resetFilters" class="mt-4 block mx-auto text-[10px] font-bold uppercase tracking-widest text-black underline underline-offset-4">Reset all filters</button>
                </div>
            @endforelse
        </div>

        <div class="mt-16">
            {{ $units->links() }}
        </div>
    </section>

    <!-- 4. Comparison Floating Tray (Pill Design) -->
    @if(count($compareIds) > 0)
        <div class="fixed bottom-10 left-1/2 -translate-x-1/2 z-50 animate-showroom-fade-up">
            <div class="bg-black text-white rounded-full px-8 py-4 ambient-shadow flex items-center gap-8 border border-white/10 backdrop-blur-md">
                <div class="flex items-center gap-4">
                    <div class="flex -space-x-3">
                        @foreach($this->selectedUnits as $sUnit)
                            <div class="h-10 w-10 rounded-full border-2 border-black bg-zinc-800 overflow-hidden shadow-sm" wire:key="tray-{{ $sUnit->id }}">
                                @if($sUnit->mainImage)
                                    <img src="{{ Storage::url($sUnit->mainImage->url) }}" alt="" class="h-full w-full object-cover">
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-white/60">Compare</span>
                        <span class="text-[12px] font-bold">{{ count($compareIds) }} selected</span>
                    </div>
                </div>

                <div class="w-px h-8 bg-white/20"></div>

                <div class="flex items-center gap-4">
                    <button wire:click="clearCompare" class="text-[10px] font-bold uppercase tracking-widest text-white/60 hover:text-white transition-colors">Clear</button>
                    <a href="{{ route('comparison') }}" wire:navigate class="bg-white text-black text-[11px] font-bold uppercase tracking-widest px-6 py-2 rounded-full hover:bg-zinc-100 transition-all hover:scale-105">Launch Gallery</a>
                </div>
            </div>
        </div>
    @endif
</div>
