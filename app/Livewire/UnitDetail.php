<?php

namespace App\Livewire;

use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Session;
use Livewire\Component;

class UnitDetail extends Component
{
    public Unit $unit;

    public int $currentImageIndex = 0;

    #[Session(key: 'compare_ids')]
    public array $compareIds = [];

    public function mount(Unit $unit): void
    {
        $this->unit = $unit->load([
            'category',
            'images',
        ]);

        if (! is_array($this->compareIds)) {
            $this->compareIds = [];
        }
    }

    public function toggleCompare(int $id): void
    {
        $unit = Unit::find($id);
        $name = $unit ? $unit->name : 'Asset';

        if (in_array($id, $this->compareIds)) {
            $this->compareIds = array_values(array_diff($this->compareIds, [$id]));
            session()->flash('toast', ['message' => "Removed $name from Comparison", 'type' => 'info']);
            $this->dispatch('compare-updated');
        } elseif (count($this->compareIds) < 3) {
            $this->compareIds[] = $id;
            session()->flash('toast', ['message' => "Added $name to Comparison", 'type' => 'success']);
            $this->dispatch('compare-updated');
            
            // Redirect back to catalog so they can select the next one
            $this->redirect(route('home'), navigate: true);
            return;
        } else {
            $this->dispatch('toast', message: "Comparison limit reached (max 3)", type: 'info');
            $this->dispatch('compare-updated');
        }
    }

    public function nextImage(): void
    {
        if ($this->unit->images->isEmpty()) {
            return;
        }

        $this->currentImageIndex = ($this->currentImageIndex + 1) % $this->unit->images->count();
    }

    public function previousImage(): void
    {
        if ($this->unit->images->isEmpty()) {
            return;
        }

        $this->currentImageIndex = ($this->currentImageIndex - 1 + $this->unit->images->count()) % $this->unit->images->count();
    }

    public function render(): View
    {
        $image = $this->unit->images->get($this->currentImageIndex);

        $similarUnits = Unit::query()
            ->with(['category', 'mainImage'])
            ->where('category_id', $this->unit->category_id)
            ->where('id', '!=', $this->unit->id)
            ->where('status', Unit::STATUS_AVAILABLE)
            ->latest('updated_at')
            ->take(3)
            ->get();

        return view('livewire.unit-detail', [
            'activeImage' => $image,
            'similarUnits' => $similarUnits,
        ])->layout('components.layouts.public-showroom', [
            'title' => $this->unit->name,
        ]);
    }
}
