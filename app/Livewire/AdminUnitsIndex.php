<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Unit;
use App\Models\UnitStatusLog;
use App\Services\UnitInventoryLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminUnitsIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'category', history: true)]
    public ?int $categoryId = null;

    #[Url(as: 'status', history: true)]
    public string $status = '';

    #[Url(as: 'trashed', history: true)]
    public bool $includeTrashed = false;

    public ?int $unitToDeleteId = null;

    public ?string $unitToDeleteName = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedIncludeTrashed(): void
    {
        if (! $this->canManageTrash()) {
            $this->includeTrashed = false;
        }

        $this->resetPage();
    }

    public function clearCategoryFilter(): void
    {
        $this->categoryId = null;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'categoryId', 'status', 'includeTrashed']);
        $this->resetPage();
    }

    public function confirmDelete(int $unitId): void
    {
        $unit = Unit::query()
            ->withTrashed()
            ->findOrFail($unitId);

        $this->unitToDeleteId = $unit->id;
        $this->unitToDeleteName = $unit->name;
    }

    public function executeDelete(): void
    {
        if ($this->unitToDeleteId) {
            $this->delete($this->unitToDeleteId);
            $this->unitToDeleteId = null;
            $this->unitToDeleteName = null;
        }
    }

    public function delete(int $unitId): void
    {
        $unit = Unit::query()
            ->withTrashed()
            ->findOrFail($unitId);
        Gate::authorize('delete', $unit);

        if ($unit->trashed()) {
            session()->flash('info', 'Unit is already in trash.');

            return;
        }

        /** @var UnitInventoryLogService $inventoryLogService */
        $inventoryLogService = app(UnitInventoryLogService::class);
        $inventoryLogService->record(
            unit: $unit,
            userId: (int) auth()->id(),
            action: UnitStatusLog::ACTION_DELETE,
            changes: ['deleted_at' => now()->toDateTimeString()],
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
        );

        $unit->delete();

        session()->flash('status', 'Unit deleted.');
    }

    public function restore(int $unitId): void
    {
        $unit = Unit::query()
            ->withTrashed()
            ->findOrFail($unitId);
        Gate::authorize('restore', $unit);

        if (! $unit->trashed()) {
            session()->flash('info', 'Unit is already active.');

            return;
        }

        $unit->restore();

        /** @var UnitInventoryLogService $inventoryLogService */
        $inventoryLogService = app(UnitInventoryLogService::class);
        $inventoryLogService->record(
            unit: $unit,
            userId: (int) auth()->id(),
            action: UnitStatusLog::ACTION_RESTORE,
            changes: ['restored_at' => now()->toDateTimeString()],
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
        );

        session()->flash('status', 'Unit restored.');
    }

    public function render(): View
    {
        Gate::authorize('viewAny', Unit::class);

        if (! $this->canManageTrash()) {
            $this->includeTrashed = false;
        }

        $statusFilter = in_array($this->status, Unit::statuses(), true)
            ? $this->status
            : '';

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $units = Unit::query()
            ->with(['category', 'mainImage'])
            ->when(
                $this->canManageTrash() && $this->includeTrashed,
                fn ($query) => $query->withTrashed(),
            )
            ->when(
                $this->search !== '',
                fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'),
            )
            ->when(
                $this->categoryId !== null,
                fn ($query) => $query->where('category_id', $this->categoryId),
            )
            ->when(
                $statusFilter !== '',
                fn ($query) => $query->where('status', $statusFilter),
            )
            ->latest('updated_at')
            ->paginate(15);

        return view('livewire.admin-units-index', [
            'categories' => $categories,
            'units' => $units,
            'canManageTrash' => $this->canManageTrash(),
        ])->layout('layouts.admin-panel', [
            'title' => 'Manage Units',
        ]);
    }

    private function canManageTrash(): bool
    {
        return (bool) (auth()->user()?->is_admin ?? false);
    }
}
