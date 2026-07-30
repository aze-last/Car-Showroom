<?php

use App\Livewire\AdminDashboard;
use App\Models\Unit;
use App\Models\UnitView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ─── Dashboard render: zero-data and populated scenarios ─────────────────

test('dashboard renders with an empty database showing chart empty states', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test(AdminDashboard::class)
        ->assertOk()
        ->assertViewHas('velocityHasData', false)
        ->assertViewHas('viewsHasData', false)
        ->assertSee('Not enough sales data yet')
        ->assertSee('No views recorded yet')
        ->assertSee('No unit views in the last 7 days.')
        ->assertSee('No units have been favorited yet.');
});

test('dashboard renders charts when sales and view data exist', function () {
    $admin = User::factory()->admin()->create();

    Unit::factory()->sold()->create();
    UnitView::factory()->create();

    $this->actingAs($admin);

    Livewire::test(AdminDashboard::class)
        ->assertOk()
        ->assertViewHas('velocityHasData', true)
        ->assertViewHas('viewsHasData', true)
        ->assertViewHas('velocityChart', fn (array $chart) => count($chart['labels']) === 6 && array_sum($chart['data']) === 1)
        ->assertViewHas('viewsChart', fn (array $chart) => count($chart['labels']) === 30 && array_sum($chart['data']) === 1)
        ->assertDontSee('Not enough sales data yet')
        ->assertDontSee('No views recorded yet');
});

// ─── Leaderboard widgets ─────────────────────────────────────────────────

test('most viewed this week widget ranks the top 5 units correctly', function () {
    $admin = User::factory()->admin()->create();

    $units = Unit::factory()->count(7)->create();
    // Views this week: unit index => count (indexes 5 and 6 get none this week)
    foreach ([0 => 3, 1 => 10, 2 => 5, 3 => 1, 4 => 7] as $index => $count) {
        UnitView::factory()->count($count)->create(['unit_id' => $units[$index]->id]);
    }
    // Views outside the 7-day window must not count.
    UnitView::factory()->count(20)->create([
        'unit_id' => $units[5]->id,
        'viewed_at' => now()->subDays(10),
    ]);

    $this->actingAs($admin);

    Livewire::test(AdminDashboard::class)
        ->assertViewHas('mostViewedThisWeek', function ($leaderboard) use ($units) {
            return $leaderboard->pluck('id')->all() === [
                $units[1]->id, // 10
                $units[4]->id, // 7
                $units[2]->id, // 5
                $units[0]->id, // 3
                $units[3]->id, // 1
            ];
        });
});

test('most favorited widget ranks the top 5 units correctly', function () {
    $admin = User::factory()->admin()->create();

    $units = Unit::factory()->count(3)->create();
    $collectors = User::factory()->count(4)->create();

    // unit 0: 4 saves, unit 1: 2 saves, unit 2: 0 saves
    $collectors->each(fn (User $u) => $u->savedUnits()->attach($units[0]->id));
    $collectors->take(2)->each(fn (User $u) => $u->savedUnits()->attach($units[1]->id));

    $this->actingAs($admin);

    Livewire::test(AdminDashboard::class)
        ->assertViewHas('mostFavoritedUnits', function ($leaderboard) use ($units) {
            return $leaderboard->pluck('id')->all() === [$units[0]->id, $units[1]->id]
                && $leaderboard->first()->saved_by_users_count === 4;
        });
});

test('weekly funnel counts views favorites and sold units', function () {
    $admin = User::factory()->admin()->create();

    $unit = Unit::factory()->create();
    UnitView::factory()->count(3)->create(['unit_id' => $unit->id]);
    User::factory()->create()->savedUnits()->attach($unit->id);
    Unit::factory()->sold()->create();

    $this->actingAs($admin);

    Livewire::test(AdminDashboard::class)
        ->assertViewHas('viewsThisWeek', 3)
        ->assertViewHas('favoritesThisWeek', 1)
        ->assertViewHas('soldThisWeek', 1);
});

// ─── Public-facing view counts ───────────────────────────────────────────

test('showroom card displays the view count for a seeded unit', function () {
    $unit = Unit::factory()->create();
    UnitView::factory()->count(42)->create(['unit_id' => $unit->id]);

    Livewire::test(\App\Livewire\PublicShowroom::class)
        ->assertSee($unit->name)
        ->assertSee('42 views'); // eye-icon tooltip renders the exact count
});

test('unit detail page displays the view count including the current visit', function () {
    $unit = Unit::factory()->create();
    UnitView::factory()->count(9)->create(['unit_id' => $unit->id]);

    // The visit itself records a 10th view, which the page should include.
    $this->get(route('units.show', $unit))
        ->assertOk()
        ->assertSee('10 views');
});

// ─── Number formatting boundaries ────────────────────────────────────────

test('view count formatting is exact below 1000 and abbreviated from 1000 up', function () {
    $unit = Unit::factory()->create();

    $format = function (int $count) use ($unit): string {
        $unit->views_count = $count;

        return $unit->formattedViewCount();
    };

    expect($format(0))->toBe('0')
        ->and($format(999))->toBe('999')
        ->and($format(1000))->toBe('1K')
        ->and($format(1500))->toBe('1.5K')
        ->and($format(1549))->toBe('1.5K')
        ->and($format(12345))->toBe('12.3K')
        ->and($format(2_000_000))->toBe('2M');
});
