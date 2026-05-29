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

    public ?int $buyer_id = null;

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
            $this->buyer_id = $this->unit->buyer_id;
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

        if ($result && $this->buyer_id) {
            $this->unit->update(['buyer_id' => $this->buyer_id]);

            $buyer = \App\Models\User::find($this->buyer_id);
            if ($buyer) {
                $buyer->notify(new \App\Notifications\UnitAcquiredNotification([
                    'message' => "Congratulations! You have successfully acquired the {$this->unit->name}.",
                    'unit_id' => $this->unit->id,
                    'unit_name' => $this->unit->name,
                ]));
            }
        }

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

    public function removeNewImage(int $index): void
    {
        unset($this->newImages[$index]);
        unset($this->newImageSortOrders[$index]);
        $this->newImages = array_values($this->newImages);
        $this->newImageSortOrders = array_values($this->newImageSortOrders);
    }

    public function save(UnitImageStorageService $storageService, UnitInventoryLogService $logService): void
    {
        $this->name = trim($this->name);
        $this->description = trim((string) $this->description);

        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price_php' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'show_price' => ['boolean'],
            'is_featured' => ['boolean'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'mileage' => ['nullable', 'integer', 'min:0'],
            'transmission' => ['nullable', 'string', 'max:50'],
            'fuel_type' => ['nullable', 'string', 'max:50'],
            'newImages.*' => ['image', 'max:10240'], // 10MB
        ];

        $validated = $this->validate($rules);

        $isNew = $this->unit === null;

        if ($isNew) {
            $this->unit = new Unit;
        }

        $oldData = $isNew ? [] : $this->unit->toArray();

        $this->unit->fill([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'price_php' => $validated['price_php'],
            'description' => $validated['description'],
            'show_price' => $validated['show_price'],
            'is_featured' => $validated['is_featured'],
            'year' => $validated['year'],
            'mileage' => $validated['mileage'],
            'transmission' => $validated['transmission'],
            'fuel_type' => $validated['fuel_type'],
        ]);

        $this->unit->save();

        // Process removals
        foreach ($this->existingImages as $imageData) {
            if ($imageData['remove']) {
                $image = UnitImage::find($imageData['id']);
                if ($image) {
                    $storageService->delete($image->url);
                    $image->delete();
                }
            } else {
                // Update sort order for existing
                UnitImage::where('id', $imageData['id'])
                    ->update(['sort_order' => $imageData['sort_order']]);
            }
        }

        // Process new uploads
        foreach ($this->newImages as $index => $file) {
            $path = $storageService->store($file, "units/{$this->unit->id}");
            UnitImage::create([
                'unit_id' => $this->unit->id,
                'url' => $path,
                'sort_order' => $this->newImageSortOrders[$index] ?? 0,
            ]);
        }

        // Log changes
        if ($isNew) {
            $logService->logCreation($this->unit, auth()->id());
        } else {
            $changes = $this->unit->getChanges();
            if (! empty($changes)) {
                $logService->logUpdate($this->unit, auth()->id(), $oldData, $changes);
            }
        }

        session()->flash('status', 'Unit saved successfully.');
        $this->redirectRoute('admin.units.index');
    }

    private function loadStatusAndQrMeta(): void
    {
        if ($this->unit === null) {
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
            'users' => \App\Models\User::query()->orderBy('name')->get(),
            'isEdit' => $this->unit !== null,
        ])->layout('layouts.admin-panel', [
            'title' => $this->unit === null ? 'Create Unit' : 'Edit Unit',
        ]);
    }
}
