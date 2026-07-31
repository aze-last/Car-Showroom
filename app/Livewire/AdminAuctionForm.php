<?php

namespace App\Livewire;

use App\Concerns\HandlesLivewireErrors;
use App\Models\Auction;
use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class AdminAuctionForm extends Component
{
    use HandlesLivewireErrors;

    public ?Auction $auction = null;

    public bool $isEdit = false;

    public int $unit_id;

    public string $lot_number = '';

    public string $start_at = '';

    public string $end_at = '';

    public int $reserve_price_php = 0;

    public int $starting_bid_php = 0;

    public string $status = 'scheduled';

    public bool $is_featured = false;

    public function mount(?Auction $auction = null): void
    {
        Gate::authorize('access-admin');

        if ($auction && $auction->exists) {
            $this->auction = $auction;
            $this->isEdit = true;
            $this->unit_id = $auction->unit_id;
            $this->lot_number = $auction->lot_number;
            $this->is_featured = (bool) $auction->is_featured;
            $this->start_at = $auction->start_at->format('Y-m-d\TH:i');
            $this->end_at = $auction->end_at->format('Y-m-d\TH:i');
            $this->reserve_price_php = $auction->reserve_price_php;
            $this->starting_bid_php = $auction->starting_bid_php;
            $this->status = $auction->status;
        } else {
            $this->start_at = now()->addDay()->format('Y-m-d\TH:i');
            $this->end_at = now()->addDay()->addHours(4)->format('Y-m-d\TH:i');

            if (request()->has('unit_id')) {
                $this->unit_id = (int) request()->query('unit_id');
                $this->applySuggestionsForUnit();
            }
        }
    }

    public function updatedUnitId(): void
    {
        $this->applySuggestionsForUnit();
    }

    public function applySuggestionsForUnit(): void
    {
        if ($this->unit_id && ! $this->isEdit) {
            $unit = Unit::find($this->unit_id);
            if ($unit) {
                $readiness = app(\App\Services\AuctionReadinessService::class)->evaluate($unit);
                if ($readiness['is_candidate'] && $readiness['suggested_reserve_php']) {
                    $this->reserve_price_php = $readiness['suggested_reserve_php'];
                    $this->starting_bid_php = $readiness['suggested_starting_bid_php'];
                }
            }
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
            'lot_number' => ['required', 'string', 'max:20', $this->isEdit ? 'unique:auctions,lot_number,'.$this->auction->id : 'unique:auctions,lot_number'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'reserve_price_php' => ['required', 'integer', 'min:0'],
            'starting_bid_php' => ['required', 'integer', 'min:0'],
            'status' => ['required', \Illuminate\Validation\Rule::in(Auction::STATUSES)],
            'is_featured' => ['boolean'],
        ]);

        $saved = $this->safely(function () use ($validated) {
            if ($this->isEdit) {
                $this->auction->update($validated);
                session()->flash('status', 'Auction updated successfully.');
            } else {
                Auction::create($validated);
                session()->flash('status', 'Auction scheduled successfully.');
            }

            return true;
        }, 'Could not save the auction. Please try again.', [
            'auction_id' => $this->auction?->id,
            'unit_id' => $this->unit_id,
        ]);

        if ($saved === null) {
            return;
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
