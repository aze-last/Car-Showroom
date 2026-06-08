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
        $stats = [
            'active_value' => Auction::where('status', 'live')->with('unit')->get()->sum(fn ($a) => $a->current_bid_php ?: $a->starting_bid_php),
            'total_bids' => \App\Models\Bid::count(),
            'success_rate' => Auction::whereIn('status', ['completed', 'cancelled'])->count() > 0
                ? (Auction::where('status', 'completed')->count() / Auction::whereIn('status', ['completed', 'cancelled'])->count()) * 100
                : 0,
        ];

        return view('livewire.admin-auctions-index', [
            'auctions' => Auction::query()
                ->with(['unit'])
                ->latest()
                ->paginate(10),
            'stats' => $stats,
        ])->layout('layouts.admin-panel', [
            'title' => 'Manage Auctions',
        ]);
    }
}
