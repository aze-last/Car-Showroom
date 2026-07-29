<?php

namespace App\Livewire;

use App\Concerns\HandlesLivewireErrors;
use App\Models\Auction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class AdminAuctionsIndex extends Component
{
    use HandlesLivewireErrors;
    use WithPagination;

    public function mount(): void
    {
        Gate::authorize('access-admin');
    }

    public function delete(int $id): void
    {
        Gate::authorize('access-admin');

        $deleted = $this->safely(
            fn () => Auction::findOrFail($id)->delete(),
            'Could not delete the auction. Please try again.',
            ['auction_id' => $id],
        );

        if ($deleted === null) {
            return;
        }

        session()->flash('status', 'Auction deleted successfully.');
    }

    public function render(): View
    {
        $stats = [
            'active_value' => Auction::whereIn('status', ['live', 'active'])->where('end_at', '>', now())->with('unit')->get()->sum(fn ($a) => $a->current_bid_php ?: $a->starting_bid_php),
            'total_bids' => \App\Models\Bid::count(),
            'success_rate' => Auction::whereIn('status', ['completed', 'reserve_not_met', 'cancelled'])->count() > 0
                ? (Auction::where('status', 'completed')->count() / Auction::whereIn('status', ['completed', 'reserve_not_met', 'cancelled'])->count()) * 100
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
