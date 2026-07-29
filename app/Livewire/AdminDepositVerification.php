<?php

namespace App\Livewire;

use App\Concerns\HandlesLivewireErrors;
use App\Models\BidDeposit;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class AdminDepositVerification extends Component
{
    use HandlesLivewireErrors;
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

        $updated = $this->safely(
            fn () => $deposit->update(['status' => 'approved']),
            'Could not approve the deposit. Please try again.',
            ['deposit_id' => $deposit->id],
        );

        if ($updated === null) {
            return;
        }

        // Notify User — approval already succeeded, so a notification failure
        // is reported but does not undo the approval.
        $notified = $this->safely(fn () => $deposit->user->notify(new \App\Notifications\DepositApprovedNotification([
            'message' => 'Your deposit for '.$deposit->auction->unit->name.' has been approved. You can now enter the auction room.',
            'auction_id' => $deposit->auction_id,
            'unit_name' => $deposit->auction->unit->name,
        ])) ?? true, 'Deposit approved, but the collector could not be notified.', ['deposit_id' => $deposit->id]);

        if ($notified === null) {
            return;
        }

        session()->flash('status', 'Deposit for '.$deposit->user->name.' approved.');
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

        $updated = $this->safely(
            fn () => $deposit->update([
                'status' => 'rejected',
                'admin_note' => $this->adminNote,
            ]),
            'Could not reject the deposit. Please try again.',
            ['deposit_id' => $deposit->id],
        );

        if ($updated === null) {
            return;
        }

        // Notify User — rejection already succeeded; a notification failure is
        // reported but does not undo it.
        $notified = $this->safely(fn () => $deposit->user->notify(new \App\Notifications\DepositRejectedNotification([
            'message' => 'Your deposit for '.$deposit->auction->unit->name.' was rejected.',
            'auction_id' => $deposit->auction_id,
            'unit_name' => $deposit->auction->unit->name,
            'reason' => $this->adminNote,
        ])) ?? true, 'Deposit rejected, but the collector could not be notified.', ['deposit_id' => $deposit->id]);

        $this->selectedDepositId = null;
        $this->dispatch('close-modal', name: 'reject-deposit-modal');

        if ($notified === null) {
            return;
        }

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
