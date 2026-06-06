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
                    class="w-full bg-zinc-50 border-zinc-100 rounded-full px-10 py-6 text-[15px] font-bold uppercase tracking-widest focus:ring-black focus:border-black transition-all"
                >
            </div>
        </div>
    </header>

    <div class="showroom-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
        @foreach($units as $unit)
            <article class="showroom-item group" wire:key="unit-{{ $unit->id }}">
                <div class="showroom-item-inner bg-white rounded-[40px] overflow-hidden border border-zinc-100 transition-all duration-500 hover:shadow-[0_32px_64px_-16px_rgba(0,0,0,0.08)]">
                    <a href="{{ route('units.show', $unit) }}" wire:navigate class="block">
                        <div class="aspect-[16/10] overflow-hidden bg-zinc-50 relative">
                            @if($unit->mainImage)
                                <img src="{{ Storage::url($unit->mainImage->url) }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                            @endif
                            <div class="absolute top-6 left-6">
                                <span class="bg-white/90 backdrop-blur-md px-4 py-1.5 rounded-full text-[9px] font-bold uppercase tracking-widest border border-white/20 shadow-sm">
                                    {{ $unit->category->name }}
                                </span>
                            </div>
                        </div>
                        <div class="p-8 space-y-4">
                            <div class="flex justify-between items-start gap-4">
                                <h3 class="text-2xl font-bold text-black tracking-tighter">{{ $unit->name }}</h3>
                                <p class="text-xl font-bold text-black shrink-0">{{ $unit->formattedPrice() }}</p>
                            </div>
                            <div class="flex items-center justify-between pt-4 border-t border-zinc-50">
                                <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-[0.2em]">{{ $unit->year }} • {{ $unit->transmission }}</span>
                                <span class="flex items-center gap-2 text-[9px] font-black uppercase tracking-widest {{ $unit->status === App\Models\Unit::STATUS_AVAILABLE ? 'text-emerald-500' : 'text-zinc-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $unit->status === App\Models\Unit::STATUS_AVAILABLE ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-400' }}"></span>
                                    {{ $unit->status === App\Models\Unit::STATUS_AVAILABLE ? 'Available' : 'Archived' }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </article>
        @endforeach
    </div>

    <div class="pt-20 border-t border-zinc-50">
        {{ $units->links() }}
    </div>
</div>
