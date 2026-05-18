@php
    use App\Models\Unit;
    use Illuminate\Support\Facades\Storage;
@endphp

<section class="space-y-6">
    <a href="{{ route('home') }}" class="inline-flex items-center rounded-full border border-zinc-800 bg-zinc-900/45 px-3 py-1.5 text-xs font-medium text-zinc-300 transition hover:bg-zinc-900 hover:text-zinc-100">
        &larr; Back to showroom
    </a>

    <div class="relative rounded-[30px] border border-zinc-800 bg-zinc-900/55 p-4 shadow-[0_26px_56px_rgba(0,0,0,0.45)] backdrop-blur-sm showroom-fade-in sm:p-6">
        <div class="pointer-events-none absolute -top-12 left-1/2 h-40 w-72 -translate-x-1/2 rounded-full bg-amber-500/10 blur-2xl"></div>

        <div class="relative grid gap-6 lg:grid-cols-[1.45fr_1fr]">
            <div class="space-y-4 rounded-[22px] border border-zinc-800 bg-zinc-950/65 p-4 shadow-[0_18px_40px_rgba(0,0,0,0.4)] sm:p-5">
                <div class="relative overflow-hidden rounded-[18px] bg-zinc-800">
                    <div class="absolute inset-0 z-10 bg-gradient-to-t from-black/75 via-transparent to-transparent"></div>
                    <div class="aspect-[4/3]">
                        @if ($activeImage)
                            <img
                                src="{{ Storage::url($activeImage->url) }}"
                                alt="{{ $unit->name }}"
                                class="h-full w-full object-cover transition duration-500 {{ $unit->status === Unit::STATUS_SOLD ? 'grayscale' : '' }}"
                            >
                        @else
                            <div class="flex h-full w-full items-center justify-center text-sm text-zinc-400">
                                No images uploaded
                            </div>
                        @endif
                    </div>

                    @if ($unit->images->count() > 1)
                        <button
                            type="button"
                            wire:click="previousImage"
                            aria-label="Previous photo"
                            class="absolute left-3 top-1/2 z-20 -translate-y-1/2 rounded-full border border-white/15 bg-black/55 p-2.5 text-zinc-100 transition hover:bg-black/80 disabled:cursor-not-allowed disabled:opacity-40"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2">
                                <path d="M15 6L9 12L15 18" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button
                            type="button"
                            wire:click="nextImage"
                            aria-label="Next photo"
                            class="absolute right-3 top-1/2 z-20 -translate-y-1/2 rounded-full border border-white/15 bg-black/55 p-2.5 text-zinc-100 transition hover:bg-black/80 disabled:cursor-not-allowed disabled:opacity-40"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2">
                                <path d="M9 6L15 12L9 18" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        <div class="absolute bottom-3 left-1/2 z-20 -translate-x-1/2 rounded-full border border-white/10 bg-black/45 px-3 py-1 text-xs text-zinc-200">
                            {{ $currentImageIndex + 1 }} / {{ $unit->images->count() }}
                        </div>
                    @endif
                </div>

                @if ($unit->images->isNotEmpty())
                    <div class="grid grid-cols-4 gap-2 sm:grid-cols-6">
                        @foreach ($unit->images as $index => $image)
                            <button
                                type="button"
                                wire:click="$set('currentImageIndex', {{ $index }})"
                                class="overflow-hidden rounded-xl border transition duration-200 {{ $currentImageIndex === $index ? 'border-amber-400/70 ring-1 ring-amber-400/50' : 'border-zinc-700 hover:border-zinc-500' }}"
                                aria-label="View image {{ $index + 1 }}"
                            >
                                <img
                                    src="{{ Storage::url($image->url) }}"
                                    alt="{{ $unit->name }} image {{ $index + 1 }}"
                                    class="h-16 w-full object-cover"
                                    loading="lazy"
                                >
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="space-y-5 rounded-[22px] border border-zinc-800 bg-zinc-950/65 p-5 shadow-[0_18px_40px_rgba(0,0,0,0.4)] sm:p-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight text-zinc-100 sm:text-3xl">{{ $unit->name }}</h1>
                        <p class="mt-2 text-sm text-zinc-400">Category: {{ $unit->category?->name ?? 'Uncategorized' }}</p>
                    </div>
                    <span class="rounded-md border px-2.5 py-1 text-xs font-semibold {{ $unit->status === Unit::STATUS_AVAILABLE ? 'border-emerald-300/35 bg-emerald-600/75 text-white' : 'border-red-300/35 bg-red-600/80 text-white' }}">
                        {{ $unit->status === Unit::STATUS_AVAILABLE ? 'Available' : 'Sold' }}
                    </span>
                </div>

                <p class="text-xl font-semibold tracking-tight text-amber-300">{{ $unit->formattedPrice() }}</p>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-900/45 p-4">
                    <h2 class="text-xs font-medium uppercase tracking-[0.12em] text-zinc-300/90">Description</h2>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-300/80">
                        {{ $unit->description ?: 'No description provided.' }}
                    </p>
                </div>

                @if($unit->year || $unit->mileage || $unit->transmission || $unit->fuel_type)
                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900/45 p-4">
                        <h2 class="text-xs font-medium uppercase tracking-[0.12em] text-zinc-300/90 mb-3">Specifications</h2>
                        <div class="grid grid-cols-2 gap-y-3 gap-x-6 text-sm">
                            @if($unit->year)
                                <div class="flex flex-col">
                                    <span class="text-zinc-500 text-[10px] uppercase tracking-wider">Year Model</span>
                                    <span class="text-zinc-200 font-medium">{{ $unit->year }}</span>
                                </div>
                            @endif
                            @if($unit->mileage)
                                <div class="flex flex-col">
                                    <span class="text-zinc-500 text-[10px] uppercase tracking-wider">Mileage</span>
                                    <span class="text-zinc-200 font-medium">{{ number_format($unit->mileage) }} km</span>
                                </div>
                            @endif
                            @if($unit->transmission)
                                <div class="flex flex-col">
                                    <span class="text-zinc-500 text-[10px] uppercase tracking-wider">Transmission</span>
                                    <span class="text-zinc-200 font-medium">{{ $unit->transmission }}</span>
                                </div>
                            @endif
                            @if($unit->fuel_type)
                                <div class="flex flex-col">
                                    <span class="text-zinc-500 text-[10px] uppercase tracking-wider">Fuel Type</span>
                                    <span class="text-zinc-200 font-medium">{{ $unit->fuel_type }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="space-y-2">
                    <div class="flex items-center justify-between rounded-xl border border-zinc-800 bg-zinc-900/35 px-4 py-3 text-sm">
                        <span class="text-zinc-400">Status</span>
                        <span class="font-medium {{ $unit->status === Unit::STATUS_AVAILABLE ? 'text-emerald-300' : 'text-red-300' }}">
                            {{ $unit->status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-zinc-800 bg-zinc-900/35 px-4 py-3 text-sm">
                        <span class="text-zinc-400">Gallery</span>
                        <span class="font-medium text-zinc-100">{{ $unit->images->count() }} photos</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-zinc-800">
                    <h3 class="text-lg font-semibold text-zinc-100 mb-4">Contact us about this vehicle</h3>

                    @if ($submitted)
                        <div class="rounded-2xl border border-emerald-400/30 bg-emerald-500/10 p-5 text-center shadow-lg">
                            <svg viewBox="0 0 24 24" fill="none" class="mx-auto h-8 w-8 text-emerald-400" stroke="currentColor" stroke-width="2">
                                <path d="M9 12L11 14L15 10M21 12A9 9 0 1 1 3 12A9 9 0 0 1 21 12Z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p class="mt-3 text-sm font-medium text-emerald-100">Message sent successfully!</p>
                            <p class="mt-1 text-xs text-zinc-400">Our team will get back to you shortly.</p>
                            <button type="button" wire:click="$set('submitted', false)" class="mt-4 text-xs font-semibold text-amber-400 hover:text-amber-300">Send another message</button>
                        </div>
                    @else
                        <form wire:submit="submitInquiry" class="space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="block">
                                    <span class="mb-1.5 block text-xs font-medium text-zinc-400">Full Name</span>
                                    <input type="text" wire:model="name" class="h-10 w-full rounded-xl border border-zinc-800 bg-zinc-950/40 px-3.5 text-sm text-zinc-100 focus:border-amber-400/60 focus:outline-none" placeholder="Juan Dela Cruz">
                                    @error('name') <p class="mt-1 text-[10px] text-red-400">{{ $message }}</p> @enderror
                                </label>
                                <label class="block">
                                    <span class="mb-1.5 block text-xs font-medium text-zinc-400">Email Address</span>
                                    <input type="email" wire:model="email" class="h-10 w-full rounded-xl border border-zinc-800 bg-zinc-950/40 px-3.5 text-sm text-zinc-100 focus:border-amber-400/60 focus:outline-none" placeholder="juan@example.com">
                                    @error('email') <p class="mt-1 text-[10px] text-red-400">{{ $message }}</p> @enderror
                                </label>
                            </div>

                            <label class="block">
                                <span class="mb-1.5 block text-xs font-medium text-zinc-400">Phone Number (Optional)</span>
                                <input type="text" wire:model="phone" class="h-10 w-full rounded-xl border border-zinc-800 bg-zinc-950/40 px-3.5 text-sm text-zinc-100 focus:border-amber-400/60 focus:outline-none" placeholder="0912 345 6789">
                                @error('phone') <p class="mt-1 text-[10px] text-red-400">{{ $message }}</p> @enderror
                            </label>

                            <label class="block">
                                <span class="mb-1.5 block text-xs font-medium text-zinc-400">Your Message</span>
                                <textarea wire:model="message" rows="4" class="w-full rounded-xl border border-zinc-800 bg-zinc-950/40 px-3.5 py-2.5 text-sm text-zinc-100 focus:border-amber-400/60 focus:outline-none" placeholder="I am interested in this vehicle. Is it still available for viewing?"></textarea>
                                @error('message') <p class="mt-1 text-[10px] text-red-400">{{ $message }}</p> @enderror
                            </label>

                            <button type="submit" wire:loading.attr="disabled" class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-bold text-zinc-950 transition hover:bg-amber-400 disabled:opacity-50">
                                <span wire:loading.remove wire:target="submitInquiry">Send Inquiry</span>
                                <span wire:loading wire:target="submitInquiry">Sending...</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($similarUnits->isNotEmpty())
        <div class="pt-10 space-y-6">
            <h2 class="text-xl font-semibold text-zinc-100">Similar Vehicles You Might Like</h2>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($similarUnits as $sUnit)
                    <a
                        href="{{ route('units.show', $sUnit) }}"
                        class="group overflow-hidden rounded-[22px] border border-zinc-800 bg-zinc-900/45 shadow-lg transition duration-300 hover:-translate-y-1 hover:border-zinc-700"
                        wire:key="similar-unit-{{ $sUnit->id }}"
                    >
                        <div class="relative aspect-video overflow-hidden bg-zinc-800">
                            @if ($sUnit->mainImage)
                                <img
                                    src="{{ Storage::url($sUnit->mainImage->url) }}"
                                    alt="{{ $sUnit->name }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                                >
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-medium text-zinc-100 group-hover:text-amber-200 transition-colors">{{ $sUnit->name }}</h3>
                            <p class="mt-1 text-sm font-semibold text-amber-300">{{ $sUnit->formattedPrice() }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</section>
