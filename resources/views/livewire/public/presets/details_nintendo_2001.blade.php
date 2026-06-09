@use('App\Models\Unit')
@use('Illuminate\Support\Facades\Storage')

<div class="nintendo-preset min-h-screen bg-[#7a8aba] text-[#21242e] font-sans pb-24 animate-showroom-fade-up" 
     x-data="{ currentImage: @entangle('currentImageIndex').live }">
    
    <style>
        .nintendo-preset {
            --n-red: #e60012;
            --n-orange: #f68d1f;
            --n-amber: #ecab37;
            --n-canvas: #7a8aba;
            --n-carbon: #21242e;
            --n-indigo: #3d4f97;
            --n-platinum: #dedede;
            --n-surface: #ffffff;
        }

        .beveled-plate {
            background: var(--n-canvas);
            border-top: 2px solid #ffffff;
            border-left: 2px solid #ffffff;
            border-right: 2px solid var(--n-indigo);
            border-bottom: 2px solid var(--n-indigo);
            box-shadow: inset 1px 1px 0 rgba(255,255,255,0.5), 2px 2px 5px rgba(0,0,0,0.2);
        }

        .beveled-plate-inset {
            background: var(--n-platinum);
            border-top: 2px solid var(--n-indigo);
            border-left: 2px solid var(--n-indigo);
            border-right: 2px solid #ffffff;
            border-bottom: 2px solid #ffffff;
        }

        .nintendo-display {
            font-family: 'Arial Black', Gadget, sans-serif;
            text-transform: uppercase;
            color: white;
            text-shadow: 
                -2px -2px 0 #000,  
                 2px -2px 0 #000,
                -2px  2px 0 #000,
                 2px  2px 0 #000,
                 3px 3px 0 rgba(0,0,0,0.5);
        }

        .nintendo-label {
            font-family: Arial, sans-serif;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 11px;
        }

        .carbon-slab {
            background-color: var(--n-carbon);
            background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 0);
            background-size: 4px 4px;
        }

        .chamfered {
            clip-path: polygon(
                10px 0%, calc(100% - 10px) 0%, 
                100% 10px, 100% calc(100% - 10px), 
                calc(100% - 10px) 100%, 10px 100%, 
                0% calc(100% - 10px), 0% 10px
            );
        }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <div class="max-w-6xl mx-auto px-4 pt-8">
        
        <!-- Breadcrumb / Header Strip -->
        <div class="bg-[#9fbee7] beveled-plate px-6 py-2 mb-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <a href="{{ route('home') }}" wire:navigate class="nintendo-label text-[#3d4f97] hover:underline flex items-center gap-2">
                &larr; BACK TO SYSTEMS
            </a>
            <div class="nintendo-label text-[#21242e] text-center sm:text-right">
                SYSTEM ID: #{{ substr($unit->public_id, -8) }}
            </div>
        </div>

        <!-- Main Machine Layout -->
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left Column: Display Screen & Gallery -->
            <div class="lg:w-2/3 space-y-8 min-w-0">
                <!-- Main Screen -->
                <section 
                    class="relative w-full aspect-[16/10] bg-black beveled-plate p-1 overflow-hidden group shadow-2xl"
                >
                    <div id="nintendo-carousel" class="w-full h-full relative overflow-x-auto snap-x snap-mandatory flex no-scrollbar scroll-smooth">
                        @foreach($unit->images as $index => $img)
                            <div class="slide min-w-full w-full h-full overflow-hidden snap-center relative" wire:key="slide-{{ $img->id }}">
                                <img 
                                    src="{{ Storage::url($img->url) }}" 
                                    alt="{{ $unit->name }} - {{ $index + 1 }}" 
                                    class="card-parallax w-full h-full object-cover grayscale-[0.1] contrast-110 absolute inset-0 {{ $unit->status === App\Models\Unit::STATUS_SOLD ? 'grayscale opacity-60' : '' }}"
                                >
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Hardware Bezel Overlays -->
                    <div class="absolute inset-0 pointer-events-none border-[8px] sm:border-[12px] border-transparent shadow-[inset_0_0_60px_rgba(0,0,0,0.9)]"></div>
                    
                    <div class="absolute bottom-4 left-4 right-4 sm:bottom-8 sm:left-8 sm:right-8 flex justify-between items-end pointer-events-none z-20">
                        <div class="bg-black/60 backdrop-blur-md px-4 py-3 sm:px-8 sm:py-6 beveled-plate border border-white/20 chamfered">
                            <h1 class="nintendo-display text-2xl sm:text-4xl lg:text-5xl leading-none mb-2">{{ $unit->name }}</h1>
                            <p class="nintendo-label text-[#f68d1f] text-[9px] sm:text-xs tracking-[2px]">{{ $unit->category?->name }} // HIGH FIDELITY SYSTEM</p>
                        </div>
                    </div>

                    <!-- Navigation D-Pad style buttons -->
                    @if($unit->images->count() > 1)
                        <div class="absolute top-1/2 -translate-y-1/2 left-4 z-20">
                            <button onclick="document.getElementById('nintendo-carousel').scrollBy({ left: -document.getElementById('nintendo-carousel').offsetWidth, behavior: 'smooth' })" class="w-10 h-10 bg-black/80 text-white rounded-full flex items-center justify-center border-2 border-white/30 hover:bg-[#e60012] hover:border-white transition-all transform active:scale-90">
                                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="4"><path d="M15 18L9 12L15 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        <div class="absolute top-1/2 -translate-y-1/2 right-4 z-20">
                            <button onclick="document.getElementById('nintendo-carousel').scrollBy({ left: document.getElementById('nintendo-carousel').offsetWidth, behavior: 'smooth' })" class="w-10 h-10 bg-black/80 text-white rounded-full flex items-center justify-center border-2 border-white/30 hover:bg-[#e60012] hover:border-white transition-all transform active:scale-90">
                                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="4"><path d="M9 18L15 12L9 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    @endif
                </section>

                <!-- Thumbnails Strip -->
                @if($unit->images->count() > 1)
                    <div class="bg-[#dedede] beveled-plate p-3 flex gap-4 overflow-x-auto no-scrollbar shadow-inner">
                        @foreach ($unit->images as $index => $image)
                            <button 
                                onclick="document.getElementById('nintendo-carousel').scrollTo({ left: {{ $index }} * document.getElementById('nintendo-carousel').offsetWidth, behavior: 'smooth' })"
                                class="h-16 w-24 flex-shrink-0 beveled-plate overflow-hidden transition-all transform {{ $currentImageIndex === $index ? 'ring-4 ring-[#e60012] scale-105 z-10' : 'opacity-50 hover:opacity-100' }}"
                            >
                                <img src="{{ Storage::url($image->url) }}" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Technical Specs Bento -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-[#ffffff] beveled-plate p-4 sm:p-6 text-center shadow-lg">
                        <p class="nintendo-label text-[#3d4f97] mb-2 text-[9px] sm:text-xs">MODEL YEAR</p>
                        <p class="nintendo-display text-[#21242e] text-xl sm:text-3xl">{{ $unit->year ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-[#ffffff] beveled-plate p-4 sm:p-6 text-center shadow-lg">
                        <p class="nintendo-label text-[#3d4f97] mb-2 text-[9px] sm:text-xs">FUEL SOURCE</p>
                        <p class="nintendo-display text-[#21242e] text-xl sm:text-3xl">{{ $unit->fuel_type ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-[#ffffff] beveled-plate p-4 sm:p-6 text-center shadow-lg">
                        <p class="nintendo-label text-[#3d4f97] mb-2 text-[9px] sm:text-xs">GEARBOX</p>
                        <p class="nintendo-display text-[#21242e] text-lg sm:text-2xl">{{ $unit->transmission ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-[#ffffff] beveled-plate p-4 sm:p-6 text-center shadow-lg">
                        <p class="nintendo-label text-[#3d4f97] mb-2 text-[9px] sm:text-xs">DISTANCE</p>
                        <p class="nintendo-display text-[#21242e] text-lg sm:text-2xl">{{ $unit->mileage ? number_format($unit->mileage) : 'N/A' }}</p>
                    </div>
                </div>

                <!-- Descriptive Content -->
                <div class="bg-[#dedede] beveled-plate p-6 sm:p-12 space-y-8 shadow-xl">
                    <div class="flex items-center gap-4 border-b-4 border-[#3d4f97] pb-4">
                        <div class="w-8 h-8 bg-[#3d4f97] rounded flex items-center justify-center text-white font-black">i</div>
                        <h3 class="nintendo-label text-xl sm:text-2xl tracking-tighter">Technical Briefing</h3>
                    </div>
                    <p class="font-sans text-[#21242e] text-sm sm:text-base leading-[2] sm:leading-loose font-medium">
                        {{ $unit->description ?: 'This unit has been meticulously calibrated for peak performance. Featuring state-of-the-art engineering and high-fidelity aesthetics.' }}
                    </p>
                    <div class="flex gap-2">
                        <div class="h-2 w-12 bg-[#e60012] beveled-plate"></div>
                        <div class="h-2 w-12 bg-[#f68d1f] beveled-plate"></div>
                        <div class="h-2 w-12 bg-[#ecab37] beveled-plate"></div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Command Center -->
            <aside class="lg:w-1/3 space-y-8">
                <div class="carbon-slab beveled-plate p-6 sm:p-10 space-y-10 chamfered shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-white/5 rotate-45 translate-x-12 -translate-y-12"></div>
                    
                    <div class="space-y-2">
                        <p class="nintendo-label text-[#ecab37] tracking-[2px]">MSRP LISTING</p>
                        <h2 class="nintendo-display text-4xl sm:text-5xl text-white">{{ $unit->formattedPrice() }}</h2>
                    </div>

                    <div class="space-y-4">
                        @if(\App\Models\Setting::get('design_show_inquiries', true))
                            <button 
                                @if(auth()->check())
                                    wire:click="$dispatch('open-chat')"
                                @else
                                    onclick="window.location.href='{{ route('login') }}'"
                                @endif
                                class="w-full h-16 bg-[#f68d1f] text-white nintendo-label text-base beveled-plate hover:bg-[#e48600] transition-all transform active:translate-y-1 shadow-lg"
                            >
                                REQUEST SYSTEM INFO
                            </button>
                        @endif

                        @if(\App\Models\Setting::get('design_show_comparison', true))
                            <button wire:click="toggleCompare({{ $unit->id }})" class="w-full h-12 bg-[#21242e] text-white nintendo-label border-2 border-[#5a5f8c] flex items-center justify-center gap-3 hover:bg-[#3d4f97] transition-all transform active:translate-y-1">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-[#9fbee7]" stroke="currentColor" stroke-width="3"><path d="M7 16V4M7 4L3 8M7 4L11 8M17 8V20M17 20L13 16M17 20L21 16" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span>{{ in_array($unit->id, $compareIds) ? 'SELECTED' : 'COMPARE SYSTEM' }}</span>
                            </button>
                        @endif
                    </div>

                    <div class="border-t-2 border-dashed border-[#5a5f8c] pt-8 space-y-6">
                        <div class="flex justify-between items-center">
                            <span class="nintendo-label text-[#9fbee7]">STATUS</span>
                            <span class="bg-[#e60012] text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-md animate-pulse">
                                {{ $unit->status }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="nintendo-label text-[#9fbee7]">REGION</span>
                            <span class="text-white font-black text-xs uppercase tracking-widest border border-white/20 px-2 py-0.5">NTSC-U/C</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="nintendo-label text-[#9fbee7]">RATING</span>
                            <div class="w-8 h-8 bg-white flex items-center justify-center font-black text-[#21242e] text-xl beveled-plate">E</div>
                        </div>
                    </div>
                </div>

                <!-- Character Card / Seal of Quality -->
                <div class="bg-white beveled-plate p-6 flex items-start gap-6 shadow-xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-transparent via-transparent to-zinc-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-14 h-14 bg-[#e60012] rounded-full flex items-center justify-center shrink-0 border-4 border-[#21242e] shadow-md transform group-hover:rotate-12 transition-transform">
                        <span class="text-white font-black italic text-2xl">N</span>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[11px] font-black text-[#21242e] uppercase leading-relaxed mb-3">
                            "This system is official Nintendo Seal of Quality approved for your showroom collection!"
                        </p>
                        <div class="h-5 w-32 bg-[#ecab37] beveled-plate relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        </div>
                    </div>
                </div>

                <!-- Small Promo -->
                <div class="bg-[#acace7] beveled-plate p-6 chamfered shadow-lg">
                    <h4 class="nintendo-display text-lg mb-3">ACCESSORIES</h4>
                    <p class="text-[10px] font-bold text-[#3d4f97] mb-4 uppercase">Upgrade your driving experience today.</p>
                    <button class="bg-[#f68d1f] text-white nintendo-label px-6 py-2 text-[10px] beveled-plate hover:bg-[#e48600] transition-colors">GO</button>
                </div>
            </aside>
        </div>
    </div>
</div>
