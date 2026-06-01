<!-- Minimalist Preset -->
<div class="max-w-7xl mx-auto px-6 py-20 space-y-24">
    <header class="text-center space-y-6">
        <h1 class="text-6xl md:text-8xl font-bold text-black tracking-tighter">{{ $designSettings['headline'] }}</h1>
        <p class="text-xl text-zinc-400 font-medium max-w-2xl mx-auto">{{ $designSettings['subtitle'] }}</p>
        <div class="flex justify-center gap-4 pt-8">
            <div class="relative w-full md:w-96">
                <input 
                    wire:model.live.debounce.300ms="search"
                    type="text" 
                    placeholder="Search collection..." 
                    class="w-full bg-zinc-50 border-zinc-100 rounded-full px-10 py-6 text-sm font-bold uppercase tracking-widest focus:ring-black focus:border-black transition-all"
                >
            </div>
        </div>
    </header>

    <div class="showroom-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
        @foreach($units as $unit)
            <a href="{{ route('units.show', $unit) }}" wire:navigate class="showroom-item group space-y-6">
                <div class="aspect-[16/10] rounded-[40px] overflow-hidden bg-zinc-50 border border-zinc-100 transition-all duration-700 group-hover:shadow-2xl">
                    @if($unit->mainImage)
                        <img src="{{ Storage::url($unit->mainImage->url) }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                    @endif
                </div>
                <div class="flex justify-between items-start px-4">
                    <div>
                        <h3 class="text-2xl font-bold text-black tracking-tight group-hover:text-zinc-600 transition-colors">{{ $unit->name }}</h3>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">{{ $unit->category->name }} • {{ $unit->year }}</p>
                    </div>
                    <p class="text-xl font-black text-black">{{ $unit->formattedPrice() }}</p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="pt-20 border-t border-zinc-50">
        {{ $units->links() }}
    </div>
</div>
