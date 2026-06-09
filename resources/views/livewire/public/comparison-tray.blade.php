@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div>
    @if($showComparison && count($compareIds) > 0)
        <div class="fixed bottom-6 left-4 right-4 md:left-1/2 md:right-auto md:-translate-x-1/2 z-50 animate-showroom-fade-up">
            <div class="bg-black/95 backdrop-blur-2xl text-white rounded-[30px] px-6 py-4 md:px-10 md:py-5 shadow-[0_40px_100px_-15px_rgba(0,0,0,0.6)] flex flex-col sm:flex-row items-center justify-between gap-6 md:gap-10 border border-white/10 max-w-lg md:max-w-none mx-auto">
                <div class="flex items-center gap-4 md:gap-6">
                    <div class="flex -space-x-3 md:-space-x-4">
                        @foreach($this->selectedUnits as $sUnit)
                            <div class="h-10 w-10 md:h-14 md:w-14 rounded-full border-2 md:border-4 border-black bg-zinc-800 overflow-hidden shadow-2xl transition-transform hover:scale-110 hover:z-30 relative" title="{{ $sUnit->name }}" wire:key="tray-{{ $sUnit->id }}">
                                @if($sUnit->mainImage)
                                    <img src="{{ Storage::url($sUnit->mainImage->url) }}" alt="{{ $sUnit->name }}" class="h-full w-full object-cover">
                                @endif
                                <button wire:click="toggleCompare({{ $sUnit->id }})" class="absolute inset-0 bg-red-600/80 opacity-0 hover:opacity-100 flex items-center justify-center transition-opacity">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 md:h-5 md:w-5 text-white" stroke="currentColor" stroke-width="3"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                        @for($i = 0; $i < (3 - count($compareIds)); $i++)
                            <div class="h-10 w-10 md:h-14 md:w-14 rounded-full border-2 md:border-4 border-black bg-zinc-900 flex items-center justify-center border-dashed border-zinc-700">
                                <span class="text-zinc-600 text-xs font-bold">+</span>
                            </div>
                        @endfor
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[8px] md:text-[10px] font-black uppercase tracking-[0.3em] text-emerald-400">Compare</span>
                        <span class="text-xs md:text-sm font-bold tracking-tight">{{ count($compareIds) }}/3 Assets</span>
                    </div>
                </div>

                <div class="hidden sm:block w-px h-8 md:h-10 bg-white/10"></div>

                <div class="flex items-center justify-between sm:justify-start w-full sm:w-auto gap-4 md:gap-6">
                    <button wire:click="clearCompare" class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-zinc-500 hover:text-white transition-colors">Reset</button>
                    <a href="{{ route('comparison') }}" wire:navigate class="flex-1 sm:flex-none text-center bg-brand-primary text-white text-[9px] md:text-[11px] font-black uppercase tracking-widest px-6 py-3 md:px-10 md:py-4 rounded-2xl hover:bg-brand-primary-light transition-all shadow-[0_15px_30px_-5px_rgba(16,185,129,0.3)] hover:scale-105 active:scale-95 leading-none">
                        Compare
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
