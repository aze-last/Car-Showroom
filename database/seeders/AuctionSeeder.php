<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class AuctionSeeder extends Seeder
{
    public function run(): void
    {
        $units = Unit::all();

        if ($units->isEmpty()) {
            return;
        }

        // Live Auction
        Auction::create([
            'unit_id' => $units->first()->id,
            'lot_number' => '042',
            'is_featured' => true,
            'start_at' => now()->subDay(),
            'end_at' => now()->addHours(2),
            'reserve_price_php' => 4500000,
            'starting_bid_php' => 3000000,
            'current_bid_php' => 4250000,
            'status' => 'live',
        ]);

        // Ending Soon
        Auction::create([
            'unit_id' => $units->get(1)->id ?? $units->first()->id,
            'lot_number' => '045',
            'start_at' => now()->subDay(),
            'end_at' => now()->addMinutes(15),
            'reserve_price_php' => 300000,
            'starting_bid_php' => 200000,
            'current_bid_php' => 315000,
            'status' => 'live',
        ]);

        // Scheduled
        Auction::create([
            'unit_id' => $units->get(2)->id ?? $units->first()->id,
            'lot_number' => '050',
            'start_at' => now()->addDays(2),
            'end_at' => now()->addDays(3),
            'reserve_price_php' => 1500000,
            'starting_bid_php' => 1200000,
            'current_bid_php' => 0,
            'status' => 'scheduled',
        ]);
    }
}
