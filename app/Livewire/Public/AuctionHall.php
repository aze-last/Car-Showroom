<?php

namespace App\Livewire\Public;

use App\Concerns\EnforcesCollectorAuthentication;
use App\Models\Auction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AuctionHall extends Component
{
    use EnforcesCollectorAuthentication;
    use \Livewire\WithFileUploads, WithPagination;

    public ?Auction $selectedAuction = null;

    public $proof_image;

    public ?int $deposit_amount = 5000; // Default or dynamic

    public function mount()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->unreadNotifications
                ->where('type', 'App\Notifications\BidPlacedNotification')
                ->markAsRead();
        }
    }

    public function openJoinModal(int $auctionId): void
    {
        if ($this->redirectIfGuest() || $this->redirectIfGoogleRequiredForAuctions()) {
            return;
        }

        $this->selectedAuction = Auction::with('unit')->findOrFail($auctionId);
    }

    public function submitDeposit(): void
    {
        if ($this->redirectIfGuest() || $this->redirectIfGoogleRequiredForAuctions()) {
            return;
        }

        $this->validate([
            'proof_image' => ['required', 'image', 'max:5120'],
            'deposit_amount' => ['required', 'integer', 'min:1000'],
        ]);

        $path = $this->proof_image->store('deposits/'.$this->selectedAuction->id, 'public');

        $deposit = \App\Models\BidDeposit::query()->create([
            'user_id' => Auth::id(),
            'auction_id' => $this->selectedAuction->id,
            'amount' => $this->deposit_amount,
            'proof_image' => $path,
            'status' => 'pending',
        ]);

        // Notify Admins
        $admins = \App\Models\User::query()->where('is_admin', true)->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\DepositSubmittedNotification([
                'message' => 'New deposit from '.Auth::user()->name.' for '.$this->selectedAuction->unit->name,
                'auction_id' => $this->selectedAuction->id,
                'user_name' => Auth::user()->name,
                'amount' => $this->deposit_amount,
            ]));
        }

        $this->proof_image = null;

        session()->flash('status', 'Successfully sent your entry. Please wait for admin approval.');
    }

    public function render(): View
    {
        $featuredAuction = Auction::query()
            ->with(['unit.category', 'unit.images'])
            ->withCount('bids')
            ->whereIn('status', ['live', 'active'])
            ->where('end_at', '>', now())
            ->latest('start_at')
            ->first();

        $activeLots = Auction::query()
            ->with(['unit.category', 'unit.images'])
            ->withCount('bids')
            ->whereIn('status', ['live', 'active', 'scheduled'])
            ->where('id', '!=', $featuredAuction?->id)
            ->where('end_at', '>', now())
            ->orderBy('end_at', 'asc')
            ->paginate(12);

        return view('livewire.public.auction-hall', [
            'featuredAuction' => $featuredAuction,
            'activeLots' => $activeLots,
        ])->layout('components.layouts.public-showroom', [
            'title' => 'Auction Hall',
        ]);
    }
}
