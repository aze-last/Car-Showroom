<?php

use App\Models\Auction;
use App\Models\Bid;
use App\Models\BidDeposit;
use App\Models\Category;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function liveAuctionForErrorTests(): Auction
{
    $unit = Unit::factory()->create();

    return Auction::query()->create([
        'unit_id' => $unit->id,
        'lot_number' => 'ERR-'.fake()->unique()->numerify('####'),
        'start_at' => now()->subHour(),
        'end_at' => now()->addHour(),
        'reserve_price_php' => 1_000_000,
        'starting_bid_php' => 500_000,
        'current_bid_php' => 500_000,
        'status' => 'live',
    ]);
}

// ─── Bidding: DB failure during placeBid ────────────────────────────────

test('a failed bid write shows a graceful error and is logged instead of a 500', function () {
    Log::spy();

    $user = User::factory()->withGoogle()->create();
    $auction = liveAuctionForErrorTests();
    BidDeposit::query()->create([
        'user_id' => $user->id,
        'auction_id' => $auction->id,
        'amount' => 50_000,
        'proof_image' => 'deposits/test.jpg',
        'status' => 'approved',
    ]);

    // Force the underlying write to fail (simulates a DB error mid-transaction).
    Bid::creating(function () {
        throw new RuntimeException('Simulated database failure');
    });

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Public\AuctionRoom::class, ['auction' => $auction])
        ->set('bidAmount', 600_000)
        ->call('placeBid')
        ->assertOk()
        ->assertDispatched('toast', message: 'Could not place your bid. Please try again.', type: 'error');

    expect(Bid::query()->count())->toBe(0)
        ->and($auction->fresh()->current_bid_php)->toBe(500_000);

    Log::shouldHaveReceived('error')->once();
});

// ─── File upload: deposit submission failure ────────────────────────────

test('a failed deposit submission shows a graceful error instead of a 500', function () {
    Log::spy();
    Storage::fake('public');

    $user = User::factory()->withGoogle()->create();
    $auction = liveAuctionForErrorTests();

    BidDeposit::creating(function () {
        throw new RuntimeException('Simulated storage/database failure');
    });

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Public\AuctionHall::class)
        ->call('openJoinModal', $auction->id)
        ->set('proof_image', UploadedFile::fake()->image('proof.jpg'))
        ->set('deposit_amount', 5000)
        ->call('submitDeposit')
        ->assertOk()
        ->assertDispatched('toast', message: 'Could not submit your deposit. Please try again.', type: 'error');

    expect(BidDeposit::query()->count())->toBe(0);

    Log::shouldHaveReceived('error')->once();
});

// ─── Admin CRUD: category creation failure ──────────────────────────────

test('a failed admin category create shows a graceful error and is logged', function () {
    Log::spy();

    $admin = User::factory()->admin()->create();

    Category::creating(function () {
        throw new RuntimeException('Simulated database failure');
    });

    $this->actingAs($admin);

    Livewire::test(\App\Livewire\AdminCategories::class)
        ->set('name', 'Hypercars')
        ->call('create')
        ->assertOk()
        ->assertDispatched('toast', message: 'Could not create the category. Please try again.', type: 'error');

    expect(Category::query()->where('name', 'Hypercars')->exists())->toBeFalse();

    Log::shouldHaveReceived('error')->once();
});

// ─── Validation errors are NOT swallowed by the safety net ──────────────

test('validation errors still surface as inline field errors, not generic flashes', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test(\App\Livewire\AdminCategories::class)
        ->set('name', '')
        ->call('create')
        ->assertHasErrors(['name' => 'required']);

    expect(session('error'))->toBeNull();
});
