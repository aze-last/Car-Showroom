<?php

namespace App\Livewire\Public;

use App\Models\Auction;
use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AuctionHall extends Component
{
    use WithPagination;

    public function mount()
    {
        if (auth()->check()) {
            auth()->user()->unreadNotifications
                ->where('type', 'App\Notifications\BidPlacedNotification')
                ->markAsRead();
        }
    }

    public function render(): View
    {
        $featuredAuction = Auction::query()
            ->with(['unit.category', 'unit.images'])
            ->withCount('bids')
            ->where('status', 'live')
            ->latest('start_at')
            ->first();

        $activeLots = Auction::query()
            ->with(['unit.category', 'unit.images'])
            ->withCount('bids')
            ->whereIn('status', ['live', 'scheduled'])
            ->where('id', '!=', $featuredAuction?->id)
            ->orderBy('end_at', 'asc')
            ->paginate(12);

        return view('livewire.public.auction-hall', [
            'featuredAuction' => $featuredAuction,
            'activeLots' => $activeLots,
        ])->layout('components.layouts.public-showroom', [
            'title' => 'Auction Hall',
        ]);
    }
}
