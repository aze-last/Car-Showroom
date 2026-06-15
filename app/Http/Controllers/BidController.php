<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\BidDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    /**
     * Submit a bid for an auction.
     */
    public function store(Request $request, Auction $auction)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (! $user->canParticipateInAuctions()) {
            return redirect()
                ->route('auth.google.redirect')
                ->with('status', 'Sign in with Google to participate in auctions.');
        }

        // 1. Check if user is suspended
        if ($user->auctionStrike?->is_suspended && now()->lessThan($user->auctionStrike->suspended_until)) {
            return back()->with('error', 'Your account is suspended from bidding.');
        }

        // 2. Check if auction is active
        if ($auction->status !== 'active' && $auction->status !== 'live') {
            return back()->with('error', 'Auction is not currently active.');
        }

        // 3. Check if user has an approved deposit for this auction
        $hasApprovedDeposit = BidDeposit::query()->where('user_id', $user->id)
            ->where('auction_id', $auction->id)
            ->where('status', 'approved')
            ->exists();

        if (! $hasApprovedDeposit) {
            return back()->with('error', 'You must have an approved deposit to bid on this auction.');
        }

        // 4. Validate bid amount
        $currentPrice = $auction->current_bid_php ?: $auction->starting_bid_php;
        $minBid = $currentPrice + ($currentPrice * 0.05);
        $maxBid = $currentPrice + ($currentPrice * 0.50);

        $request->validate([
            'amount' => ['required', 'integer', 'min:'.$minBid, 'max:'.$maxBid],
        ], [
            'amount.min' => 'Minimum bid increment is 5% (₱'.number_format($minBid).').',
            'amount.max' => 'Maximum bid jump is 50% (₱'.number_format($maxBid).').',
        ]);

        // 5. Place the bid within a transaction to handle concurrency
        return DB::transaction(function () use ($request, $auction, $user) {
            // Lock the auction row for update to prevent race conditions
            $auction = Auction::query()->where('id', $auction->id)->lockForUpdate()->first();

            // Re-validate current price inside the lock
            $currentPrice = $auction->current_bid_php ?: $auction->starting_bid_php;
            if ($request->amount <= $currentPrice) {
                return back()->with('error', 'Someone placed a higher bid already.');
            }

            Bid::create([
                'user_id' => $user->id,
                'auction_id' => $auction->id,
                'amount_php' => $request->amount,
            ]);

            $auction->update(['current_bid_php' => $request->amount]);

            return back()->with('status', 'Bid placed successfully!');
        });
    }

    /**
     * Join an auction by submitting a deposit proof.
     */
    public function join(Request $request, Auction $auction)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (! $user->canParticipateInAuctions()) {
            return redirect()
                ->route('auth.google.redirect')
                ->with('status', 'Sign in with Google to participate in auctions.');
        }

        $request->validate([
            'amount' => ['required', 'integer', 'min:1'], // Usually fixed or min amount
            'proof_image' => ['required', 'image', 'max:5120'], // 5MB
        ]);

        $path = $request->file('proof_image')->store('deposits/'.$auction->id, 'public');

        BidDeposit::query()->create([
            'user_id' => $user->id,
            'auction_id' => $auction->id,
            'amount' => $request->amount,
            'proof_image' => $path,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Deposit submitted. Please wait for admin approval.');
    }
}
