<?php

namespace App\Livewire;

use App\Concerns\HandlesLivewireErrors;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class AdminCustomersIndex extends Component
{
    use HandlesLivewireErrors;
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public function mount(): void
    {
        Gate::authorize('access-admin');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function toggleBlock(int $userId): void
    {
        Gate::authorize('access-admin');

        $customer = User::query()->customers()->find($userId);

        if (! $customer instanceof User) {
            session()->flash('error', 'Customer account not found.');

            return;
        }

        $newStatus = ! $customer->is_blocked;

        $updated = $this->safely(function () use ($customer, $newStatus) {
            $customer->forceFill([
                'is_blocked' => $newStatus,
            ])->save();

            return true;
        }, 'Could not update customer status. Please try again.');

        if ($updated === null) {
            return;
        }

        $actionText = $newStatus ? 'blocked' : 'unblocked';
        $message = "Customer {$customer->name} has been {$actionText}.";

        $this->dispatch('toast', message: $message, type: $newStatus ? 'error' : 'success');
        session()->flash('status', $message);
    }

    public function render(): View
    {
        Gate::authorize('access-admin');

        $customers = User::query()
            ->customers()
            ->withCount(['savedUnits', 'bids', 'bidDeposits', 'chatMessages'])
            ->when($this->search !== '', function ($q) {
                $term = '%'.trim($this->search).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->when($this->statusFilter === 'active', fn ($q) => $q->where('is_blocked', false))
            ->when($this->statusFilter === 'blocked', fn ($q) => $q->where('is_blocked', true))
            ->orderByDesc('id')
            ->paginate(12);

        $totalCustomers = User::query()->customers()->count();
        $activeCount = User::query()->customers()->where('is_blocked', false)->count();
        $blockedCount = User::query()->customers()->where('is_blocked', true)->count();

        return view('livewire.admin-customers-index', [
            'customers' => $customers,
            'totalCustomers' => $totalCustomers,
            'activeCount' => $activeCount,
            'blockedCount' => $blockedCount,
        ])->layout('layouts.admin-panel', [
            'title' => 'Customer Activity & Security',
        ]);
    }
}
