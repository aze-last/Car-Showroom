<?php

use App\Models\Auction;
use App\Models\BidDeposit;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function makeAuction(array $attributes = []): Auction
{
    return Auction::create(array_merge([
        'unit_id' => Unit::factory()->create()->id,
        'lot_number' => 'LOT-'.fake()->unique()->numerify('####'),
        'start_at' => now()->subHour(),
        'end_at' => now()->addHour(),
        'reserve_price_php' => 1000000,
        'starting_bid_php' => 500000,
        'status' => 'scheduled',
    ], $attributes));
}

test('guests cannot access the admin dashboard', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

test('collectors cannot access the admin dashboard', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('admin.dashboard'))->assertForbidden();
});

test('admins can view the dashboard with priority action metrics', function () {
    $admin = User::factory()->admin()->create();

    Unit::factory()->count(3)->create(['status' => Unit::STATUS_AVAILABLE]);
    Unit::factory()->create(['status' => Unit::STATUS_SOLD]);

    $this->actingAs($admin);

    Livewire::test(\App\Livewire\AdminDashboard::class)
        ->assertViewHas('totalUnits', 4)
        ->assertViewHas('availableUnits', 3)
        ->assertViewHas('soldUnits', 1)
        ->assertViewHas('availablePercentage', 75)
        ->assertSee('Priority Actions')
        ->assertSee('Deposit Queue')
        ->assertSee('Fleet Health');
});

test('dashboard counts pending deposits and shows the alert banner', function () {
    $admin = User::factory()->admin()->create();
    $auction = makeAuction();

    BidDeposit::create([
        'user_id' => User::factory()->create()->id,
        'auction_id' => $auction->id,
        'amount' => 50000,
        'proof_image' => 'deposits/proof-1.jpg',
        'status' => 'pending',
    ]);
    BidDeposit::create([
        'user_id' => User::factory()->create()->id,
        'auction_id' => $auction->id,
        'amount' => 50000,
        'proof_image' => 'deposits/proof-2.jpg',
        'status' => 'approved',
    ]);

    $this->actingAs($admin);

    Livewire::test(\App\Livewire\AdminDashboard::class)
        ->assertViewHas('pendingDepositsCount', 1)
        ->assertViewHas('resolvedDepositsCount', 1)
        ->assertSee('awaiting verification');
});

test('dashboard hides the alert banner when no deposits are pending', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test(\App\Livewire\AdminDashboard::class)
        ->assertViewHas('pendingDepositsCount', 0)
        ->assertDontSee('awaiting verification')
        ->assertSee('All Systems Nominal');
});

test('dashboard counts live auctions and surfaces the next closing lot', function () {
    $admin = User::factory()->admin()->create();

    makeAuction(['status' => 'live', 'end_at' => now()->addHours(2)]);
    makeAuction(['status' => 'live', 'end_at' => now()->addMinutes(30)]);
    makeAuction(['status' => 'scheduled']);

    $this->actingAs($admin);

    Livewire::test(\App\Livewire\AdminDashboard::class)
        ->assertViewHas('activeAuctionsCount', 2)
        ->assertViewHas('nextAuctionEndingAt', fn ($endAt) => $endAt !== null && $endAt->lessThan(now()->addHour()))
        ->assertSee('Live Now');
});
