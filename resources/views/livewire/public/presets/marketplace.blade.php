<!-- Marketplace Preset -->
<div class="flex flex-col space-y-12 py-12">
    <div class="max-w-7xl mx-auto w-full px-6 flex flex-col md:flex-row justify-between items-end gap-6">
        <div>
            <h1 class="text-5xl font-bold text-black tracking-tighter">{{ $designSettings['headline'] }}</h1>
            <p class="text-zinc-500 font-medium mt-2 uppercase text-xs tracking-widest">{{ $designSettings['subtitle'] }}</p>
        </div>
        <div class="flex gap-4">
            <div class="flex items-center gap-4 bg-white p-4 rounded-2xl border border-zinc-100 shadow-sm">
                <div class="h-10 w-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white">
                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Total Assets</p>
                    <p class="text-xl font-bold text-black">{{ $units->total() }} Units</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Filter Bar -->
    <nav class="sticky top-20 z-40 w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto transition-all duration-500">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6 bg-white p-6 rounded-[32px] border border-zinc-100 shadow-xl">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-2 md:pb-0 md:flex-wrap md:justify-center w-full md:w-auto">
                <button 
                    wire:click="clearCategoryFilter"
                    class="category-pill shrink-0 text-[10px] font-bold uppercase tracking-widest px-6 py-3 rounded-xl transition-all duration-300 {{ $categoryId === null ? 'bg-black text-white shadow-xl scale-105' : 'text-zinc-400 hover:text-black hover:bg-zinc-50 border border-zinc-100' }}"
                >
                    All
                </button>
                @foreach ($categories as $category)
                    <button 
                        wire:click="$set('categoryId', {{ $category->id }})"
                        class="category-pill shrink-0 text-[10px] font-bold uppercase tracking-widest px-5 py-3 rounded-xl transition-all duration-300 {{ $categoryId === $category->id ? 'bg-black text-white shadow-xl scale-105' : 'text-zinc-400 hover:text-black hover:bg-zinc-50 border border-zinc-100' }}"
                    >
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <div class="relative w-full md:w-64 group">
                <input 
                    wire:model.live.debounce.300ms="search"
                    type="text" 
                    placeholder="Find a vehicle..." 
                    class="w-full bg-zinc-50 border-none rounded-xl px-6 py-3 text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-black/5 focus:bg-white transition-all"
                >
            </div>
        </div>
    </nav>

    <!-- Grid Showcase -->
    <section class="w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto pb-32">   
        <div class="showroom-grid grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"> 
            @foreach ($units as $unit)
                <a href="{{ route('units.show', $unit) }}" wire:navigate class="showroom-item relative bg-transparent rounded-[32px] overflow-visible transition-all group flex flex-col cursor-pointer">
                    <div class="showroom-item-inner flex-grow flex flex-col bg-white rounded-[32px] border border-zinc-100 overflow-hidden hover:shadow-2xl transition-all duration-500">
                        <div class="relative aspect-[4/3] overflow-hidden bg-zinc-50">
                            @if($unit->mainImage)
                                <img src="{{ Storage::url($unit->mainImage->url) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @endif
                            <div class="absolute top-4 left-4">
                                <span class="bg-white/90 backdrop-blur-md text-black text-[8px] font-black uppercase tracking-widest px-3 py-1 rounded-full shadow-sm">{{ $unit->year }}</span>
                            </div>
                        </div>
                        <div class="p-8 space-y-6 flex-grow flex flex-col">
                            <div class="space-y-1">
                                <h3 class="text-xl font-bold text-black tracking-tight">{{ $unit->name }}</h3>
                                <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-[0.2em]">{{ $unit->category->name }}</p>
                            </div>
                            <div class="flex justify-between items-end mt-auto">
                                <p class="text-lg font-black text-black">{{ $unit->formattedPrice() }}</p>
                                <div class="h-10 w-10 rounded-xl bg-black text-white flex items-center justify-center group-hover:scale-110 transition-all">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="3"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-12">
            {{ $units->links() }}
        </div>
    </section>
</div>
