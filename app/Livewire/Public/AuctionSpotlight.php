<?php

namespace App\Livewire\Public;

use App\Models\Auction;
use Livewire\Component;

class AuctionSpotlight extends Component
{
    public function render()
    {
        $featuredAuction = Auction::query()
            ->with(['unit.mainImage', 'unit.category'])
            ->whereIn('status', ['live', 'active'])
            ->orderByDesc('is_featured')
            ->first();

        return view('livewire.public.auction-spotlight', [
            'featuredAuction' => $featuredAuction,
        ]);
    }
}
