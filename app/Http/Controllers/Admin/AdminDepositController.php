<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BidDeposit;
use Illuminate\Http\Request;

class AdminDepositController extends Controller
{
    /**
     * List all pending deposits.
     */
    public function index()
    {
        $deposits = BidDeposit::with(['user', 'auction.unit'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.deposits.index', compact('deposits'));
    }

    /**
     * Approve a deposit.
     */
    public function approve(BidDeposit $deposit)
    {
        $deposit->update(['status' => 'approved']);
        return back()->with('status', 'Deposit approved. User can now bid.');
    }

    /**
     * Reject a deposit with a note.
     */
    public function reject(Request $request, BidDeposit $deposit)
    {
        $request->validate(['admin_note' => 'required|string']);
        
        $deposit->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note
        ]);

        return back()->with('status', 'Deposit rejected.');
    }
}
