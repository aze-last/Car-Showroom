<?php

namespace App\Livewire;

use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UnitDetail extends Component
{
    public Unit $unit;

    public int $currentImageIndex = 0;

    public function mount(Unit $unit): void
    {
        $this->unit = $unit->load([
            'category',
            'images',
        ]);
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

        return view('livewire.unit-detail', [
            'activeImage' => $image,
        ])->layout('layouts.public-showroom', [
            'title' => $this->unit->name,
        ]);
    }
}
