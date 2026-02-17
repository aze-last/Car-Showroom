<?php

namespace App\Livewire;

use App\Models\Unit;
use App\Models\UnitStatusLog;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminLogs extends Component
{
    use WithPagination;

    #[Url(as: 'unit', history: true)]
    public ?int $unitId = null;

    #[Url(as: 'user', history: true)]
    public ?int $userId = null;

    #[Url(as: 'from', history: true)]
    public ?string $fromDate = null;

    #[Url(as: 'to', history: true)]
    public ?string $toDate = null;

    #[Url(as: 'action', history: true)]
    public ?string $actionType = null;

    public function updatedUnitId(): void
    {
        $this->resetPage();
    }

    public function updatedUserId(): void
    {
        $this->resetPage();
    }

    public function updatedFromDate(): void
    {
        $this->resetPage();
    }

    public function updatedToDate(): void
    {
        $this->resetPage();
    }

    public function updatedActionType(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['unitId', 'userId', 'fromDate', 'toDate', 'actionType']);
        $this->resetPage();
    }

    public function render(): View
    {
        Gate::authorize('viewAny', UnitStatusLog::class);

        $from = $this->parseDate($this->fromDate)?->startOfDay();
        $to = $this->parseDate($this->toDate)?->endOfDay();
        $actionFilter = in_array((string) $this->actionType, UnitStatusLog::actions(), true)
            ? $this->actionType
            : null;

        $logs = UnitStatusLog::query()
            ->with(['unit', 'user'])
            ->when(
                $this->unitId !== null,
                fn ($query) => $query->where('unit_id', $this->unitId),
            )
            ->when(
                $this->userId !== null,
                fn ($query) => $query->where('user_id', $this->userId),
            )
            ->when(
                $from !== null,
                fn ($query) => $query->where('created_at', '>=', $from),
            )
            ->when(
                $to !== null,
                fn ($query) => $query->where('created_at', '<=', $to),
            )
            ->when(
                $actionFilter !== null,
                fn ($query) => $query->where('action', $actionFilter),
            )
            ->latest()
            ->paginate(20);

        return view('livewire.admin-logs', [
            'logs' => $logs,
            'units' => Unit::query()->orderBy('name')->get(['id', 'name']),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
            'actions' => UnitStatusLog::actions(),
        ])->layout('layouts.admin-panel', [
            'title' => 'Status Logs',
        ]);
    }

    private function parseDate(?string $value): ?CarbonImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (InvalidFormatException) {
            return null;
        }
    }
}
