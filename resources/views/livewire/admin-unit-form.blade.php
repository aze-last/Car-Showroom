@php
    use App\Models\Unit;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

<section class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">{{ $isEdit ? 'Edit Unit' : 'Create Unit' }}</h2>
            <p class="text-sm text-slate-500">Manage unit information, status workflow, and QR details.</p>
        </div>
        <a href="{{ route('admin.units.index') }}" class="admin-btn-secondary">Back to Units</a>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="grid gap-6 xl:grid-cols-[1.35fr_1fr]">
            <article class="admin-card">
                <div class="admin-card-header">
                    <h3 class="text-base font-semibold text-slate-900">Basic Information</h3>
                </div>
                <div class="admin-card-body grid gap-4">
                    <label class="block">
                        <span class="mb-2 block text-sm font-medium text-slate-700">Unit Name</span>
                        <input type="text" wire:model="name" class="admin-input" placeholder="e.g. Toyota Hiace">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-sm font-medium text-slate-700">Category</span>
                        <select wire:model="category_id" class="admin-select">
                            <option value="">Select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </label>

                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Price (PHP)</span>
                            <input type="number" min="0" wire:model="price_php" class="admin-input" placeholder="e.g. 1250000">
                            @error('price_php') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </label>

                        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Price Preview</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                @if ($show_price && $price_php !== null && $price_php >= 0)
                                    {{ '₱'.number_format($price_php) }}
                                @else
                                    Price upon request
                                @endif
                            </p>
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700">
                        <input type="checkbox" wire:model="show_price" class="rounded border-slate-300 text-slate-900 focus:ring-slate-400">
                        Show price publicly
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-sm font-medium text-slate-700">Status</span>
                        <select disabled class="admin-select bg-slate-100 text-slate-700">
                            <option value="{{ Unit::STATUS_AVAILABLE }}" @selected(($unit?->status ?? Unit::STATUS_AVAILABLE) === Unit::STATUS_AVAILABLE)>AVAILABLE</option>
                            <option value="{{ Unit::STATUS_SOLD }}" @selected(($unit?->status ?? Unit::STATUS_AVAILABLE) === Unit::STATUS_SOLD)>SOLD</option>
                        </select>
                        <p class="mt-1 text-xs text-slate-500">Status updates are handled through secured set-state actions.</p>
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-sm font-medium text-slate-700">Description</span>
                        <textarea wire:model="description" rows="5" class="admin-textarea" placeholder="Optional description"></textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </label>
                </div>
            </article>

            <div class="space-y-6">
                <article class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="text-base font-semibold text-slate-900">Image Manager</h3>
                        <p class="text-xs text-slate-500">Drag cards to reorder existing images.</p>
                    </div>
                    <div class="admin-card-body space-y-4">
                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Upload Images</span>
                            <div class="rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 p-4 text-center">
                                <input
                                    id="unit-images-upload"
                                    type="file"
                                    wire:model="newImages"
                                    multiple
                                    accept="image/*"
                                    class="sr-only"
                                >
                                <label for="unit-images-upload" class="inline-flex cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-100">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                                        <path d="M12 16V6M12 6L8 10M12 6L16 10" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5 15V17A2 2 0 0 0 7 19H17A2 2 0 0 0 19 17V15" stroke-linecap="round"/>
                                    </svg>
                                    Choose files
                                </label>
                                <p class="mt-2 text-xs text-slate-500">Drag and drop or click to browse. Max 8MB per file.</p>
                            </div>
                            @error('newImages.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </label>

                        <div wire:loading wire:target="newImages" class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                            Uploading images...
                        </div>

                        @if (! empty($existingImages))
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-slate-700">Existing Images</h4>
                                <div id="existing-image-grid" class="grid gap-3 sm:grid-cols-2">
                                    @foreach ($existingImages as $index => $image)
                                        <article
                                            class="group relative rounded-lg border border-slate-200 bg-white p-2 shadow-sm {{ $image['remove'] ? 'opacity-60' : '' }}"
                                            data-image-card
                                            data-image-id="{{ $image['id'] }}"
                                            draggable="true"
                                        >
                                            <div class="relative overflow-hidden rounded-md border border-slate-200 bg-slate-100">
                                                <img
                                                    src="{{ Storage::url($image['url']) }}"
                                                    alt="Unit image {{ $index + 1 }}"
                                                    class="h-32 w-full object-cover"
                                                    loading="lazy"
                                                >

                                                <button
                                                    type="button"
                                                    wire:click="$set('existingImages.{{ $index }}.remove', {{ $image['remove'] ? 'false' : 'true' }})"
                                                    class="absolute right-2 top-2 inline-flex rounded-full bg-white/90 p-1 text-red-600 shadow hover:bg-red-50"
                                                    title="{{ $image['remove'] ? 'Undo remove' : 'Mark for removal' }}"
                                                >
                                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M4 7H20M9 7V5H15V7M8 7V18A1 1 0 0 0 9 19H15A1 1 0 0 0 16 18V7" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>

                                                @if ($image['remove'])
                                                    <div class="absolute inset-0 flex items-center justify-center bg-slate-900/50 text-xs font-semibold uppercase tracking-wide text-white">
                                                        Marked for removal
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="mt-2 flex items-center justify-between gap-2">
                                                <span class="rounded bg-slate-100 px-2 py-1 text-[11px] font-medium text-slate-600">Drag</span>
                                                <label class="text-xs text-slate-600">
                                                    Order
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        wire:model="existingImages.{{ $index }}.sort_order"
                                                        class="admin-input mt-1 w-20 py-1.5 text-xs"
                                                    >
                                                </label>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (! empty($newImages))
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-slate-700">New Uploads</h4>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    @foreach ($newImages as $index => $image)
                                        <article class="rounded-lg border border-slate-200 bg-white p-2 shadow-sm">
                                            <img src="{{ $image->temporaryUrl() }}" alt="New upload {{ $index + 1 }}" class="h-32 w-full rounded-md object-cover">
                                            <div class="mt-2 flex items-center justify-between gap-2">
                                                <label class="text-xs text-slate-600">
                                                    Order
                                                    <input type="number" min="0" wire:model="newImageSortOrders.{{ $index }}" class="admin-input mt-1 w-20 py-1.5 text-xs">
                                                </label>
                                                <button type="button" wire:click="removeNewImage({{ $index }})" class="admin-btn-secondary px-2.5 py-1.5 text-xs">
                                                    Remove
                                                </button>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </article>

                <article class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="text-base font-semibold text-slate-900">Status</h3>
                    </div>
                    <div class="admin-card-body space-y-3">
                        <div>
                            <span class="{{ ($unit?->status ?? Unit::STATUS_AVAILABLE) === Unit::STATUS_AVAILABLE ? 'admin-badge admin-badge-available' : 'admin-badge admin-badge-sold' }}">
                                {{ $unit?->status ?? Unit::STATUS_AVAILABLE }}
                            </span>
                        </div>

                        @if ($lastStatusLog)
                            <p class="text-sm text-slate-600">
                                Last changed by <span class="font-semibold text-slate-800">{{ $lastStatusLog->user?->name ?? 'Unknown User' }}</span>
                            </p>
                            <p class="text-sm text-slate-600">{{ $lastStatusLog->created_at?->format('Y-m-d H:i:s') }}</p>
                        @else
                            <p class="text-sm text-slate-600">No status change history yet.</p>
                        @endif

                        @if ($isEdit && $unit instanceof Unit)
                            @if ($unit->status === Unit::STATUS_AVAILABLE)
                                <form method="POST" action="{{ route('admin.units.set-sold', $unit) }}" onsubmit="return confirm('Confirm setting this unit to SOLD?')" data-disable-on-submit class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="request_id" value="{{ (string) Str::uuid() }}">
                                    <input type="text" name="reason" maxlength="255" class="admin-input" placeholder="Optional reason">
                                    <button type="submit" class="admin-btn-danger w-full">Mark as SOLD</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.units.set-available', $unit) }}" onsubmit="return confirm('Confirm setting this unit to AVAILABLE?')" data-disable-on-submit class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="request_id" value="{{ (string) Str::uuid() }}">
                                    <input type="text" name="reason" maxlength="255" class="admin-input" placeholder="Optional correction reason">
                                    <button type="submit" class="admin-btn-primary w-full bg-emerald-600 hover:bg-emerald-500">Mark as AVAILABLE</button>
                                </form>
                            @endif
                        @else
                            <p class="text-xs text-slate-500">Save the unit first to enable status actions.</p>
                        @endif
                    </div>
                </article>

                <article class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="text-base font-semibold text-slate-900">QR Code</h3>
                    </div>
                    <div class="admin-card-body space-y-3">
                        @if ($isEdit && $unit instanceof Unit)
                            <div class="mx-auto max-w-[220px] rounded-md border border-slate-200 bg-white p-2">
                                {!! $qrSvg !!}
                            </div>
                            <p class="text-xs text-slate-500">public_id: <span class="font-mono text-slate-700">{{ $unit->public_id }}</span></p>
                            <div class="flex gap-2">
                                <a href="{{ $unit->signedQrUrl() }}" target="_blank" rel="noopener noreferrer" class="admin-btn-secondary flex-1">Print QR</a>
                                <a
                                    href="data:image/svg+xml;charset=utf-8,{{ rawurlencode($qrSvg) }}"
                                    download="unit-{{ $unit->public_id }}-qr.svg"
                                    class="admin-btn-secondary flex-1"
                                >
                                    Download QR
                                </a>
                            </div>
                        @else
                            <p class="text-sm text-slate-600">QR preview is available after creating this unit.</p>
                        @endif
                    </div>
                </article>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <span wire:loading wire:target="save" class="text-sm text-slate-500">Saving changes...</span>
            <button type="submit" wire:loading.attr="disabled" wire:target="save" class="admin-btn-primary">
                {{ $isEdit ? 'Save Changes' : 'Create Unit' }}
            </button>
        </div>
    </form>

    @if (! empty($existingImages))
        @script
            <script>
                const initImageSorting = () => {
                    const grid = document.getElementById('existing-image-grid');
                    if (!grid || grid.dataset.dragBound === '1') {
                        return;
                    }

                    grid.dataset.dragBound = '1';
                    let draggedCard = null;

                    const currentCards = () => Array.from(grid.querySelectorAll('[data-image-card]'));
                    const syncOrder = () => {
                        const orderedIds = currentCards()
                            .map((card) => Number.parseInt(card.dataset.imageId ?? '', 10))
                            .filter((id) => Number.isInteger(id));

                        $wire.reorderExistingImages(orderedIds);
                    };

                    grid.addEventListener('dragstart', (event) => {
                        const target = event.target instanceof HTMLElement ? event.target.closest('[data-image-card]') : null;
                        if (!(target instanceof HTMLElement)) {
                            return;
                        }

                        draggedCard = target;
                        target.classList.add('opacity-60');
                    });

                    grid.addEventListener('dragend', () => {
                        if (draggedCard instanceof HTMLElement) {
                            draggedCard.classList.remove('opacity-60');
                        }
                        draggedCard = null;
                    });

                    grid.addEventListener('dragover', (event) => {
                        event.preventDefault();
                        if (!(draggedCard instanceof HTMLElement)) {
                            return;
                        }

                        const target = event.target instanceof HTMLElement ? event.target.closest('[data-image-card]') : null;
                        if (!(target instanceof HTMLElement) || target === draggedCard) {
                            return;
                        }

                        const targetRect = target.getBoundingClientRect();
                        const shouldInsertAfter = event.clientY >= targetRect.top + (targetRect.height / 2);
                        const referenceNode = shouldInsertAfter ? target.nextSibling : target;
                        grid.insertBefore(draggedCard, referenceNode);
                    });

                    grid.addEventListener('drop', () => {
                        syncOrder();
                    });
                };

                initImageSorting();
                document.addEventListener('livewire:navigated', initImageSorting);
            </script>
        @endscript
    @endif
</section>
