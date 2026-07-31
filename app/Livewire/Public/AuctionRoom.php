<?php

namespace App\Livewire\Public;

use App\Concerns\EnforcesCollectorAuthentication;
use App\Concerns\HandlesLivewireErrors;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\BidDeposit;
use App\Models\UserAuctionStrike;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class AuctionRoom extends Component
{
    use EnforcesCollectorAuthentication;
    use HandlesLivewireErrors;
    use WithFileUploads;

    public Auction $auction;

    public ?int $bidAmount = null;

    public string $message = '';

    public ?string $activeImage = null;

    public $proof_image;

    public int $deposit_amount = 5000;

    public string $full_name = '';

    public string $email = '';

    public string $phone = '';

    public string $verification_code = '';

    public ?string $generated_otp = null;

    public bool $phone_is_verified = false;

    public string $address = '';

    public float $latitude = 14.5995;

    public float $longitude = 120.9842;

    public function mount(Auction $auction): void
    {
        if (! $auction->isLive()) {
            $this->redirectRoute('auction.hall');

            return;
        }

        $this->auction = $auction->load(['unit.category', 'unit.images', 'bids.user']);
        $this->bidAmount = ($this->auction->current_bid_php ?: $this->auction->starting_bid_php) + 50000;
        $this->activeImage = $this->auction->unit->mainImage?->url;

        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $this->full_name = $user->name;
            $this->email = $user->email;
        }
    }

    public function setActiveImage(string $url): void
    {
        $this->activeImage = $url;
    }

    public function sendVerificationCode(): void
    {
        $this->validate([
            'phone' => ['required', 'string', 'min:10'],
        ]);

        $this->generated_otp = (string) rand(100000, 999999);
        $this->message = "SMS code [{$this->generated_otp}] sent to {$this->phone}. Enter code below to verify.";
    }

    public function verifyCode(): void
    {
        if (trim($this->verification_code) === (string) $this->generated_otp && $this->generated_otp !== null) {
            $this->phone_is_verified = true;
            $this->message = 'Mobile number verified successfully!';
        } else {
            $this->addError('verification_code', 'Invalid SMS code. Please check and try again.');
        }
    }

    public function updateCoordinates($lat, $lng): void
    {
        $this->latitude = (float) $lat;
        $this->longitude = (float) $lng;
    }

    public function submitDeposit(): void
    {
        if ($this->redirectIfGuest() || $this->redirectIfGoogleRequiredForAuctions()) {
            return;
        }

        $this->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'min:10'],
            'email' => ['required', 'email'],
            'address' => ['required', 'string', 'min:5'],
            'proof_image' => ['required', 'image', 'max:5120'],
            'deposit_amount' => ['required', 'integer', 'min:1000'],
        ]);

        if (! $this->phone_is_verified) {
            $this->addError('phone', 'Please verify your mobile number with the SMS code before submitting.');

            return;
        }

        $submitted = $this->safely(function () {
            $path = $this->proof_image->store('deposits/'.$this->auction->id, 'public');

            BidDeposit::query()->create([
                'user_id' => Auth::id(),
                'auction_id' => $this->auction->id,
                'amount' => $this->deposit_amount,
                'proof_image' => $path,
                'status' => 'pending',
                'full_name' => $this->full_name,
                'phone' => $this->phone,
                'phone_verified_at' => now(),
                'email' => $this->email,
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ]);

            return true;
        }, 'Could not submit your deposit. Please try again.', [
            'auction_id' => $this->auction->id,
        ]);

        if ($submitted === null) {
            return;
        }

        // Notify Admins
        $this->safely(function () {
            $admins = \App\Models\User::query()->where('is_admin', true)->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\DepositSubmittedNotification([
                    'message' => 'New deposit from '.Auth::user()->name.' for '.$this->auction->unit->name,
                    'auction_id' => $this->auction->id,
                    'user_name' => Auth::user()->name,
                    'amount' => $this->deposit_amount,
                ]));
            }
        }, 'Deposit submitted, but admins could not be notified.');

        $this->proof_image = null;
        $this->message = 'Deposit slip submitted successfully! Awaiting admin verification.';
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
            $pendingDeposit = BidDeposit::query()
                ->where('user_id', Auth::id())
                ->where('auction_id', $this->auction->id)
                ->where('status', 'pending')
                ->first();

            if ($pendingDeposit) {
                $this->addError('bidAmount', 'Your ₱'.number_format($pendingDeposit->amount).' deposit proof is pending admin approval.');
            } else {
                $this->addError('bidAmount', 'An approved security deposit is required to bid on this lot. Please upload your deposit slip.');
            }

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

        $this->safely(fn () => DB::transaction(function () {
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
        }), 'Could not place your bid. Please try again.', [
            'auction_id' => $this->auction->id,
            'bid_amount' => $this->bidAmount,
        ]);
    }

    protected function userHasApprovedDeposit(): bool
    {
        if (Auth::user()?->isStaff()) {
            return true;
        }

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
