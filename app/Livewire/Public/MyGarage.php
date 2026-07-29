<?php

namespace App\Livewire\Public;

use App\Concerns\HandlesLivewireErrors;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MyGarage extends Component
{
    use HandlesLivewireErrors;

    public function removeUnit(int $unitId)
    {
        $this->safely(
            fn () => Auth::user()->savedUnits()->detach($unitId),
            'Could not remove the unit from your garage. Please try again.',
            ['unit_id' => $unitId],
        );
    }

    #[Layout('components.layouts.public-showroom')]
    public function render()
    {
        $user = Auth::user();
        $savedUnits = $user->savedUnits()->with('mainImage', 'category')->get();

        $acquiredUnits = \App\Models\Unit::query()
            ->with(['mainImage', 'category'])
            ->where('buyer_id', $user->id)
            ->where('status', \App\Models\Unit::STATUS_SOLD)
            ->latest('updated_at')
            ->get();

        $myBids = \App\Models\Bid::query()
            ->with(['auction.unit.mainImage', 'auction.unit.category'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->unique('auction_id');

        $collectionValue = $acquiredUnits->sum('price_php');
        $availableCount = $savedUnits->where('status', \App\Models\Unit::STATUS_AVAILABLE)->count();

        return view('livewire.public.my-garage', [
            'savedUnits' => $savedUnits,
            'acquiredUnits' => $acquiredUnits,
            'myBids' => $myBids,
            'collectionValue' => $collectionValue,
            'availableCount' => $availableCount,
        ]);
    }
}
