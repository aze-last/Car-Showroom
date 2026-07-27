<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\BidDeposit;
use App\Models\UserAuctionStrike;
use App\Notifications\AuctionWinnerNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAuctionDeadlines extends Command
{
    protected $signature = 'auction:check-deadlines';

    protected $description = 'Processes ended auctions and payment deadlines.';

    public function handle()
    {
        $this->processScheduledAuctions();
        $this->processEndedAuctions();
        $this->processPaymentDeadlines();
    }

    /**
     * Move scheduled auctions to live when start_at is reached.
     */
    private function processScheduledAuctions()
    {
        $toActivate = Auction::where('status', 'scheduled')
            ->where('start_at', '<=', now())
            ->get();

        foreach ($toActivate as $auction) {
            $auction->update(['status' => 'live']);
            $this->info("Activated Auction #{$auction->id}");
        }
    }

    /**
     * Finalize auctions that have reached their end time.
     */
    private function processEndedAuctions()
    {
        $endedAuctions = Auction::whereIn('status', ['active', 'live'])
            ->where('end_at', '<=', now())
            ->get();

        foreach ($endedAuctions as $auction) {
            DB::transaction(function () use ($auction) {
                $winningBid = $auction->bids()->orderByDesc('amount_php')->first();
                $fallbackBid = $auction->bids()
                    ->when($winningBid, fn ($query) => $query->where('user_id', '!=', $winningBid->user_id))
                    ->orderByDesc('amount_php')
                    ->first();

                if (! $winningBid) {
                    $auction->update(['status' => Auction::STATUS_CANCELLED]); // No bids

                    // Release every approved deposit — nobody won anything.
                    BidDeposit::where('auction_id', $auction->id)
                        ->where('status', 'approved')
                        ->update(['status' => 'refunded']);

                    return;
                }

                if ($auction->reserveNotMetBy($winningBid->amount_php)) {
                    // Reserve not met: no winner, no payment deadline.
                    $auction->update(['status' => Auction::STATUS_RESERVE_NOT_MET]);

                    BidDeposit::where('auction_id', $auction->id)
                        ->where('status', 'approved')
                        ->update(['status' => 'refunded']);

                    return;
                }

                $auction->update([
                    'status' => Auction::STATUS_COMPLETED,
                    'winner_user_id' => $winningBid->user_id,
                    'fallback_user_id' => $fallbackBid ? $fallbackBid->user_id : null,
                    'payment_deadline' => now()->addHours(48),
                ]);

                // Set winner deposit to 'applied' or similar if needed,
                // but usually it stays 'approved' until payment.

                // Mark losers for refund — but keep the fallback bidder's deposit
                // approved so it can back a reassignment if the winner defaults.
                BidDeposit::where('auction_id', $auction->id)
                    ->whereNotIn('user_id', array_filter([
                        $winningBid->user_id,
                        $fallbackBid?->user_id,
                    ]))
                    ->where('status', 'approved')
                    ->update(['status' => 'refunded']);
            });

            $this->info("Processed ending for Auction #{$auction->id}");
        }
    }

    /**
     * Handle winners who failed to pay within 48 hours.
     */
    private function processPaymentDeadlines()
    {
        $expiredPayments = Auction::where('status', 'completed')
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<=', now())
            ->get();

        foreach ($expiredPayments as $auction) {
            DB::transaction(function () use ($auction) {
                $winnerId = $auction->winner_user_id;

                // 1. Forfeit winner's deposit
                BidDeposit::where('auction_id', $auction->id)
                    ->where('user_id', $winnerId)
                    ->update(['status' => 'forfeited', 'admin_note' => 'Failed to pay within 48 hours.']);

                // 2. Add strike to winner
                $strike = UserAuctionStrike::firstOrCreate(['user_id' => $winnerId]);
                $strike->increment('strike_count');

                if ($strike->strike_count >= 3) {
                    $strike->update([
                        'is_suspended' => true,
                        'suspended_until' => now()->addDays(30), // Suspend for 30 days
                    ]);
                }

                // 3. Offer to fallback user or cancel
                $fallbackId = $auction->fallback_user_id;

                $fallbackBid = $fallbackId
                    ? $auction->bids()->where('user_id', $fallbackId)->orderByDesc('amount_php')->first()
                    : null;

                $fallbackHasApprovedDeposit = $fallbackId
                    && BidDeposit::where('auction_id', $auction->id)
                        ->where('user_id', $fallbackId)
                        ->where('status', 'approved')
                        ->exists();

                // Eligible only with an approved deposit AND a bid that still meets reserve.
                if ($fallbackHasApprovedDeposit && $fallbackBid && ! $auction->reserveNotMetBy($fallbackBid->amount_php)) {
                    // Next-highest bidder below the new winner (excluding the defaulter).
                    $nextFallbackBid = $auction->bids()
                        ->whereNotIn('user_id', [$winnerId, $fallbackId])
                        ->orderByDesc('amount_php')
                        ->first();

                    $auction->update([
                        'winner_user_id' => $fallbackId,
                        'fallback_user_id' => $nextFallbackBid ? $nextFallbackBid->user_id : null,
                        'current_bid_php' => $fallbackBid->amount_php,
                        'payment_deadline' => now()->addHours(48),
                    ]);

                    $auction->refresh();
                    $auction->winner?->notify(new AuctionWinnerNotification($auction));

                    $this->info("Winner #{$winnerId} failed to pay for Auction #{$auction->id}. Reassigned to Fallback #{$fallbackId}.");

                    return; // New deadline is in the future — no reprocessing until it lapses.
                }

                // No eligible fallback: cancel and release any deposit still held.
                if ($fallbackId) {
                    BidDeposit::where('auction_id', $auction->id)
                        ->where('user_id', $fallbackId)
                        ->where('status', 'approved')
                        ->update(['status' => 'refunded']);
                }

                $auction->update([
                    'status' => Auction::STATUS_CANCELLED,
                    'payment_deadline' => null, // Deadline processed
                ]);

                $this->warn("Winner #{$winnerId} failed to pay for Auction #{$auction->id}. No eligible fallback — auction cancelled.");
            });
        }
    }
}
