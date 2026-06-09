<?php

namespace App\Livewire;

use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class UnitDetail extends Component
{
    public Unit $unit;

    public int $currentImageIndex = 0;

    public array $compareIds = [];

    public function mount(Unit $unit): void
    {
        $this->unit = $unit->load([
            'category',
            'images',
        ]);

        $this->compareIds = session()->get('compare_ids', []);
    }

    public function toggleCompare(int $id): void
    {
        $this->compareIds = session()->get('compare_ids', []);
        $unit = Unit::find($id);
        $name = $unit ? $unit->name : 'Asset';

        if (in_array($id, $this->compareIds)) {
            $this->compareIds = array_values(array_diff($this->compareIds, [$id]));
            session()->flash('toast', ['message' => "Removed $name from Comparison", 'type' => 'info']);
            session()->put('compare_ids', $this->compareIds);
            $this->dispatch('compare-updated');
        } elseif (count($this->compareIds) < 3) {
            $this->compareIds[] = $id;
            session()->flash('toast', ['message' => "Added $name to Comparison", 'type' => 'success']);
            session()->put('compare_ids', $this->compareIds);
            $this->dispatch('compare-updated');

            // Redirect back to catalog so they can select the next one
            $this->redirect(route('home'), navigate: true);

            return;
        } else {
            $this->dispatch('toast', message: 'Comparison limit reached (max 3)', type: 'info');
            $this->dispatch('compare-updated');
        }
    }

    #[On('compare-updated')]
    public function refreshCompare(): void
    {
        $this->compareIds = session()->get('compare_ids', []);
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

        $shopName = \App\Models\Setting::get('shop_name', 'The Gallery');
        $vehicleYearName = ($this->unit->year ? $this->unit->year.' ' : '').$this->unit->name;

        return view('livewire.unit-detail', [
            'activeImage' => $image,
            'similarUnits' => $similarUnits,
        ])->layout('components.layouts.public-showroom', [
            'title' => $vehicleYearName.' - Certified Luxury | '.$shopName,
            'description' => 'Inspect specs, images, and history for the certified '.$vehicleYearName.'. Request information or participate in live bidding at '.$shopName.' ✓',
            'metaImage' => $this->unit->mainImage ? \Illuminate\Support\Facades\Storage::url($this->unit->mainImage->url) : null,
        ]);
    }
}
