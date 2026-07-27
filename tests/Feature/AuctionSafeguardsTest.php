<?php

use App\Models\Auction;
use App\Models\Bid;
use App\Models\BidDeposit;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserAuctionStrike;
use App\Notifications\AuctionWinnerNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function makeLiveAuction(array $overrides = []): Auction
{
    $unit = Unit::factory()->create();

    return Auction::query()->create(array_merge([
        'unit_id' => $unit->id,
        'lot_number' => 'LOT-'.fake()->unique()->numerify('####'),
        'start_at' => now()->subHour(),
        'end_at' => now()->addHour(),
        'reserve_price_php' => 1_000_000,
        'starting_bid_php' => 500_000,
        'current_bid_php' => 500_000,
        'status' => 'live',
    ], $overrides));
}

function approveDeposit(User $user, Auction $auction, string $status = 'approved'): BidDeposit
{
    return BidDeposit::query()->create([
        'user_id' => $user->id,
        'auction_id' => $auction->id,
        'amount' => 50_000,
        'proof_image' => 'deposits/test.jpg',
        'status' => $status,
    ]);
}

// ─── Bug 1: deposit gate ────────────────────────────────────────────────

test('a bid without any deposit is rejected', function () {
    $user = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction();

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Public\AuctionRoom::class, ['auction' => $auction])
        ->set('bidAmount', 600_000)
        ->call('placeBid')
        ->assertHasErrors('bidAmount');

    expect($auction->fresh()->current_bid_php)->toBe(500_000)
        ->and(Bid::query()->count())->toBe(0);
});

test('a bid with a pending (not approved) deposit is rejected', function () {
    $user = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction();
    approveDeposit($user, $auction, 'pending');

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Public\AuctionRoom::class, ['auction' => $auction])
        ->set('bidAmount', 600_000)
        ->call('placeBid')
        ->assertHasErrors('bidAmount');

    expect(Bid::query()->count())->toBe(0);
});

test('a bid with an approved deposit succeeds', function () {
    $user = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction();
    approveDeposit($user, $auction);

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Public\AuctionRoom::class, ['auction' => $auction])
        ->set('bidAmount', 600_000)
        ->call('placeBid')
        ->assertHasNoErrors()
        ->assertSet('message', 'Bid placed successfully!');

    expect($auction->fresh()->current_bid_php)->toBe(600_000);
});

// ─── Bug 1: suspension gate ─────────────────────────────────────────────

test('a suspended user cannot bid even with an approved deposit', function () {
    $user = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction();
    approveDeposit($user, $auction);

    UserAuctionStrike::query()->create([
        'user_id' => $user->id,
        'strike_count' => 3,
        'is_suspended' => true,
        'suspended_until' => now()->addDays(10),
    ]);

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Public\AuctionRoom::class, ['auction' => $auction])
        ->set('bidAmount', 600_000)
        ->call('placeBid')
        ->assertHasErrors('bidAmount');

    expect(Bid::query()->count())->toBe(0);
});

test('a user whose suspension has lapsed can bid again', function () {
    $user = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction();
    approveDeposit($user, $auction);

    UserAuctionStrike::query()->create([
        'user_id' => $user->id,
        'strike_count' => 3,
        'is_suspended' => true,
        'suspended_until' => now()->subDay(),
    ]);

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Public\AuctionRoom::class, ['auction' => $auction])
        ->set('bidAmount', 600_000)
        ->call('placeBid')
        ->assertHasNoErrors()
        ->assertSet('message', 'Bid placed successfully!');

    expect($auction->fresh()->current_bid_php)->toBe(600_000);
});

// ─── Bug 1: max bid jump ────────────────────────────────────────────────

test('a bid more than 50 percent above the current price is rejected', function () {
    $user = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction();
    approveDeposit($user, $auction);

    $this->actingAs($user);

    // Current is 500k → cap is 750k.
    Livewire::test(\App\Livewire\Public\AuctionRoom::class, ['auction' => $auction])
        ->set('bidAmount', 800_000)
        ->call('placeBid')
        ->assertHasErrors(['bidAmount' => 'max']);

    expect(Bid::query()->count())->toBe(0);
});

test('a bid below the flat 10k minimum increment is rejected', function () {
    $user = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction();
    approveDeposit($user, $auction);

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Public\AuctionRoom::class, ['auction' => $auction])
        ->set('bidAmount', 505_000)
        ->call('placeBid')
        ->assertHasErrors(['bidAmount' => 'min']);
});

