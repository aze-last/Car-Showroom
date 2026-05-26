<?php

namespace App\Livewire;

use App\Models\Auction;
use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class AdminAuctionForm extends Component
{
    public ?Auction $auction = null;
    public bool $isEdit = false;

    public int $unit_id;
    public string $lot_number = '';
    public string $start_at = '';
    public string $end_at = '';
    public int $reserve_price_php = 0;
    public int $starting_bid_php = 0;
    public string $status = 'scheduled';

    public function mount(?Auction $auction = null): void
    {
        Gate::authorize('access-admin');

        if ($auction && $auction->exists) {
            $this->auction = $auction;
            $this->isEdit = true;
            $this->unit_id = $auction->unit_id;
            $this->lot_number = $auction->lot_number;
            $this->start_at = $auction->start_at->format('Y-m-d\TH:i');
            $this->end_at = $auction->end_at->format('Y-m-d\TH:i');
            $this->reserve_price_php = $auction->reserve_price_php;
            $this->starting_bid_php = $auction->starting_bid_php;
            $this->status = $auction->status;
        } else {
            $this->start_at = now()->addDay()->format('Y-m-d\TH:i');
            $this->end_at = now()->addDay()->addHours(4)->format('Y-m-d\TH:i');
        }
    }

    public function updatedStatus(string $value): void
    {
        if ($value === 'live') {
            $this->start_at = now()->format('Y-m-d\TH:i');
        }
    }

    public function save(): void
    {
        Gate::authorize('access-admin');

        $validated = $this->validate([
            'unit_id' => ['required', 'exists:units,id'],
            'lot_number' => ['required', 'string', 'max:20', $this->isEdit ? 'unique:auctions,lot_number,' . $this->auction->id : 'unique:auctions,lot_number'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'reserve_price_php' => ['required', 'integer', 'min:0'],
            'starting_bid_php' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:scheduled,live,completed,cancelled'],
        ]);

        if ($this->isEdit) {
            $this->auction->update($validated);
            session()->flash('status', 'Auction updated successfully.');
        } else {
            Auction::create($validated);
            session()->flash('status', 'Auction scheduled successfully.');
        }

        $this->redirectRoute('admin.auctions.index');
    }

    public function render(): View
    {
        return view('livewire.admin-auction-form', [
            'units' => Unit::query()->orderBy('name')->get(),
        ])->layout('layouts.admin-panel', [
            'title' => $this->isEdit ? 'Edit Auction' : 'Schedule New Auction',
        ]);
    }
}
