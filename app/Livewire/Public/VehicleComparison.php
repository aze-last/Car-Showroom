<?php

namespace App\Livewire\Public;

use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class VehicleComparison extends Component
{
    public array $compareIds = [];

    public function mount(): void
    {
        $this->compareIds = session()->get('compare_ids', []);
    }

    public function removeFromComparison(int $id): void
    {
        $this->compareIds = session()->get('compare_ids', []);
        $this->compareIds = array_values(array_diff($this->compareIds, [$id]));
        session()->put('compare_ids', $this->compareIds);
        $this->dispatch('compare-updated');

        if (empty($this->compareIds)) {
            $this->redirect(route('home'), navigate: true);
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
