<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Unit;
use App\Models\UnitImage;
use App\Models\UnitStatusLog;
use App\Services\UnitImageStorageService;
use App\Services\UnitInventoryLogService;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class AdminUnitForm extends Component
{
    use WithFileUploads;

    public ?Unit $unit = null;

    public ?int $category_id = null;

    public string $name = '';

    public ?int $price_php = null;

    public ?string $description = null;

    public bool $show_price = true;

    public bool $is_featured = false;

    public ?int $year = null;

    public ?int $mileage = null;

    public ?string $transmission = null;

    public ?string $fuel_type = null;

    /** @var array<int, array{id: int, url: string, sort_order: int, remove: bool}> */
    public array $existingImages = [];

    /** @var array<int, TemporaryUploadedFile> */
    public array $newImages = [];

    /** @var array<int, int> */
    public array $newImageSortOrders = [];

    public ?UnitStatusLog $lastStatusLog = null;

    public string $qrSvg = '';

    public ?string $statusReason = null;

    public function mount(?Unit $unit = null): void
    {
        $this->unit = $unit?->exists ? $unit->load('images') : null;
        $this->category_id = Category::query()->value('id');

        if ($this->unit !== null) {
            Gate::authorize('update', $this->unit);

            $this->category_id = $this->unit->category_id;
            $this->name = $this->unit->name;
            $this->price_php = $this->unit->price_php;
            $this->description = $this->unit->description;
            $this->show_price = (bool) $this->unit->show_price;
            $this->is_featured = (bool) $this->unit->is_featured;
            $this->year = $this->unit->year;
            $this->mileage = $this->unit->mileage;
            $this->transmission = $this->unit->transmission;
            $this->fuel_type = $this->unit->fuel_type;
            $this->existingImages = $this->unit->images
                ->map(fn (UnitImage $image): array => [
                    'id' => $image->id,
                    'url' => $image->url,
                    'sort_order' => $image->sort_order,
                    'remove' => false,
                ])
                ->values()
                ->all();

            $this->loadStatusAndQrMeta();
        } else {
            Gate::authorize('create', Unit::class);
        }
    }

    public function markAsSold(\App\Services\UnitStatusService $statusService): void
    {
        if ($this->unit === null) {
            return;
        }

        Gate::authorize('changeStatus', $this->unit);

        $result = $statusService->setSold(
            unit: $this->unit,
            userId: (int) auth()->id(),
            reason: $this->statusReason,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
        );

        $this->statusReason = null;
        $this->loadStatusAndQrMeta();

        session()->flash($result['changed'] ? 'status' : 'info', $result['message']);
    }

    public function markAsAvailable(\App\Services\UnitStatusService $statusService): void
    {
        if ($this->unit === null) {
            return;
        }

        Gate::authorize('changeStatus', $this->unit);

        $result = $statusService->setAvailable(
            unit: $this->unit,
            userId: (int) auth()->id(),
            reason: $this->statusReason,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
        );

        $this->statusReason = null;
        $this->loadStatusAndQrMeta();

        session()->flash($result['changed'] ? 'status' : 'info', $result['message']);
    }

    public function updatedNewImages(): void
    {
        foreach (array_keys($this->newImages) as $index) {
            if (! array_key_exists($index, $this->newImageSortOrders)) {
                $this->newImageSortOrders[$index] = $this->nextSortOrder();
            }
        }
    }

    public function removeNewImage(int $index): void
    {
        unset($this->newImages[$index], $this->newImageSortOrders[$index]);

        $this->newImages = array_values($this->newImages);
        $this->newImageSortOrders = array_values($this->newImageSortOrders);
    }

    /**
     * @param  array<int, int|string>  $orderedIds
     */
    public function reorderExistingImages(array $orderedIds): void
    {
        $imagesById = collect($this->existingImages)
            ->keyBy(fn (array $row): int => (int) $row['id']);

        $reordered = [];

        foreach (array_values($orderedIds) as $position => $id) {
            $row = $imagesById->get((int) $id);
            if (! is_array($row)) {
                continue;
            }

            $row['sort_order'] = $position;
            $reordered[] = $row;
            $imagesById->forget((int) $id);
        }

        foreach ($imagesById as $remaining) {
            if (! is_array($remaining)) {
                continue;
            }

            $remaining['sort_order'] = count($reordered);
            $reordered[] = $remaining;
        }

        $this->existingImages = $reordered;
    }

    public function save(): void
    {
        $isCreate = $this->unit === null;
        $existingUnit = $this->unit;

        if ($isCreate) {
            Gate::authorize('create', Unit::class);
        } else {
            if (! $existingUnit instanceof Unit) {
                abort(404);
            }

            Gate::authorize('update', $existingUnit);
        }

        $validated = $this->validate();
        $unitData = Arr::only($validated, [
            'category_id',
            'name',
            'price_php',
            'description',
            'show_price',
            'is_featured',
            'year',
            'mileage',
            'transmission',
            'fuel_type',
        ]);

        $unit = $existingUnit ?? new Unit;
        $originalAttributes = $isCreate
            ? []
            : Arr::only($unit->toArray(), [
                'category_id',
                'name',
                'price_php',
                'description',
                'show_price',
                'is_featured',
                'year',
                'mileage',
                'transmission',
                'fuel_type',
            ]);

        $unit->fill($unitData);
        $unit->save();

        /** @var UnitInventoryLogService $inventoryLogService */
        $inventoryLogService = app(UnitInventoryLogService::class);
        $userId = (int) auth()->id();
        $ipAddress = request()->ip();
        $userAgent = request()->userAgent();

        if ($isCreate) {
            $inventoryLogService->record(
                unit: $unit,
                userId: $userId,
                action: UnitStatusLog::ACTION_CREATE,
                changes: [
                    'attributes' => Arr::only($unit->toArray(), [
                        'category_id',
                        'name',
                        'price_php',
                        'description',
                        'show_price',
                    ]),
                ],
                fromStatus: $unit->status,
                toStatus: $unit->status,
                ipAddress: $ipAddress,
                userAgent: $userAgent,
            );
        } else {
            $attributeChanges = $this->buildAttributeChanges($originalAttributes, $unitData);

            if ($attributeChanges !== []) {
                $inventoryLogService->record(
                    unit: $unit,
                    userId: $userId,
                    action: UnitStatusLog::ACTION_UPDATE,
                    changes: [
                        'attributes' => $attributeChanges,
                    ],
                    fromStatus: $unit->status,
                    toStatus: $unit->status,
                    ipAddress: $ipAddress,
                    userAgent: $userAgent,
                );
            }
        }

        $imageChanges = $this->syncExistingImages($unit);
        $addedPaths = $this->storeNewImages($unit);

        if ($imageChanges['removed'] !== []) {
            $inventoryLogService->record(
                unit: $unit,
                userId: $userId,
                action: UnitStatusLog::ACTION_IMAGE_REMOVE,
                changes: ['removed' => $imageChanges['removed']],
                ipAddress: $ipAddress,
                userAgent: $userAgent,
            );
        }

        if ($imageChanges['reordered'] !== []) {
            $inventoryLogService->record(
                unit: $unit,
                userId: $userId,
                action: UnitStatusLog::ACTION_IMAGE_REORDER,
                changes: ['reordered' => $imageChanges['reordered']],
                ipAddress: $ipAddress,
                userAgent: $userAgent,
            );
        }

        if ($addedPaths !== []) {
            $inventoryLogService->record(
                unit: $unit,
                userId: $userId,
                action: UnitStatusLog::ACTION_IMAGE_ADD,
                changes: ['added' => $addedPaths],
                ipAddress: $ipAddress,
                userAgent: $userAgent,
            );
        }

        session()->flash(
            'status',
            $isCreate ? 'Unit created successfully.' : 'Unit updated successfully.',
        );

        $this->redirectRoute('admin.units.index', navigate: true);
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Validation\Rules\Unique>>
     */
    protected function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price_php' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'show_price' => ['boolean'],
            'is_featured' => ['boolean'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 2)],
            'mileage' => ['nullable', 'integer', 'min:0'],
            'transmission' => ['nullable', 'string', 'max:50'],
            'fuel_type' => ['nullable', 'string', 'max:50'],
            'existingImages' => ['array'],
            'existingImages.*.id' => ['required', 'integer'],
            'existingImages.*.sort_order' => ['required', 'integer', 'min:0'],
            'existingImages.*.remove' => ['boolean'],
            'newImages' => ['array'],
            'newImages.*' => ['image', 'max:8192'],
            'newImageSortOrders' => ['array'],
            'newImageSortOrders.*' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array{removed: array<int, array{id: int, path: string}>, reordered: array<int, array{id: int, from: int, to: int}>}
     */
    private function syncExistingImages(Unit $unit): array
    {
        Gate::authorize('manageImages', $unit);
        /** @var UnitImageStorageService $unitImageStorageService */
        $unitImageStorageService = app(UnitImageStorageService::class);

        $existing = $unit->images()
            ->get()
            ->keyBy('id');
        $removed = [];
        $reordered = [];

        foreach ($this->existingImages as $row) {
            $image = $existing->get((int) $row['id']);

            if ($image === null) {
                continue;
            }

            if (($row['remove'] ?? false) === true) {
                $unitImageStorageService->delete($image->url);
                $removed[] = [
                    'id' => $image->id,
                    'path' => $image->url,
                ];
                $image->delete();

                continue;
            }

            $newSortOrder = (int) $row['sort_order'];
            if ($image->sort_order !== $newSortOrder) {
                $reordered[] = [
                    'id' => $image->id,
                    'from' => $image->sort_order,
                    'to' => $newSortOrder,
                ];
                $image->sort_order = $newSortOrder;
                $image->save();
            }
        }

        return [
            'removed' => $removed,
            'reordered' => $reordered,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function storeNewImages(Unit $unit): array
    {
        Gate::authorize('manageImages', $unit);
        /** @var UnitImageStorageService $unitImageStorageService */
        $unitImageStorageService = app(UnitImageStorageService::class);

        $addedPaths = [];

        foreach ($this->newImages as $index => $uploadedImage) {
            if (! $uploadedImage instanceof TemporaryUploadedFile) {
                continue;
            }

            $path = $unitImageStorageService->storeForUnit($unit, $uploadedImage);

            UnitImage::query()->create([
                'unit_id' => $unit->id,
                'url' => $path,
                'sort_order' => (int) ($this->newImageSortOrders[$index] ?? $this->nextSortOrder()),
            ]);

            $addedPaths[] = $path;
        }

        return $addedPaths;
    }

    private function nextSortOrder(): int
    {
        $existingMax = collect($this->existingImages)
            ->reject(fn (array $image): bool => ($image['remove'] ?? false) === true)
            ->max('sort_order');

        $newMax = empty($this->newImageSortOrders)
            ? null
            : max($this->newImageSortOrders);

        $max = max(
            (int) ($existingMax ?? -1),
            (int) ($newMax ?? -1),
        );

        return $max + 1;
    }

    /**
     * @param  array<string, mixed>  $original
     * @param  array<string, mixed>  $current
     * @return array<string, array{from: mixed, to: mixed}>
     */
    private function buildAttributeChanges(array $original, array $current): array
    {
        $changes = [];

        foreach (['category_id', 'name', 'price_php', 'description', 'show_price', 'is_featured', 'year', 'mileage', 'transmission', 'fuel_type'] as $field) {
            $from = $original[$field] ?? null;
            $to = $current[$field] ?? null;

            if ($from !== $to) {
                $changes[$field] = [
                    'from' => $from,
                    'to' => $to,
                ];
            }
        }

        return $changes;
    }

    private function loadStatusAndQrMeta(): void
    {
        if (! $this->unit instanceof Unit) {
            $this->lastStatusLog = null;
            $this->qrSvg = '';

            return;
        }

        $this->lastStatusLog = $this->unit->statusLogs()
            ->with('user')
            ->first();

        $renderer = new ImageRenderer(
            new RendererStyle(220),
            new SvgImageBackEnd,
        );

        $this->qrSvg = (new Writer($renderer))
            ->writeString($this->unit->signedQrUrl());
    }

    public function render(): View
    {
        return view('livewire.admin-unit-form', [
            'categories' => Category::query()->orderBy('name')->get(),
            'isEdit' => $this->unit !== null,
        ])->layout('layouts.admin-panel', [
            'title' => $this->unit === null ? 'Create Unit' : 'Edit Unit',
        ]);
    }
}
