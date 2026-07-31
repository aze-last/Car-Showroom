<?php

use App\Models\Auction;
use App\Models\Category;
use App\Models\Unit;
use App\Models\UnitStatusLog;
use App\Models\User;
use App\Services\AuctionReadinessService;
use Livewire\Livewire;

test('daysInCurrentListing uses most recent AVAILABLE status log and handles relisting after sold scenario', function () {
    // Scenario 1: Unit created 100 days ago with no status log -> falls back to created_at
    $unit1 = Unit::factory()->create([
        'created_at' => now()->subDays(100),
    ]);
    expect($unit1->daysInCurrentListing())->toBe(100);

    // Scenario 2: Unit created 120 days ago, marked SOLD 50 days ago, relisted AVAILABLE 20 days ago
    $unit2 = Unit::factory()->create([
        'created_at' => now()->subDays(120),
        'status' => Unit::STATUS_AVAILABLE,
    ]);

    UnitStatusLog::factory()->create([
        'unit_id' => $unit2->id,
        'from_status' => Unit::STATUS_AVAILABLE,
        'to_status' => Unit::STATUS_SOLD,
        'created_at' => now()->subDays(50),
    ]);

    UnitStatusLog::factory()->create([
        'unit_id' => $unit2->id,
        'from_status' => Unit::STATUS_SOLD,
        'to_status' => Unit::STATUS_AVAILABLE,
        'created_at' => now()->subDays(20),
    ]);

    // Should use the most recent AVAILABLE log (20 days ago), not created_at (120 days)
    expect($unit2->daysInCurrentListing())->toBe(20);
});

test('category benchmark requires minimum 3 completed sales and calculates accurate average', function () {
    $category = Category::factory()->create();
    $service = app(AuctionReadinessService::class);

    // 0 completed sales -> null
    expect($service->getCategoryBenchmark($category->id))->toBeNull();

    // Create 2 completed sales (AVAILABLE -> SOLD)
    for ($i = 1; $i <= 2; $i++) {
        $unit = Unit::factory()->create(['category_id' => $category->id]);
        UnitStatusLog::factory()->create([
            'unit_id' => $unit->id,
            'to_status' => Unit::STATUS_AVAILABLE,
            'created_at' => now()->subDays(40),
        ]);
        UnitStatusLog::factory()->create([
            'unit_id' => $unit->id,
            'to_status' => Unit::STATUS_SOLD,
            'created_at' => now()->subDays(20), // 20 days to sell
        ]);
    }

    // 2 completed sales (< 3 threshold) -> still null
    expect($service->getCategoryBenchmark($category->id))->toBeNull();

    // Create 3rd completed sale (30 days to sell)
    $unit3 = Unit::factory()->create(['category_id' => $category->id]);
    UnitStatusLog::factory()->create([
        'unit_id' => $unit3->id,
        'to_status' => Unit::STATUS_AVAILABLE,
        'created_at' => now()->subDays(60),
    ]);
    UnitStatusLog::factory()->create([
        'unit_id' => $unit3->id,
        'to_status' => Unit::STATUS_SOLD,
        'created_at' => now()->subDays(30), // 30 days to sell
    ]);

    // Now 3 completed sales: durations 20, 20, 30 -> average = 23.3 days
    expect($service->getCategoryBenchmark($category->id))->toBe(23.3);
});

test('reserve and starting bid suggestions match tiered sitting day thresholds', function () {
    $service = app(AuctionReadinessService::class);
    $price = 1_000_000;

    // 20 Days (0-30 Tier: Fresh, not candidate)
    $unit20 = Unit::factory()->create([
        'price_php' => $price,
        'created_at' => now()->subDays(20),
    ]);
    $eval20 = $service->evaluate($unit20);
    expect($eval20['is_candidate'])->toBeFalse();
    expect($eval20['recommendation_status'])->toBe('still_fresh');
    expect($eval20['suggested_reserve_php'])->toBeNull();

    // 45 Days (31-60 Tier: 90% reserve = 900k, starting bid 65% of reserve = 585k)
    $unit45 = Unit::factory()->create([
        'price_php' => $price,
        'created_at' => now()->subDays(45),
    ]);
    $eval45 = $service->evaluate($unit45);
    expect($eval45['is_candidate'])->toBeTrue();
    expect($eval45['suggested_reserve_php'])->toBe(900_000);
    expect($eval45['suggested_starting_bid_php'])->toBe(585_000);

    // 75 Days (61-90 Tier: 80% reserve = 800k, starting bid 60% of reserve = 480k)
    $unit75 = Unit::factory()->create([
        'price_php' => $price,
        'created_at' => now()->subDays(75),
    ]);
    $eval75 = $service->evaluate($unit75);
    expect($eval75['is_candidate'])->toBeTrue();
    expect($eval75['suggested_reserve_php'])->toBe(800_000);
    expect($eval75['suggested_starting_bid_php'])->toBe(480_000);

    // 120 Days (90+ Tier: 70% reserve = 700k, starting bid 55% of reserve = 385k)
    $unit120 = Unit::factory()->create([
        'price_php' => $price,
        'created_at' => now()->subDays(120),
    ]);
    $eval120 = $service->evaluate($unit120);
    expect($eval120['is_candidate'])->toBeTrue();
    expect($eval120['suggested_reserve_php'])->toBe(700_000);
    expect($eval120['suggested_starting_bid_php'])->toBe(385_000);
});

test('engagement soft adjustment increases reserve ratio by 5 percent when high views or favorites exist', function () {
    $service = app(AuctionReadinessService::class);
    $price = 1_000_000;

    $unit = Unit::factory()->create([
        'price_php' => $price,
        'created_at' => now()->subDays(45), // 31-60 tier base reserve 90%
    ]);

    // Attach 2 favorites
    $users = User::factory()->count(2)->create();
    $unit->savedByUsers()->attach($users->pluck('id'));

    $eval = $service->evaluate($unit);
    expect($eval['engagement_adjusted'])->toBeTrue();
    // 90% base + 5% boost = 95% reserve (950,000)
    expect($eval['suggested_reserve_php'])->toBe(950_000);
});

test('auction creation form pre-fills suggested values when available and admin can override them', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $unit = Unit::factory()->create([
        'name' => 'Stagnant Luxury SUV',
        'price_php' => 2_000_000,
        'created_at' => now()->subDays(75), // 61-90 tier: 80% reserve = 1,600,000; starting bid 60% = 960,000
    ]);

    // Mount form with unit_id query parameter
    Livewire::withQueryParams(['unit_id' => $unit->id])
        ->actingAs($admin)
        ->test('admin-auction-form')
        ->assertSet('unit_id', $unit->id)
        ->assertSet('reserve_price_php', 1_600_000)
        ->assertSet('starting_bid_php', 960_000)
        // Admin overrides suggested values with custom inputs
        ->set('lot_number', 'LOT-999')
        ->set('reserve_price_php', 1_500_000)
        ->set('starting_bid_php', 900_000)
        ->set('start_at', now()->addDay()->format('Y-m-d\TH:i'))
        ->set('end_at', now()->addDay()->addHours(4)->format('Y-m-d\TH:i'))
        ->call('save')
        ->assertHasNoErrors();

    // Assert auction was created with the admin's overridden values
    $auction = Auction::query()->where('lot_number', 'LOT-999')->first();
    expect($auction)->not->toBeNull();
    expect($auction->reserve_price_php)->toBe(1_500_000);
    expect($auction->starting_bid_php)->toBe(900_000);
});
