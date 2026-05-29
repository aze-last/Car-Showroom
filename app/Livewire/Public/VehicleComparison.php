<?php

namespace App\Livewire\Public;

use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Session;
use Livewire\Component;

class VehicleComparison extends Component
{
    #[Session(key: 'compare_ids')]
    public array $compareIds = [];

    public function removeFromComparison(int $id): void
    {
        $this->compareIds = array_values(array_diff($this->compareIds, [$id]));

        if (empty($this->compareIds)) {
            $this->redirect(route('units.index'), navigate: true);
        }
    }

    public function render(): View
    {
        $units = Unit::query()
            ->with(['category', 'mainImage'])
            ->whereIn('id', $this->compareIds)
            ->get()
            ->sortBy(function ($unit) {
                return array_search($unit->id, $this->compareIds);
            });

        return view('livewire.public.vehicle-comparison', [
            'units' => $units,
        ])->layout('components.layouts.public-showroom', [
            'title' => 'Vehicle Comparison',
        ]);
    }
}
