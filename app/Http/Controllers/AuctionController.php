<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Support\Facades\Gate;

class AuctionController extends Controller
{
    /**
     * List all auctions.
     */
    public function index()
    {
        $auctions = Auction::with('unit')->latest()->paginate(12);

        return view('auctions.index', compact('auctions'));
    }

    /**
     * Show a specific auction.
     */
    public function show(Auction $auction)
    {
        $auction->load(['unit', 'bids.user', 'deposits']);

        return view('auctions.show', compact('auction'));
    }

    /**
     * Manually activate an auction if min bidders met.
     */
    public function activate(Auction $auction)
    {
        Gate::authorize('access-admin');

        $approvedDepositsCount = $auction->deposits()->where('status', 'approved')->count();

        if ($approvedDepositsCount < $auction->min_bidders) {
            return back()->with('error', "Minimum {$auction->min_bidders} approved bidders required. Currently have {$approvedDepositsCount}.");
        }

        $auction->update(['status' => 'active']);

        return back()->with('status', 'Auction is now ACTIVE and open for bidding.');
    }
}
