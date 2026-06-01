<?php

namespace App\Livewire;

use App\Models\BidDeposit;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class AdminDepositVerification extends Component
{
    use WithPagination;

    public ?int $selectedDepositId = null;
    public string $adminNote = '';

    public function mount(): void
    {
        Gate::authorize('access-admin');
    }

    public function approve(int $id): void
    {
        Gate::authorize('access-admin');
        
        $deposit = BidDeposit::with(['user', 'auction.unit'])->findOrFail($id);
        $deposit->update(['status' => 'approved']);

        // Notify User
        $deposit->user->notify(new \App\Notifications\DepositApprovedNotification([
            'message' => "Your deposit for " . $deposit->auction->unit->name . " has been approved. You can now enter the auction room.",
            'auction_id' => $deposit->auction_id,
            'unit_name' => $deposit->auction->unit->name,
        ]));
        
        session()->flash('status', 'Deposit for ' . $deposit->user->name . ' approved.');
    }

    public function openRejectModal(int $id): void
    {
        $this->selectedDepositId = $id;
        $this->adminNote = '';
    }

    public function reject(): void
    {
        Gate::authorize('access-admin');
        
        $this->validate([
            'adminNote' => ['required', 'string', 'max:255'],
        ]);

        $deposit = BidDeposit::with(['user', 'auction.unit'])->findOrFail($this->selectedDepositId);
        $deposit->update([
            'status' => 'rejected',
            'admin_note' => $this->adminNote,
        ]);

        // Notify User
        $deposit->user->notify(new \App\Notifications\DepositRejectedNotification([
            'message' => "Your deposit for " . $deposit->auction->unit->name . " was rejected.",
            'auction_id' => $deposit->auction_id,
            'unit_name' => $deposit->auction->unit->name,
            'reason' => $this->adminNote,
        ]));

        $this->selectedDepositId = null;
        $this->dispatch('close-modal', name: 'reject-deposit-modal');
        
        session()->flash('status', 'Deposit rejected.');
    }

    public function render(): View
    {
        $deposits = BidDeposit::with(['user', 'auction.unit'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        return view('livewire.admin-deposit-verification', [
            'deposits' => $deposits,
        ])->layout('layouts.admin-panel', [
            'title' => 'Deposit Verification',
        ]);
    }
}
