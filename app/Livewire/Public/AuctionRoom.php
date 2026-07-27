<?php

namespace App\Livewire\Public;

use App\Concerns\EnforcesCollectorAuthentication;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\BidDeposit;
use App\Models\UserAuctionStrike;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AuctionRoom extends Component
{
    use EnforcesCollectorAuthentication;

    public Auction $auction;

    public ?int $bidAmount = null;

    public string $message = '';

    public ?string $activeImage = null;

    public function mount(Auction $auction): void
    {
        if (! $auction->isLive()) {
            $this->redirectRoute('auction.hall');

            return;
        }

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
        if ($this->redirectIfGuest() || $this->redirectIfGoogleRequiredForAuctions()) {
            return;
        }

        // Logic Reviewer Guard: What if sold?
        if ($this->auction->unit->isSold()) {
            $this->message = 'This vehicle has been sold externally. Bidding is now closed.';

            return;
        }

        // Fast pre-checks for UX feedback (re-verified inside the transaction below).
        if (! $this->userHasApprovedDeposit()) {
            $this->addError('bidAmount', 'You need an approved deposit to bid on this lot.');

            return;
        }

        if ($suspensionError = $this->userSuspensionError()) {
            $this->addError('bidAmount', $suspensionError);

            return;
        }

        $currentPrice = $this->auction->current_bid_php ?: $this->auction->starting_bid_php;
        $minBid = $currentPrice + 10000;
        $maxBid = (int) floor($currentPrice * 1.5);

        $validated = $this->validate([
            'bidAmount' => ['required', 'integer', 'min:'.$minBid, 'max:'.$maxBid],
        ], [
            'bidAmount.min' => 'Minimum bid increment is ₱10,000 (₱'.number_format($minBid).').',
            'bidAmount.max' => 'Maximum bid jump is 50% above the current price (₱'.number_format($maxBid).').',
        ]);

        // Rate limiting: 10 bids per minute per user/IP
        $rateLimitKey = 'bidding:'.(Auth::id() ?: request()->ip());
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            $this->addError('bidAmount', 'You are bidding too fast. Please wait a moment.');

            return;
        }
        \Illuminate\Support\Facades\RateLimiter::hit($rateLimitKey, 60);

        DB::transaction(function () {
            // Lock the auction for update to prevent race conditions
            $auction = Auction::query()->where('id', $this->auction->id)->lockForUpdate()->first();

            if (! $auction->isLive()) {
                $this->addError('bidAmount', 'This auction is no longer accepting bids.');

                return;
            }

            if (now()->greaterThan($auction->end_at)) {
                $this->addError('bidAmount', 'Auction has already ended.');

                return;
            }

            // Re-verify account state inside the lock: deposit approval or
            // suspension could have changed between form load and submit.
            if (! $this->userHasApprovedDeposit()) {
                $this->addError('bidAmount', 'You need an approved deposit to bid on this lot.');

                return;
            }

            if ($suspensionError = $this->userSuspensionError()) {
                $this->addError('bidAmount', $suspensionError);

                return;
            }

            if ($this->bidAmount <= $auction->current_bid_php) {
                $this->addError('bidAmount', 'Someone else placed a higher bid. Please increase your bid.');

                return;
            }

            Bid::query()->create([
                'auction_id' => $auction->id,
                'user_id' => Auth::id(),
                'amount_php' => $this->bidAmount,
            ]);

            $auction->update([
                'current_bid_php' => $this->bidAmount,
            ]);

            // Notify other collectors
            $otherBidders = $auction->bids()
                ->where('user_id', '!=', Auth::id())
                ->pluck('user_id')
                ->unique();

            foreach ($otherBidders as $bidderId) {
                $user = \App\Models\User::query()->find($bidderId);
                if ($user) {
                    $user->notify(new \App\Notifications\BidPlacedNotification([
                        'message' => "New bid placed on {$auction->unit->name}: ₱".number_format($this->bidAmount),
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

    protected function userHasApprovedDeposit(): bool
    {
        return BidDeposit::query()
            ->where('user_id', Auth::id())
            ->where('auction_id', $this->auction->id)
            ->where('status', 'approved')
            ->exists();
    }

    protected function userSuspensionError(): ?string
    {
        $strike = UserAuctionStrike::query()
            ->where('user_id', Auth::id())
            ->first();

        if ($strike?->is_suspended && $strike->suspended_until && now()->lessThan($strike->suspended_until)) {
            return 'Your account is suspended from bidding until '
                .$strike->suspended_until->format('M d, Y H:i')
                .' due to repeated non-payment.';
        }

        return null;
    }

    public function render(): View
    {
        return view('livewire.public.auction-room')
            ->layout('components.layouts.public-showroom', [
                'title' => 'Lot '.$this->auction->lot_number.' | '.$this->auction->unit->name,
            ]);
    }
}
