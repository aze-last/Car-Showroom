<div x-data="{ 
    scrollY: 0,
    handleScroll() { this.scrollY = window.scrollY }
}" @scroll.window="handleScroll">
    
    <!-- Render Preset Layout -->
    @include('livewire.public.presets.' . $designLayout)

    <!-- Global Feature: Featured Auction Spotlight (Optional) -->
    @if($designSettings['showAuctions'])
        <livewire:public.auction-spotlight />
    @endif

    <!-- Global Feature: Fluid Comparison Tray (Optional) -->
    @if($designSettings['showComparison'] && count($compareIds) > 0)
        <div class="fixed bottom-12 left-1/2 -translate-x-1/2 z-50 animate-showroom-fade-up">
            <div class="bg-black/90 backdrop-blur-2xl text-white rounded-[32px] px-10 py-5 shadow-[0_40px_100px_-15px_rgba(0,0,0,0.5)] flex items-center gap-10 border border-white/10">
                <div class="flex items-center gap-6">
                    <div class="flex -space-x-4">
                        @foreach($this->selectedUnits as $sUnit)
                            <div class="h-14 w-14 rounded-full border-4 border-black bg-zinc-800 overflow-hidden shadow-2xl transition-transform hover:scale-110 hover:z-30 relative" wire:key="tray-{{ $sUnit->id }}">
                                @if($sUnit->mainImage)
                                    <img src="{{ Storage::url($sUnit->mainImage->url) }}" alt="" class="h-full w-full object-cover">
                                @endif
                                <button wire:click="toggleCompare({{ $sUnit->id }})" class="absolute inset-0 bg-red-600/80 opacity-0 hover:opacity-100 flex items-center justify-center transition-opacity">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-white" stroke="currentColor" stroke-width="3"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                        @for($i = 0; $i < (3 - count($compareIds)); $i++)
                            <div class="h-14 w-14 rounded-full border-4 border-black bg-zinc-900 flex items-center justify-center border-dashed border-zinc-700">
                                <span class="text-zinc-600 text-xs font-bold">+</span>
                            </div>
                        @endfor
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-400">Ready to Compare</span>
                        <span class="text-sm font-bold tracking-tight">{{ count($compareIds) }} of 3 Assets</span>
                    </div>
                </div>

                <div class="w-px h-10 bg-white/10"></div>

                <div class="flex items-center gap-6">
                    <button wire:click="clearCompare" class="text-[10px] font-bold uppercase tracking-widest text-zinc-500 hover:text-white transition-colors">Reset</button>
                    <a href="{{ route('comparison') }}" wire:navigate class="bg-brand-primary text-white text-[11px] font-black uppercase tracking-widest px-10 py-4 rounded-2xl hover:bg-brand-primary-light transition-all shadow-[0_15px_30px_-5px_rgba(16,185,129,0.3)] hover:scale-105 active:scale-95">
                        Launch Comparison
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