// ─── Bug 2: reserve enforcement ─────────────────────────────────────────

test('ending an auction below reserve does not declare a winner', function () {
    $bidder = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction([
        'end_at' => now()->subMinute(),
        'reserve_price_php' => 1_000_000,
        'current_bid_php' => 800_000,
    ]);
    approveDeposit($bidder, $auction);
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $bidder->id, 'amount_php' => 800_000]);

    $this->artisan('auction:check-deadlines');

    $auction->refresh();
    expect($auction->status)->toBe(Auction::STATUS_RESERVE_NOT_MET)
        ->and($auction->winner_user_id)->toBeNull()
        ->and($auction->payment_deadline)->toBeNull()
        ->and(BidDeposit::query()->where('auction_id', $auction->id)->where('user_id', $bidder->id)->value('status'))->toBe('refunded');
});

test('ending an auction at or above reserve declares the winner as before', function () {
    $winner = User::factory()->withGoogle()->create();
    $loser = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction([
        'end_at' => now()->subMinute(),
        'reserve_price_php' => 1_000_000,
        'current_bid_php' => 1_200_000,
    ]);
    approveDeposit($winner, $auction);
    approveDeposit($loser, $auction);
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $loser->id, 'amount_php' => 1_100_000]);
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $winner->id, 'amount_php' => 1_200_000]);

    $this->artisan('auction:check-deadlines');

    $auction->refresh();
    expect($auction->status)->toBe(Auction::STATUS_COMPLETED)
        ->and($auction->winner_user_id)->toBe($winner->id)
        ->and($auction->fallback_user_id)->toBe($loser->id)
        ->and($auction->payment_deadline)->not->toBeNull();
});

test('ending an auction with no reserve declares the highest bidder', function () {
    $winner = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction([
        'end_at' => now()->subMinute(),
        'reserve_price_php' => 0,
        'current_bid_php' => 600_000,
    ]);
    approveDeposit($winner, $auction);
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $winner->id, 'amount_php' => 600_000]);

    $this->artisan('auction:check-deadlines');

    expect($auction->fresh()->status)->toBe(Auction::STATUS_COMPLETED)
        ->and($auction->fresh()->winner_user_id)->toBe($winner->id);
});

test('the fallback bidder deposit is not refunded when a winner is declared', function () {
    $winner = User::factory()->withGoogle()->create();
    $fallback = User::factory()->withGoogle()->create();
    $loser = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction([
        'end_at' => now()->subMinute(),
        'reserve_price_php' => 1_000_000,
        'current_bid_php' => 1_200_000,
    ]);
    approveDeposit($winner, $auction);
    approveDeposit($fallback, $auction);
    approveDeposit($loser, $auction);
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $loser->id, 'amount_php' => 1_050_000]);
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $fallback->id, 'amount_php' => 1_100_000]);
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $winner->id, 'amount_php' => 1_200_000]);

    $this->artisan('auction:check-deadlines');

    $statuses = BidDeposit::query()->where('auction_id', $auction->id)->pluck('status', 'user_id');
    expect($statuses[$winner->id])->toBe('approved')
        ->and($statuses[$fallback->id])->toBe('approved')
        ->and($statuses[$loser->id])->toBe('refunded');
});

// ─── Bug 3: fallback reassignment ───────────────────────────────────────

test('a missed payment deadline reassigns the win to the fallback bidder', function () {
    Notification::fake();

    $winner = User::factory()->withGoogle()->create();
    $fallback = User::factory()->withGoogle()->create();
    $third = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction([
        'status' => Auction::STATUS_COMPLETED,
        'end_at' => now()->subDays(3),
        'reserve_price_php' => 1_000_000,
        'current_bid_php' => 1_200_000,
        'winner_user_id' => $winner->id,
        'fallback_user_id' => $fallback->id,
        'payment_deadline' => now()->subHour(),
    ]);
    approveDeposit($winner, $auction);
    approveDeposit($fallback, $auction);
    approveDeposit($third, $auction, 'refunded');
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $third->id, 'amount_php' => 1_050_000]);
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $fallback->id, 'amount_php' => 1_100_000]);
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $winner->id, 'amount_php' => 1_200_000]);

    $this->artisan('auction:check-deadlines');

    $auction->refresh();
    expect($auction->status)->toBe(Auction::STATUS_COMPLETED)
        ->and($auction->winner_user_id)->toBe($fallback->id)
        ->and($auction->fallback_user_id)->toBe($third->id)
        ->and($auction->current_bid_php)->toBe(1_100_000)
        ->and($auction->payment_deadline->isFuture())->toBeTrue()
        ->and(BidDeposit::query()->where('auction_id', $auction->id)->where('user_id', $winner->id)->value('status'))->toBe('forfeited')
        ->and(UserAuctionStrike::query()->where('user_id', $winner->id)->value('strike_count'))->toBe(1);

    Notification::assertSentTo($fallback, AuctionWinnerNotification::class);
});

