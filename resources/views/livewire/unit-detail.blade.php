@php
    use App\Models\Unit;
    use Illuminate\Support\Facades\Storage;
@endphp

<section class="space-y-8">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-5 py-2 text-xs font-bold uppercase tracking-widest text-zinc-600 transition hover:bg-zinc-50 hover:text-zinc-900 shadow-sm">
        <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" stroke="currentColor" stroke-width="2.5">
            <path d="M19 12H5M12 19L5 12L12 5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Back to showroom
    </a>

    <div class="relative rounded-[40px] border border-zinc-100 bg-white p-6 shadow-xl shadow-zinc-200/40 showroom-fade-in sm:p-10">
        <div class="relative grid gap-10 lg:grid-cols-[1.5fr_1fr]">
            <div class="space-y-6">
                <div class="relative overflow-hidden rounded-[32px] bg-zinc-50 border border-zinc-100 shadow-inner">
                    <div class="aspect-[4/3] bg-zinc-100" x-data="{ loaded: false }">
                        <!-- Skeleton Loader -->
                        <div x-show="!loaded" class="absolute inset-0 animate-pulse bg-zinc-200/50"></div>

                        @if ($activeImage)
                            <img
                                src="{{ Storage::url($activeImage->url) }}"
                                alt="{{ $unit->name }}"
                                @load="loaded = true"
                                :class="loaded ? 'opacity-100' : 'opacity-0'"
                                style="view-transition-name: unit-image-{{ $unit->id }}"
                                class="h-full w-full object-cover transition-all duration-700 motion-reduce:transition-none {{ $unit->status === Unit::STATUS_SOLD ? 'grayscale opacity-60' : '' }}"
                            >
                        @else
                            <div x-init="loaded = true" class="flex h-full w-full items-center justify-center text-[10px] font-bold uppercase tracking-widest text-zinc-300">
                                No images available
                            </div>
                        @endif
                    </div>

                    @if ($unit->images->count() > 1)
                        <div class="absolute inset-y-0 left-0 flex items-center px-4">
                            <button
                                type="button"
                                wire:click="previousImage"
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 backdrop-blur-md text-zinc-900 shadow-lg transition hover:scale-110 active:scale-95 border border-zinc-100"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5">
                                    <path d="M15 18L9 12L15 6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4">
                            <button
                                type="button"
                                wire:click="nextImage"
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 backdrop-blur-md text-zinc-900 shadow-lg transition hover:scale-110 active:scale-95 border border-zinc-100"
                            >
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5">
                                    <path d="M9 18L15 12L9 6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>

                        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 rounded-full bg-black/80 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-white backdrop-blur-sm">
                            {{ $currentImageIndex + 1 }} / {{ $unit->images->count() }}
                        </div>
                    @endif
                </div>

                @if ($unit->images->count() > 1)
                    <div class="grid grid-cols-4 gap-3 sm:grid-cols-6 lg:grid-cols-8">
                        @foreach ($unit->images as $index => $image)
                            <button
                                type="button"
                                wire:click="$set('currentImageIndex', {{ $index }})"
                                class="group relative aspect-square overflow-hidden rounded-2xl border-2 transition-all duration-300 {{ $currentImageIndex === $index ? 'border-zinc-900 shadow-md ring-2 ring-zinc-900/10' : 'border-transparent opacity-60 hover:opacity-100 hover:border-zinc-200' }}"
                            >
                                <img
                                    src="{{ Storage::url($image->url) }}"
                                    alt="Thumbnail {{ $index + 1 }}"
                                    class="h-full w-full object-cover"
                                >
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex flex-col space-y-8 py-2">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-lg bg-zinc-100 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-zinc-600">
                            {{ $unit->category?->name ?? 'Vehicle' }}
                        </span>
                        <span class="rounded-lg px-3 py-1 text-[10px] font-black uppercase tracking-widest {{ $unit->status === Unit::STATUS_AVAILABLE ? 'bg-emerald-500 text-white' : 'bg-zinc-400 text-white' }}">
                            {{ $unit->status }}
                        </span>
                    </div>

                    <h1 class="text-4xl font-black tracking-tight text-zinc-900 sm:text-5xl">
                        {{ $unit->name }}
                    </h1>

                    <div class="flex items-center gap-4">
                        <span class="text-3xl font-black tracking-tight text-zinc-900">
                            {{ $unit->formattedPrice() }}
                        </span>
                        @if($unit->status === Unit::STATUS_AVAILABLE)
                            <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Ready for release</span>
                        @endif
                    </div>
                </div>

                <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" class="grid grid-cols-2 gap-4 border-y border-zinc-100 py-8 transition-all duration-700 ease-out delay-150 motion-reduce:transition-none motion-reduce:opacity-100 motion-reduce:translate-y-0">
                    @if($unit->year)
                        <div class="space-y-1">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Year Model</span>
                            <p class="text-base font-bold text-zinc-900">{{ $unit->year }}</p>
                        </div>
                    @endif
                    @if($unit->mileage)
                        <div class="space-y-1">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Mileage</span>
                            <p class="text-base font-bold text-zinc-900">{{ number_format($unit->mileage) }} KM</p>
                        </div>
                    @endif
                    @if($unit->transmission)
                        <div class="space-y-1">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Transmission</span>
                            <p class="text-base font-bold text-zinc-900">{{ $unit->transmission }}</p>
                        </div>
                    @endif
                    @if($unit->fuel_type)
                        <div class="space-y-1">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Fuel Type</span>
                            <p class="text-base font-bold text-zinc-900">{{ $unit->fuel_type }}</p>
                        </div>
                    @endif
                </div>

                <div class="space-y-4">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">About this unit</h2>
                    <p class="text-sm leading-relaxed text-zinc-600">
                        {{ $unit->description ?: 'Premium selected vehicle with verified quality standards and professional inspection completed.' }}
                    </p>
                </div>

                <div class="pt-6">
                    @if ($submitted)
                        <div class="rounded-3xl bg-zinc-900 p-8 text-center text-white shadow-2xl">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-500 text-white mb-4">
                                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="3">
                                    <path d="M20 6L9 17L4 12" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold">Inquiry Sent!</h3>
                            <p class="mt-2 text-sm text-zinc-400">Our sales representative will contact you within 24 hours.</p>
                            <button type="button" wire:click="$set('submitted', false)" class="mt-6 text-xs font-bold uppercase tracking-widest text-zinc-300 underline">Send another message</button>
                        </div>
                    @else
                        <div class="rounded-3xl border border-zinc-100 bg-zinc-50/50 p-6 sm:p-8">
                            <h3 class="text-lg font-bold text-zinc-900">Interested?</h3>
                            <p class="mt-1 text-sm text-zinc-500">Send us a message and we'll get back to you.</p>

                            <form wire:submit="submitInquiry" class="mt-8 space-y-4">
                                <div class="space-y-4">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <input type="text" wire:model="name" placeholder="Name" class="h-12 w-full rounded-xl border border-zinc-200 bg-white px-4 text-sm focus:border-zinc-900 focus:ring-0 transition-all">
                                        <input type="email" wire:model="email" placeholder="Email" class="h-12 w-full rounded-xl border border-zinc-200 bg-white px-4 text-sm focus:border-zinc-900 focus:ring-0 transition-all">
                                    </div>
                                    <input type="text" wire:model="phone" placeholder="Phone Number (Optional)" class="h-12 w-full rounded-xl border border-zinc-200 bg-white px-4 text-sm focus:border-zinc-900 focus:ring-0 transition-all">
                                    <textarea wire:model="message" rows="3" placeholder="I'm interested in this unit..." class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm focus:border-zinc-900 focus:ring-0 transition-all"></textarea>
                                </div>

                                <button type="submit" wire:loading.attr="disabled" class="w-full rounded-xl bg-zinc-900 py-4 text-xs font-black uppercase tracking-[0.2em] text-white shadow-xl transition-all hover:bg-zinc-800 active:scale-95 disabled:opacity-50">
                                    <span wire:loading.remove wire:target="submitInquiry">Send Message</span>
                                    <span wire:loading wire:target="submitInquiry">Processing...</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($similarUnits->isNotEmpty())
        <div x-data="{ shown: false }" x-intersect.once.margin.-100px="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="pt-16 space-y-8 transition-all duration-700 ease-out motion-reduce:transition-none motion-reduce:opacity-100 motion-reduce:translate-y-0">
            <h2 class="text-2xl font-black tracking-tight text-zinc-900">Similar Vehicles</h2>
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($similarUnits as $sUnit)
                    <a
                        href="{{ route('units.show', $sUnit) }}"
                        wire:navigate
                        class="group flex flex-col overflow-hidden rounded-3xl border border-zinc-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg"
                        wire:key="similar-unit-{{ $sUnit->id }}"
                    >
                        <div class="relative aspect-video overflow-hidden bg-zinc-50" x-data="{ loaded: false }">
                             <!-- Skeleton Loader -->
                            <div x-show="!loaded" class="absolute inset-0 animate-pulse bg-zinc-200/50"></div>

                            @if ($sUnit->mainImage)
                                <img
                                    src="{{ Storage::url($sUnit->mainImage->url) }}"
                                    alt="{{ $sUnit->name }}"
                                    @load="loaded = true"
                                    :class="loaded ? 'opacity-100' : 'opacity-0'"
                                    style="view-transition-name: unit-image-{{ $sUnit->id }}"
                                    class="h-full w-full object-cover transition-all duration-700 group-hover:scale-110 motion-safe:transition-all motion-reduce:transition-none"
                                >
                            @else
                                <div x-init="loaded = true" class="flex h-full w-full items-center justify-center bg-zinc-50 text-[10px] font-bold uppercase tracking-widest text-zinc-300">
                                    No Image
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="font-bold text-zinc-900 group-hover:text-zinc-600 transition-colors">{{ $sUnit->name }}</h3>
                            <p class="mt-2 text-base font-black text-zinc-900">{{ $sUnit->formattedPrice() }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</section>
