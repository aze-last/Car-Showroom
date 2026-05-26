<?php

namespace App\Livewire;

use App\Models\Auction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class AdminAuctionsIndex extends Component
{
    use WithPagination;

    public function mount(): void
    {
        Gate::authorize('access-admin');
    }

    public function delete(int $id): void
    {
        Gate::authorize('access-admin');
        Auction::findOrFail($id)->delete();
        session()->flash('status', 'Auction deleted successfully.');
    }

    public function render(): View
    {
        return view('livewire.admin-auctions-index', [
            'auctions' => Auction::query()
                ->with(['unit'])
                ->latest()
                ->paginate(10),
        ])->layout('layouts.admin-panel', [
            'title' => 'Manage Auctions',
        ]);
    }
}
