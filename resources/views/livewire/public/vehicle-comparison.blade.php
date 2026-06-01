@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="space-y-16 animate-showroom-fade-up pb-32">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-end gap-8">
        <div>
            <h1 class="text-6xl font-bold tracking-tighter text-black mb-4">Comparison Gallery</h1>
            <p class="text-lg font-medium text-zinc-400 max-w-2xl">A detailed, side-by-side analysis of performance, heritage, and engineering across your selected collection.</p>
        </div>
        <button onclick="window.print()" class="px-8 py-4 border border-gallery-outline/30 rounded-full font-bold text-[11px] uppercase tracking-widest text-black hover:bg-gallery-surface-low transition-all duration-500 no-print">
            Export Specification
        </button>
    </header>

    <!-- Comparison Table -->
    <div class="motion-table bg-white rounded-[48px] border border-gallery-outline/20 ambient-shadow overflow-hidden">
        <!-- Sticky Headers -->   
        <div class="grid grid-cols-1 md:grid-cols-4 border-b border-gallery-outline/10 sticky top-0 bg-white/90 backdrop-blur-md z-40">
            <div class="p-10 flex items-end">
                <span class="text-[11px] font-bold text-zinc-300 uppercase tracking-[0.4em]">Configuration</span>
            </div>
            
            @foreach($units as $unit)
                <div class="p-10 border-l border-gallery-outline/10 flex flex-col items-center text-center group relative">
                    <a href="{{ route('units.show', $unit) }}" wire:navigate class="absolute inset-0 z-10"></a>
                    <button 
                        wire:click="removeFromComparison({{ $unit->id }})" 
                        class="absolute top-4 right-4 h-8 w-8 rounded-full bg-zinc-50 text-zinc-300 hover:bg-black hover:text-white transition-all flex items-center justify-center no-print z-20"
                    >
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M18 6L6 18M6 6l12 12" stroke-linecap="round"/></svg>
                    </button>

                    <div class="w-full aspect-[4/3] rounded-[32px] overflow-hidden mb-8 bg-gallery-surface-low shadow-sm">
                        @if($unit->mainImage)
                            <img src="{{ Storage::url($unit->mainImage->url) }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                        @endif
                    </div>
                    <h3 class="text-2xl font-bold text-black tracking-tight mb-1 group-hover:text-zinc-500 transition-colors">{{ $unit->name }}</h3>  
                    <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest">{{ $unit->year }} • {{ $unit->category?->name }}</p>
                </div>
            @endforeach

            {{-- Empty Slots --}}
            @for($i = count($units); $i < 3; $i++)
                <div class="p-10 border-l border-gallery-outline/10 flex flex-col items-center justify-center text-center opacity-20">
                    <div class="w-full aspect-[4/3] rounded-[32px] border-2 border-dashed border-zinc-300 mb-8 flex items-center justify-center">
                        <svg viewBox="0 0 24 24" fill="none" class="h-10 w-10 text-zinc-400" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Select Asset</span>
                </div>
            @endfor
        </div>

        <!-- Data Rows -->
        <div class="flex flex-col">
            {{-- Category: Financials --}}
            <div class="motion-row grid grid-cols-1 md:grid-cols-4 bg-gallery-surface-low/50 border-b border-gallery-outline/10">
                <div class="md:col-span-4 p-6 pl-10">
                    <span class="text-[11px] font-bold text-black uppercase tracking-[0.3em]">Acquisition Parameters</span> 
                </div>
            </div>

            <div class="motion-row grid grid-cols-1 md:grid-cols-4 border-b border-gallery-outline/5 group">
                <div class="p-8 pl-10 flex items-center text-[13px] font-bold text-zinc-400 uppercase tracking-widest group-hover:bg-gallery-surface-low transition-colors">Exhibit Price</div>
                @foreach($units as $unit)
                    <div class="p-8 border-l border-gallery-outline/5 flex items-center justify-center text-xl font-bold text-black group-hover:bg-gallery-surface-low transition-colors">
                        {{ $unit->formattedPrice() }}
                    </div>
                @endforeach
                @for($i = count($units); $i < 3; $i++) <div class="p-8 border-l border-gallery-outline/5 bg-gallery-surface-low/10"></div> @endfor
            </div>

            {{-- Category: Technical Specs --}}
            <div class="motion-row grid grid-cols-1 md:grid-cols-4 bg-gallery-surface-low/50 border-b border-gallery-outline/10">
                <div class="md:col-span-4 p-6 pl-10">
                    <span class="text-[11px] font-bold text-black uppercase tracking-[0.3em]">Engineering Profile</span> 
                </div>
            </div>

            <div class="motion-row grid grid-cols-1 md:grid-cols-4 border-b border-gallery-outline/5 group">
                <div class="p-8 pl-10 flex items-center text-[13px] font-bold text-zinc-400 uppercase tracking-widest group-hover:bg-gallery-surface-low transition-colors">Transmission</div>
                @foreach($units as $unit)
                    <div class="p-8 border-l border-gallery-outline/5 flex items-center justify-center text-sm font-medium text-black group-hover:bg-gallery-surface-low transition-colors">
                        {{ $unit->transmission ?? 'Automatic' }}
                    </div>
                @endforeach
                @for($i = count($units); $i < 3; $i++) <div class="p-8 border-l border-gallery-outline/5 bg-gallery-surface-low/10"></div> @endfor
            </div>

            <div class="motion-row grid grid-cols-1 md:grid-cols-4 border-b border-gallery-outline/5 group">
                <div class="p-8 pl-10 flex items-center text-[13px] font-bold text-zinc-400 uppercase tracking-widest group-hover:bg-gallery-surface-low transition-colors">Mileage</div>
                @foreach($units as $unit)
                    <div class="p-8 border-l border-gallery-outline/5 flex items-center justify-center text-sm font-medium text-black group-hover:bg-gallery-surface-low transition-colors">
                        {{ number_format($unit->mileage) }} mi
                    </div>
                @endforeach
                @for($i = count($units); $i < 3; $i++) <div class="p-8 border-l border-gallery-outline/5 bg-gallery-surface-low/10"></div> @endfor
            </div>

            <div class="motion-row grid grid-cols-1 md:grid-cols-4 border-b border-gallery-outline/5 group">
                <div class="p-8 pl-10 flex items-center text-[13px] font-bold text-zinc-400 uppercase tracking-widest group-hover:bg-gallery-surface-low transition-colors">Fuel Heritage</div>
                @foreach($units as $unit)
                    <div class="p-8 border-l border-gallery-outline/5 flex items-center justify-center text-sm font-medium text-black group-hover:bg-gallery-surface-low transition-colors">
                        {{ $unit->fuel_type ?? 'Petrol' }}
                    </div>
                @endforeach
                @for($i = count($units); $i < 3; $i++) <div class="p-8 border-l border-gallery-outline/5 bg-gallery-surface-low/10"></div> @endfor
            </div>

            {{-- Actions --}}
            <div class="motion-row grid grid-cols-1 md:grid-cols-4 border-t border-gallery-outline/10 no-print">
                <div class="p-8 pl-10 flex items-center text-[11px] font-bold text-zinc-300 uppercase tracking-widest bg-gallery-surface-low/20">Final Inquiry</div>
                @foreach($units as $unit)
                    <div class="p-8 border-l border-gallery-outline/10 flex items-center justify-center group-hover:bg-gallery-surface-low transition-colors">
                        <a href="{{ route('units.show', $unit) }}" class="px-8 py-3 bg-black text-white rounded-full font-bold text-[10px] uppercase tracking-widest hover:scale-105 transition-all shadow-xl">
                            View Exhibit
                        </a>
                    </div>
                @endforeach
                @for($i = count($units); $i < 3; $i++) <div class="p-8 border-l border-gallery-outline/10 bg-gallery-surface-low/10"></div> @endfor
            </div>
        </div>
    </div>

    <!-- Print Footer -->
    <div class="hidden print:block border-t border-zinc-200 pt-8 text-center">
        <p class="text-xl font-bold text-black tracking-tighter">The Gallery Automotive Group</p>
        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-2">© {{ date('Y') }} • Verified Technical Specification Report</p>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .ambient-shadow { box-shadow: none !important; }
            .rounded-\[48px\] { border-radius: 0 !important; }
        }
    </style>
</div>
