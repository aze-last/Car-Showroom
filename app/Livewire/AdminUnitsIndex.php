<?php

namespace App\Livewire;

use App\Concerns\HandlesLivewireErrors;
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
    use HandlesLivewireErrors;
    use WithPagination;

    #[Url(as: 'view', history: true)]
    public string $viewMode = 'active';

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

    public ?int $selectedHandoverUnitId = null;

    public function updatedViewMode(): void
    {
        $this->resetPage();
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['active', 'sold', 'trashed'], true)) {
            $this->viewMode = $mode;
            $this->resetPage();
        }
    }

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
        $this->reset(['search', 'categoryId', 'status', 'includeTrashed', 'viewMode']);
        $this->resetPage();
    }

    public function relistAsAvailable(int $unitId, \App\Services\UnitStatusService $statusService): void
    {
        $unit = Unit::query()->findOrFail($unitId);
        Gate::authorize('update', $unit);

        $result = $this->safely(fn () => $statusService->setAvailable(
            unit: $unit,
            userId: (int) auth()->id(),
            requestId: request()->header('X-Request-ID'),
            reason: 'Relisted from Sold Archive via Admin Panel',
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
        ), 'Could not relist the unit. Please try again.', ['unit_id' => $unit->id]);

        if ($result === null) {
            return;
        }

        session()->flash($result['changed'] ? 'status' : 'info', $result['message']);
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

        $deleted = $this->safely(function () use ($unit) {
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

            return true;
        }, 'Could not delete the unit. Please try again.', ['unit_id' => $unit->id]);

        if ($deleted === null) {
            return;
        }

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

        $restored = $this->safely(function () use ($unit) {
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

            return true;
        }, 'Could not restore the unit. Please try again.', ['unit_id' => $unit->id]);

        if ($restored === null) {
            return;
        }

        session()->flash('status', 'Unit restored.');
    }

    public function render(): View
    {
        Gate::authorize('viewAny', Unit::class);

        if (! $this->canManageTrash() && $this->viewMode === 'trashed') {
            $this->viewMode = 'active';
        }

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $activeCount = Unit::query()->where('status', Unit::STATUS_AVAILABLE)->count();
        $soldCount = Unit::query()->where('status', Unit::STATUS_SOLD)->count();
        $trashedCount = $this->canManageTrash() ? Unit::onlyTrashed()->count() : 0;

        $query = Unit::query()->with(['category', 'mainImage']);

        if ($this->viewMode === 'sold') {
            $query->where('status', Unit::STATUS_SOLD);
        } elseif ($this->viewMode === 'trashed' && $this->canManageTrash()) {
            $query->onlyTrashed();
        } else { // active
            $query->where('status', Unit::STATUS_AVAILABLE);
        }

        $statusFilter = in_array($this->status, Unit::statuses(), true)
            ? $this->status
            : '';

        $units = $query
            ->when(
                $this->search !== '',
                fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'),
            )
            ->when(
                $this->categoryId !== null,
                fn ($q) => $q->where('category_id', $this->categoryId),
            )
            ->when(
                $statusFilter !== '' && $this->viewMode === 'active',
                fn ($q) => $q->where('status', $statusFilter),
            )
            ->latest('updated_at')
            ->paginate(15);

        $recentStatusChanges = UnitStatusLog::query()
            ->with(['unit', 'user'])
            ->whereIn('action', [UnitStatusLog::ACTION_SET_AVAILABLE, UnitStatusLog::ACTION_SET_SOLD])
            ->latest()
            ->limit(3)
            ->get();

        $selectedHandoverUnit = $this->selectedHandoverUnitId
            ? Unit::query()->find($this->selectedHandoverUnitId)
            : null;

        return view('livewire.admin-units-index', [
            'categories' => $categories,
            'units' => $units,
            'activeCount' => $activeCount,
            'soldCount' => $soldCount,
            'trashedCount' => $trashedCount,
            'recentStatusChanges' => $recentStatusChanges,
            'canManageTrash' => $this->canManageTrash(),
            'selectedHandoverUnit' => $selectedHandoverUnit,
        ])->layout('layouts.admin-panel', [
            'title' => 'Unit Management',
        ]);
    }

    private function canManageTrash(): bool
    {
        return (bool) (auth()->user()?->is_admin ?? false);
    }
}
