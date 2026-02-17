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
            </div>
        </div>
    </div>
</section>
