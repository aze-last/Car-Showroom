<?php

use App\Models\Auction;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function createLiveAuction(): Auction
{
    $unit = Unit::factory()->create();

    return Auction::query()->create([
        'unit_id' => $unit->id,
        'lot_number' => 'LOT-'.fake()->unique()->numerify('###'),
        'start_at' => now()->subHour(),
        'end_at' => now()->addHour(),
        'reserve_price_php' => 1_000_000,
        'starting_bid_php' => 500_000,
        'current_bid_php' => 500_000,
        'status' => 'live',
    ]);
}

test('unverified collectors are redirected when saving a vehicle', function () {
    $user = User::factory()->unverified()->create();
    $unit = Unit::factory()->create();

    $this->actingAs($user);

    Livewire::test(\App\Livewire\PublicShowroom::class)
        ->call('toggleSave', $unit->id)
        ->assertRedirect(route('verification.notice'));
});

test('verified collectors can save vehicles without google', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $this->actingAs($user);

    Livewire::test(\App\Livewire\PublicShowroom::class)
        ->call('toggleSave', $unit->id);

    expect($user->savedUnits()->where('unit_id', $unit->id)->exists())->toBeTrue();
});

test('unverified collectors cannot send chat inquiries', function () {
    $user = User::factory()->unverified()->create();
    $unit = Unit::factory()->create();

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Public\ChatInquiry::class, ['unit' => $unit])
        ->set('body', 'Is this still available?')
        ->call('sendMessage')
        ->assertRedirect(route('verification.notice'));
});

test('password-only collectors are redirected to google before joining an auction', function () {
    $user = User::factory()->create();
    $auction = createLiveAuction();

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Public\AuctionHall::class)
        ->call('openJoinModal', $auction->id)
        ->assertRedirect(route('auth.google.redirect'));
});

test('google-linked collectors can open the auction join modal', function () {
    $user = User::factory()->withGoogle()->create();
    $auction = createLiveAuction();

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Public\AuctionHall::class)
        ->call('openJoinModal', $auction->id)
        ->assertSet('selectedAuction.id', $auction->id);
});

test('password-only collectors are redirected to google before placing a bid', function () {
    $user = User::factory()->create();
    $auction = createLiveAuction();

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Public\AuctionRoom::class, ['auction' => $auction])
        ->set('bidAmount', 600_000)
        ->call('placeBid')
        ->assertRedirect(route('auth.google.redirect'));
});

test('google-linked collectors can place bids', function () {
    $user = User::factory()->withGoogle()->create();
    $auction = createLiveAuction();

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Public\AuctionRoom::class, ['auction' => $auction])
        ->set('bidAmount', 600_000)
        ->call('placeBid')
        ->assertSet('message', 'Bid placed successfully!');

    expect($auction->fresh()->current_bid_php)->toBe(600_000);
});

test('garage requires a verified email', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('garage'))
        ->assertRedirect(route('verification.notice'));
});
