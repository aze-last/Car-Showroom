<!-- BMW M-Performance Unit Detail Preset -->
<div class="w-full max-w-[1440px] mx-auto px-0 md:px-16 lg:px-24 pb-24 pt-4 animate-showroom-fade-up">
    
    <!-- HERO IMAGE SECTION (ALPINJS SLIDESHOW) START -->
    <section 
        class="relative w-full aspect-[16/9] min-h-[450px] max-h-[650px] overflow-hidden group mb-12 border border-[#3c3c3c]"
    >
        <!-- Slides -->
        <div class="w-full h-full relative overflow-x-auto snap-x snap-mandatory flex no-scrollbar scroll-smooth">
            @foreach($unit->images as $index => $img)
                <div class="slide min-w-full w-full h-full overflow-hidden snap-center relative" wire:key="slide-{{ $img->id }}">
                    <img 
                        src="{{ Storage::url($img->url) }}" 
                        alt="{{ $unit->name }} - {{ $index + 1 }}" 
                        class="card-parallax w-full h-full object-cover object-center absolute inset-0 {{ $unit->status === App\Models\Unit::STATUS_SOLD ? 'grayscale opacity-60' : '' }}"
                    >
                </div>
            @endforeach
        </div>
        
        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-90 z-10 pointer-events-none"></div>
        
        <!-- Hero Overlay Info -->
        <div class="absolute bottom-24 left-8 right-8 flex flex-col md:flex-row justify-between items-end gap-6 z-20 pointer-events-none">
            <div>
                <h1 class="font-saira display-lg text-white mb-2 leading-none">{{ $unit->name }}</h1>
                <p class="font-saira label-uppercase text-[#0066b1] tracking-[1.5px]">{{ $unit->category?->name }} • {{ $unit->transmission }}</p>
            </div>
            
            @if($unit->images->count() > 1)
                <div class="flex gap-4 pointer-events-auto">
                    <button onclick="this.closest('section').querySelector('.overflow-x-auto').scrollBy({ left: -this.closest('section').offsetWidth, behavior: 'smooth' })" class="bg-black/50 backdrop-blur p-4 hover:bg-white/20 transition-colors border border-[#3c3c3c] text-white flex items-center justify-center group/btn">
                        <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6 transform transition-transform group-hover/btn:-translate-x-1" stroke="currentColor" stroke-width="2"><path d="M15 18L9 12L15 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button onclick="this.closest('section').querySelector('.overflow-x-auto').scrollBy({ left: this.closest('section').offsetWidth, behavior: 'smooth' })" class="bg-black/50 backdrop-blur p-4 hover:bg-white/20 transition-colors border border-[#3c3c3c] text-white flex items-center justify-center group/btn">
                        <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6 transform transition-transform group-hover/btn:translate-x-1" stroke="currentColor" stroke-width="2"><path d="M9 18L15 12L9 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            @endif
        </div>

        <!-- Thumbnails Strip -->
        @if($unit->images->count() > 1)
            <div class="absolute bottom-0 left-0 w-full p-4 flex gap-4 overflow-x-auto bg-gradient-to-t from-black to-transparent z-20 no-scrollbar items-end h-24">
                @foreach ($unit->images as $index => $image)
                    <button
                        onclick="this.closest('section').querySelector('.overflow-x-auto').scrollTo({ left: {{ $index }} * this.closest('section').querySelector('.overflow-x-auto').offsetWidth, behavior: 'smooth' })"
                        class="h-14 w-24 overflow-hidden flex-shrink-0 border transition-all duration-300 border-[#3c3c3c] opacity-40 hover:opacity-100"
                    >
                        <img src="{{ Storage::url($image->url) }}" alt="" class="h-full w-full object-cover grayscale hover:grayscale-0">
                    </button>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Technical Content Area -->
    <div class="flex flex-col xl:flex-row gap-16 px-4 md:px-0">
        <!-- Specs & Details -->
        <div class="flex-grow space-y-16">
            <!-- Spec Grid -->
            <div>
                <h2 class="font-saira label-uppercase text-white mb-8 border-l-4 border-[#e22718] pl-4 text-xl">Technical Performance</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-1">
                    <div class="bg-[#0d0d0d] p-6 border border-[#3c3c3c]">
                        <p class="font-saira label-uppercase text-[#7e7e7e] mb-2 text-xs">MODEL YEAR</p>
                        <p class="font-saira display-sm text-white">{{ $unit->year ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-[#0d0d0d] p-6 border border-[#3c3c3c]">
                        <p class="font-saira label-uppercase text-[#7e7e7e] mb-2 text-xs">FUEL TYPE</p>
                        <p class="font-saira display-sm text-white">{{ $unit->fuel_type ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-[#0d0d0d] p-6 border border-[#3c3c3c]">
                        <p class="font-saira label-uppercase text-[#7e7e7e] mb-2 text-xs">GEARBOX</p>
                        <p class="font-saira display-sm text-white tracking-tight">{{ $unit->transmission ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-[#0d0d0d] p-6 border border-[#3c3c3c]">
                        <p class="font-saira label-uppercase text-[#7e7e7e] mb-2 text-xs">DISTANCE</p>
                        <p class="font-saira display-sm text-white">{{ $unit->mileage ? number_format($unit->mileage) : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Bento Grid Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-[#1a1a1a] p-10 border border-[#3c3c3c]">
                        <h3 class="font-saira title-lg uppercase text-white mb-4">Engineering Philosophy</h3>
                        <p class="font-inter body-md text-[#bbbbbb] leading-relaxed max-w-2xl">
                            {{ $unit->description ?: 'This premium selection represents the pinnacle of its class, offering uncompromising quality and exceptional performance. Meticulously inspected to meet our highest standards of excellence.' }}
                        </p>
                    </div>
                    
                    @if($unit->images->count() > 1)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($unit->images->take(2) as $img)
                            <div class="relative h-64 overflow-hidden border border-[#3c3c3c] group">
                                <img class="w-full h-full object-cover grayscale transition-all group-hover:scale-105 duration-500" 
                                     src="{{ Storage::url($img->url) }}"/>
                                <div class="absolute inset-0 bg-black/40 p-6 flex items-end">
                                    <span class="font-saira label-uppercase text-white text-xs">Gallery Asset</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <!-- Sidebar Specs (Secondary) -->
                <div class="bg-[#0d0d0d] border border-[#3c3c3c] p-8 space-y-8">
                    <div>
                        <h4 class="font-saira label-uppercase text-[#0066b1] mb-2 text-xs">Status</h4>
                        <p class="font-saira title-sm text-white">{{ $unit->status }}</p>
                        <p class="font-inter body-sm text-[#7e7e7e]">Gallery Certified Unit</p>
                    </div>
                    <hr class="border-[#3c3c3c]"/>
                    <div>
                        <h4 class="font-saira label-uppercase text-[#0066b1] mb-2 text-xs">Classification</h4>
                        <p class="font-saira title-sm text-white">{{ $unit->category?->name ?? 'N/A' }}</p>
                        <p class="font-inter body-sm text-[#7e7e7e]">Premium Lineup</p>
                    </div>
                    <hr class="border-[#3c3c3c]"/>
                    <div>
                        <h4 class="font-saira label-uppercase text-[#0066b1] mb-2 text-xs">Reference</h4>
                        <p class="font-saira title-sm text-white font-mono">#{{ substr($unit->public_id, -8) }}</p>
                        <p class="font-inter body-sm text-[#7e7e7e]">Verified ID</p>
                    </div>
                </div>
            </div>
            
            <!-- Similar Units -->
            @if($similarUnits->isNotEmpty())
                <div class="pt-8">
                    <h2 class="font-saira label-uppercase text-white mb-8 border-l-4 border-[#0066b1] pl-4 text-xl">More Like This</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($similarUnits as $sUnit)
                            <a href="{{ route('units.show', $sUnit) }}" wire:navigate class="group bg-[#1a1a1a] border border-[#3c3c3c] hover:border-white transition-colors p-4 block">
                                <div class="relative aspect-video mb-4 overflow-hidden bg-black">
                                    @if ($sUnit->mainImage)
                                        <img src="{{ Storage::url($sUnit->mainImage->url) }}" alt="{{ $sUnit->name }}" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-105 grayscale group-hover:grayscale-0">
                                    @endif
                                </div>
                                <h3 class="font-saira title-md text-white mb-1">{{ $sUnit->name }}</h3>
                                <p class="font-inter body-sm text-[#bbbbbb]">{{ $sUnit->formattedPrice() }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Inquiry Panel (Sidebar Floating) -->
        <aside class="w-full xl:w-96 shrink-0 h-fit sticky top-28">
            <div class="bg-[#1a1a1a] border border-[#3c3c3c] p-10 space-y-8">
                <div class="space-y-2">
                    <p class="font-saira label-uppercase text-[#7e7e7e] text-xs">Market Listing</p>
                    <h2 class="font-saira display-sm text-white">{{ $unit->formattedPrice() }}</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between border-b border-[#262626] py-3">
                        <span class="font-inter body-sm text-[#7e7e7e]">Taxes</span>
                        <span class="font-saira label-uppercase text-white text-xs">Exclusive</span>
                    </div>
                    <div class="flex justify-between border-b border-[#262626] py-3">
                        <span class="font-inter body-sm text-[#7e7e7e]">Delivery</span>
                        <span class="font-saira label-uppercase text-white text-xs">Nationwide</span>
                    </div>
                </div>
                
                <div class="space-y-4 pt-4">
                    @if(\App\Models\Setting::get('design_show_inquiries', true))
                        <button 
                            @if(auth()->check())
                                wire:click="$dispatch('open-chat')"
                            @else
                                onclick="window.location.href='{{ route('login') }}'"
                            @endif
                            class="w-full h-12 bg-white text-black font-saira label-uppercase border border-white hover:bg-black hover:text-white transition-colors"
                        >
                            Request Information
                        </button>
                    @endif

                    @if(\App\Models\Setting::get('design_show_comparison', true))
                        <button wire:click="toggleCompare({{ $unit->id }})" class="w-full h-12 bg-transparent text-white font-saira label-uppercase border border-[#3c3c3c] hover:border-white transition-colors flex items-center justify-center gap-2">
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M7 16V4M7 4L3 8M7 4L11 8M17 8V20M17 20L13 16M17 20L21 16" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>{{ in_array($unit->id, $compareIds) ? 'Selected' : 'Compare' }}</span>
                        </button>
                    @endif
                </div>
                
                <!-- Livewire Chat Component -->
                @auth
                    @if(\App\Models\Setting::get('design_show_inquiries', true))
                        <div class="pt-4 border-t border-[#3c3c3c]">
                            <livewire:public.chat-inquiry :unit="$unit" />
                        </div>
                    @endif
                @endauth
            </div>
        </aside>
    </div>
</div>