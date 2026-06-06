<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\BidDeposit;
use App\Models\UserAuctionStrike;
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
                $fallbackBid = $auction->bids()->orderByDesc('amount_php')->skip(1)->first();

                if ($winningBid) {
                    $auction->update([
                        'status' => 'completed',
                        'winner_user_id' => $winningBid->user_id,
                        'fallback_user_id' => $fallbackBid ? $fallbackBid->user_id : null,
                        'payment_deadline' => now()->addHours(48),
                    ]);

                    // Set winner deposit to 'applied' or similar if needed,
                    // but usually it stays 'approved' until payment.

                    // Mark losers for refund
                    BidDeposit::where('auction_id', $auction->id)
                        ->where('user_id', '!=', $winningBid->user_id)
                        ->where('status', 'approved')
                        ->update(['status' => 'refunded']);
                } else {
                    $auction->update(['status' => 'cancelled']); // No bids
                }
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
                if ($auction->fallback_user_id) {
                    // In a real app, you'd notify the fallback user and set a new deadline.
                    // For this scaffold, we'll just log it.
                    $this->warn("Winner #{$winnerId} failed to pay for Auction #{$auction->id}. Offering to Fallback #{$auction->fallback_user_id}.");
                }

                $auction->update(['payment_deadline' => null]); // Deadline processed
            });
        }
    }
}