test('the reassigned fallback winner is not reprocessed until the new deadline lapses', function () {
    Notification::fake();

    $winner = User::factory()->withGoogle()->create();
    $fallback = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction([
        'status' => Auction::STATUS_COMPLETED,
        'end_at' => now()->subDays(3),
        'reserve_price_php' => 1_000_000,
        'current_bid_php' => 1_200_000,
        'winner_user_id' => $winner->id,
        'fallback_user_id' => $fallback->id,
        'payment_deadline' => now()->subHour(),
    ]);
    approveDeposit($winner, $auction);
    approveDeposit($fallback, $auction);
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $fallback->id, 'amount_php' => 1_100_000]);
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $winner->id, 'amount_php' => 1_200_000]);

    // Run twice back-to-back: the second run must not strike the new winner.
    $this->artisan('auction:check-deadlines');
    $this->artisan('auction:check-deadlines');

    $auction->refresh();
    expect($auction->winner_user_id)->toBe($fallback->id)
        ->and(BidDeposit::query()->where('auction_id', $auction->id)->where('user_id', $fallback->id)->value('status'))->toBe('approved')
        ->and(UserAuctionStrike::query()->where('user_id', $fallback->id)->exists())->toBeFalse();
});

test('a missed payment deadline with no eligible fallback cancels the auction', function () {
    Notification::fake();

    $winner = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction([
        'status' => Auction::STATUS_COMPLETED,
        'end_at' => now()->subDays(3),
        'reserve_price_php' => 1_000_000,
        'current_bid_php' => 1_200_000,
        'winner_user_id' => $winner->id,
        'fallback_user_id' => null,
        'payment_deadline' => now()->subHour(),
    ]);
    approveDeposit($winner, $auction);
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $winner->id, 'amount_php' => 1_200_000]);

    $this->artisan('auction:check-deadlines');

    $auction->refresh();
    expect($auction->status)->toBe(Auction::STATUS_CANCELLED)
        ->and($auction->payment_deadline)->toBeNull()
        ->and(BidDeposit::query()->where('auction_id', $auction->id)->where('user_id', $winner->id)->value('status'))->toBe('forfeited');

    Notification::assertNothingSent();
});

test('a fallback without an approved deposit is skipped and the auction is cancelled', function () {
    Notification::fake();

    $winner = User::factory()->withGoogle()->create();
    $fallback = User::factory()->withGoogle()->create();
    $auction = makeLiveAuction([
        'status' => Auction::STATUS_COMPLETED,
        'end_at' => now()->subDays(3),
        'reserve_price_php' => 1_000_000,
        'current_bid_php' => 1_200_000,
        'winner_user_id' => $winner->id,
        'fallback_user_id' => $fallback->id,
        'payment_deadline' => now()->subHour(),
    ]);
    approveDeposit($winner, $auction);
    approveDeposit($fallback, $auction, 'refunded'); // Deposit already released.
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $fallback->id, 'amount_php' => 1_100_000]);
    Bid::query()->create(['auction_id' => $auction->id, 'user_id' => $winner->id, 'amount_php' => 1_200_000]);

    $this->artisan('auction:check-deadlines');

    $auction->refresh();
    expect($auction->status)->toBe(Auction::STATUS_CANCELLED)
        ->and($auction->winner_user_id)->toBe($winner->id)
        ->and($auction->payment_deadline)->toBeNull();

    Notification::assertNothingSent();
});

// ─── Scheduler registration ─────────────────────────────────────────────

test('the auction deadline command is registered in the scheduler', function () {
    $events = collect(app(\Illuminate\Console\Scheduling\Schedule::class)->events());

    expect($events->contains(fn ($event) => str_contains($event->command ?? '', 'auction:check-deadlines')))->toBeTrue();
});
