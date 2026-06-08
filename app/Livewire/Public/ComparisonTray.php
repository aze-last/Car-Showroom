<?php

namespace App\Livewire\Public;

use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;

class ComparisonTray extends Component
{
    #[Session(key: 'compare_ids')]
    public array $compareIds = [];

    #[On('compare-updated')]
    public function refreshTray(): void
    {
        // Handled by Livewire's #[On] attribute to trigger a render.
    }

    public function toggleCompare(int $id): void
    {
        if (in_array($id, $this->compareIds)) {
            $this->compareIds = array_values(array_diff($this->compareIds, [$id]));
            
            $unit = Unit::find($id);
            $name = $unit ? $unit->name : 'Asset';
            $this->dispatch('toast', message: "Removed $name from Comparison", type: 'info');
            $this->dispatch('compare-updated');
        }
    }

    public function clearCompare(): void
    {
        $this->compareIds = [];
        $this->dispatch('toast', message: 'Comparison list cleared', type: 'info');
        $this->dispatch('compare-updated');
    }

    #[Computed]
    public function selectedUnits()
    {
        return Unit::query()
            ->with(['category', 'mainImage'])
            ->whereIn('id', $this->compareIds)
            ->get()
            ->sortBy(fn ($unit) => array_search($unit->id, $this->compareIds));
    }

    public function render(): View
    {
        $showComparison = \App\Models\Setting::get('design_show_comparison', true);

        return view('livewire.public.comparison-tray', [
            'showComparison' => $showComparison,
        ]);
    }
}
