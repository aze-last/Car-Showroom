<?php

namespace App\Livewire\Public;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AuctionRoom extends Component
{
    public Auction $auction;
    public ?int $bidAmount = null;
    public string $message = '';
    public ?string $activeImage = null;

    public function mount(Auction $auction): void
    {
        $this->auction = $auction->load(['unit.category', 'unit.images', 'bids.user']);
        $this->bidAmount = ($this->auction->current_bid_php ?: $this->auction->starting_bid_php) + 50000;
        $this->activeImage = $this->auction->unit->mainImage?->url;
    }

    public function setActiveImage(string $url): void
    {
        $this->activeImage = $url;
    }

    public function placeBid(): void
    {
        if (!auth()->check()) {
            $this->redirectRoute('login');
            return;
        }

        // Logic Reviewer Guard: What if sold?
        if ($this->auction->unit->isSold()) {
            $this->message = 'This vehicle has been sold externally. Bidding is now closed.';
            return;
        }

        $minBid = ($this->auction->current_bid_php ?: $this->auction->starting_bid_php) + 10000;

        $validated = $this->validate([
            'bidAmount' => ['required', 'integer', 'min:' . $minBid],
        ]);

        DB::transaction(function () {
            // Lock the auction for update to prevent race conditions
            $auction = Auction::where('id', $this->auction->id)->lockForUpdate()->first();

            if (now()->greaterThan($auction->end_at)) {
                $this->addError('bidAmount', 'Auction has already ended.');
                return;
            }

            if ($this->bidAmount <= $auction->current_bid_php) {
                $this->addError('bidAmount', 'Someone else placed a higher bid. Please increase your bid.');
                return;
            }

            Bid::create([
                'auction_id' => $auction->id,
                'user_id' => auth()->id(),
                'amount_php' => $this->bidAmount,
            ]);

            $auction->update([
                'current_bid_php' => $this->bidAmount,
            ]);

            // Notify other collectors
            $otherBidders = $auction->bids()
                ->where('user_id', '!=', auth()->id())
                ->pluck('user_id')
                ->unique();

            foreach ($otherBidders as $bidderId) {
                $user = \App\Models\User::find($bidderId);
                if ($user) {
                    $user->notify(new \App\Notifications\BidPlacedNotification([
                        'message' => "New bid placed on {$auction->unit->name}: ₱" . number_format($this->bidAmount),
                        'auction_id' => $auction->id,
                        'unit_name' => $auction->unit->name,
                        'amount' => $this->bidAmount,
                    ]));
                }
            }

            // Anti-sniping: Extend by 2 minutes if bid is in last 2 minutes
            if (now()->diffInMinutes($auction->end_at) <= 2) {
                $auction->update([
                    'end_at' => now()->addMinutes(2),
                ]);
            }

            $this->auction = $auction->fresh(['unit.category', 'unit.images', 'bids.user']);
            $this->bidAmount = $this->auction->current_bid_php + 50000;
            $this->message = 'Bid placed successfully!';
        });
    }

    public function render(): View
    {
        return view('livewire.public.auction-room')
            ->layout('components.layouts.public-showroom', [
                'title' => 'Lot ' . $this->auction->lot_number . ' | ' . $this->auction->unit->name,
            ]);
    }
}
