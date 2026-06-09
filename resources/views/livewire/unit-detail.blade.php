<div>
    @php
        use App\Models\Unit;
        use Illuminate\Support\Facades\Storage;
    @endphp

    <script type="application/ld+json">
    {!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Car',
    'name' => $unit->name,
    'image' => $unit->mainImage ? Storage::url($unit->mainImage->url) : '',
    'description' => $unit->description ?: 'Premium curated listing.',
    'brand' => [
        '@type' => 'Brand',
        'name' => $unit->category?->name ?? 'Vehicle'
    ],
    'offers' => [
        '@type' => 'Offer',
        'priceCurrency' => 'PHP',
        'price' => $unit->price_php,
        'itemCondition' => 'https://schema.org/UsedCondition',
        'availability' => $unit->status === Unit::STATUS_AVAILABLE ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'
    ]
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

@php
    $designLayout = \App\Models\Setting::get('design_layout', 'cinema');
@endphp

@if($designLayout === 'bmw_m')
    @include('livewire.public.presets.details_bmw_m')
@elseif($designLayout === 'nintendo_2001')
    @include('livewire.public.presets.details_nintendo_2001')
@else
    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex flex-col lg:flex-row gap-16 relative animate-showroom-fade-up">
    <!-- Main Content Column -->
    <div class="flex-1 flex flex-col gap-12 min-w-0">
        <!-- Hero Carousel Area -->
        <section class="w-full relative rounded-[32px] overflow-hidden border border-gallery-outline/20 ambient-shadow bg-gallery-surface-lowest group">
            <div class="w-full relative overflow-x-auto snap-x snap-mandatory flex no-scrollbar scroll-smooth h-full min-h-[400px] lg:min-h-[500px]">
                @foreach($unit->images as $index => $img)
                    <div class="slide min-w-full w-full h-full overflow-hidden snap-center relative aspect-[16/9]" wire:key="slide-{{ $img->id }}">
                        <img 
                            src="{{ Storage::url($img->url) }}" 
                            alt="{{ $unit->name }} - {{ $index + 1 }}" 
                            class="card-parallax w-full h-full object-cover object-center absolute inset-0 {{ $unit->status === Unit::STATUS_SOLD ? 'grayscale opacity-60' : '' }}"
                        >
                    </div>
                @endforeach
                
                <!-- Overlay Overlay for text/status if needed -->
                <div class="absolute inset-0 pointer-events-none bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>

                <!-- Floating Tags -->
                <div class="absolute top-6 left-6 flex gap-2 pointer-events-none">
                    <span class="px-4 py-2 rounded-full bg-white/80 backdrop-blur font-bold text-[10px] uppercase tracking-widest text-black border border-gallery-outline/20 shadow-sm">
                        {{ $unit->status === Unit::STATUS_AVAILABLE ? 'In Stock' : 'Archived' }}
                    </span>
                    @if($unit->year)
                        <span class="px-4 py-2 rounded-full bg-white/80 backdrop-blur font-bold text-[10px] uppercase tracking-widest text-black border border-gallery-outline/20 shadow-sm">{{ $unit->year }}</span>
                    @endif
                </div>
            </div>
            
            <!-- Thumbnails (Kept for manual navigation/anchors) -->
            @if($unit->images->count() > 1)
                <div class="p-4 flex gap-4 overflow-x-auto bg-gallery-surface-lowest border-t border-gallery-outline/10 hide-scrollbar">
                    @foreach ($unit->images as $index => $image)
                        <button
                            onclick="this.closest('section').querySelector('.w-full').scrollTo({ left: {{ $index }} * this.closest('section').querySelector('.w-full').offsetWidth, behavior: 'smooth' })"
                            class="w-32 h-20 rounded-2xl overflow-hidden flex-shrink-0 border-2 transition-all duration-300 {{ $currentImageIndex === $index ? 'border-black shadow-lg scale-95' : 'border-transparent opacity-50 hover:opacity-100' }}"
                        >
                            <img src="{{ Storage::url($image->url) }}" alt="" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Vehicle Header Info -->
        <div class="flex flex-col gap-4">
            <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-5xl sm:text-7xl font-bold tracking-tighter text-black leading-tight">{{ $unit->name }}</h1>
                    <p class="text-xl font-medium text-zinc-400 mt-2">{{ $unit->category?->name }} • {{ $unit->transmission }}</p>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-4xl font-bold tracking-tight text-black">{{ $unit->formattedPrice() }}</p>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-zinc-400 mt-2">Curated Premium Listing</p>
                </div>
            </div>
        </div>

        <!-- Key Highlights Bento Grid -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gallery-surface-lowest rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift flex flex-col justify-between aspect-square">
                <div class="w-12 h-12 rounded-full bg-gallery-surface-low flex items-center justify-center mb-6 text-black">
                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div>
                    <h3 class="text-[10px] font-bold text-zinc-400 mb-2 uppercase tracking-[0.3em]">Era</h3>
                    <p class="text-4xl font-bold text-black mb-1">{{ $unit->year ?? 'N/A' }}</p>
                    <p class="text-[13px] font-medium text-zinc-500">Architectural Integrity</p>
                </div>
            </div>
            
            <div class="bg-gallery-surface-lowest rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift flex flex-col justify-between aspect-square">
                <div class="w-12 h-12 rounded-full bg-gallery-surface-low flex items-center justify-center mb-6 text-black">
                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div>
                    <h3 class="text-[10px] font-bold text-zinc-400 mb-2 uppercase tracking-[0.3em]">Distance</h3>
                    <p class="text-4xl font-bold text-black mb-1">{{ $unit->mileage ? number_format($unit->mileage) : 'N/A' }}</p>
                    <p class="text-[13px] font-medium text-zinc-500">Documented KM</p>
                </div>
            </div>

            <div class="bg-gallery-surface-lowest rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift flex flex-col justify-between aspect-square">
                <div class="w-12 h-12 rounded-full bg-gallery-surface-low flex items-center justify-center mb-6 text-black">
                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-7.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div>
                    <h3 class="text-[10px] font-bold text-zinc-400 mb-2 uppercase tracking-[0.3em]">Transmission</h3>
                    <p class="text-4xl font-bold text-black mb-1 tracking-tight">{{ $unit->transmission ?? 'N/A' }}</p>
                    <p class="text-[13px] font-medium text-zinc-500">Precision Engineering</p>
                </div>
            </div>
        </section>

        <!-- Technical Specifications Grid -->
        <section class="bg-gallery-surface-lowest rounded-[32px] border border-gallery-outline/20 ambient-shadow p-10">
            <div class="flex items-center justify-between mb-8 border-b border-gallery-outline/10 pb-6">
                <h2 class="text-xl font-bold text-black">Technical Specifications</h2>
                <div class="h-px flex-1 mx-8 bg-gallery-outline/10 hidden md:block"></div>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Verified Data</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                <div class="flex justify-between items-center py-3 border-b border-gallery-outline/10">
                    <span class="text-sm font-medium text-zinc-500">Fuel Type</span>
                    <span class="text-[14px] font-bold text-black">{{ $unit->fuel_type ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gallery-outline/10">
                    <span class="text-sm font-medium text-zinc-500">Transmission</span>
                    <span class="text-[14px] font-bold text-black">{{ $unit->transmission ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gallery-outline/10">
                    <span class="text-sm font-medium text-zinc-500">Year</span>
                    <span class="text-[14px] font-bold text-black">{{ $unit->year ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gallery-outline/10">
                    <span class="text-sm font-medium text-zinc-500">Class</span>
                    <span class="text-[14px] font-bold text-black">{{ $unit->category?->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gallery-outline/10">
                    <span class="text-sm font-medium text-zinc-500">Status</span>
                    <span class="text-[11px] font-bold uppercase tracking-widest {{ $unit->status === Unit::STATUS_AVAILABLE ? 'text-emerald-500' : 'text-zinc-400' }}">
                        {{ $unit->status }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gallery-outline/10">
                    <span class="text-sm font-medium text-zinc-500">Ref ID</span>
                    <span class="text-[12px] font-bold text-black font-mono">#{{ substr($unit->public_id, -8) }}</span>
                </div>
            </div>

            <div class="mt-12">
                <h3 class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mb-4">Curator's Note</h3>
                <p class="text-base leading-relaxed text-zinc-600 font-medium">
                    {{ $unit->description ?: 'This premium selection represents the pinnacle of its class, offering uncompromising quality and exceptional performance. Meticulously inspected to meet our highest standards of excellence.' }}
                </p>
            </div>
        </section>

        <!-- Similar Units (Acquaintances) -->
        @if($similarUnits->isNotEmpty())
            <div class="mt-12 space-y-8">
                <h2 class="text-[12px] font-bold uppercase tracking-[0.4em] text-black">More Like This</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach ($similarUnits as $sUnit)
                        <a href="{{ route('units.show', $sUnit) }}" wire:navigate class="group flex flex-col gap-6" wire:key="similar-{{ $sUnit->id }}">
                            <div class="relative aspect-video rounded-3xl overflow-hidden bg-gallery-surface-low border border-gallery-outline/10 ambient-shadow transition-transform duration-500 group-hover:-translate-y-2">
                                @if ($sUnit->mainImage)
                                    <img src="{{ Storage::url($sUnit->mainImage->url) }}" alt="{{ $sUnit->name }}" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-105">
                                @endif
                            </div>
                            <div class="flex flex-col gap-1">
                                <h3 class="text-lg font-bold text-black group-hover:text-zinc-500 transition-colors">{{ $sUnit->name }}</h3>
                                <span class="text-base font-bold text-black opacity-60">{{ $sUnit->formattedPrice() }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Sticky Action Sidebar -->
    <aside class="w-full lg:w-[400px] flex-shrink-0 relative">
        <div class="sticky top-28 flex flex-col gap-8 z-10">
            <!-- Pricing & Action Card -->
            <div class="bg-gallery-surface-lowest rounded-[32px] border border-gallery-outline/20 ambient-shadow p-8 flex flex-col">
                <div class="mb-10">
                    <h2 class="text-4xl font-bold tracking-tight text-black mb-2">{{ $unit->formattedPrice() }}</h2>
                    <p class="text-sm font-medium text-zinc-400 leading-snug">Exclusive of taxes and registration fees. Contact a curator for a full quote.</p>
                </div>
                
                <div class="flex flex-col gap-4">
                    @if(App\Models\Setting::get('design_show_inquiries', true))
                        <button 
                            @if(auth()->check())
                                wire:click="$dispatch('open-chat')"
                            @else
                                onclick="window.location.href='{{ route('login') }}'"
                            @endif
                            class="w-full bg-black text-white font-bold uppercase tracking-widest text-[11px] py-4 rounded-xl hover:opacity-90 transition-all duration-300 shadow-xl hover:shadow-2xl"
                        >
                            Request Information
                        </button>
                    @endif

                    @if(App\Models\Setting::get('design_show_comparison', true))
                        <button wire:click="toggleCompare({{ $unit->id }})" class="w-full bg-transparent border-2 border-gallery-outline/20 text-black font-bold uppercase tracking-widest text-[11px] py-4 rounded-xl hover:border-black transition-all duration-300 flex items-center justify-center gap-2">
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M7 16V4M7 4L3 8M7 4L11 8M17 8V20M17 20L13 16M17 20L21 16" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>{{ in_array($unit->id, $compareIds) ? 'Selected' : 'Compare' }}</span>
                        </button>
                    @endif
                </div>

                <div class="mt-10 pt-8 border-t border-gallery-outline/10 flex flex-col gap-6">
                    <div class="flex items-center gap-4 text-zinc-500">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-7.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span class="text-[13px] font-medium">Gallery Certified Unit</span>
                    </div>
                    <div class="flex items-center gap-4 text-zinc-500">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span class="text-[13px] font-medium">Nationwide Delivery</span>
                    </div>
                </div>
            </div>

            <!-- Livewire Chat Component -->
            @auth
                @if(App\Models\Setting::get('design_show_inquiries', true))
                    <livewire:public.chat-inquiry :unit="$unit" />
                @endif
            @endauth
        </div>
    </aside>
    </main>
@endif
</div>
