<?php

use App\Livewire\PublicShowroom;
use App\Livewire\UnitDetail;
use App\Models\Unit;
use App\Models\UnitView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ─── Recording views ─────────────────────────────────────────────────────

test('a view is recorded on first visit to a unit detail page', function () {
    $unit = Unit::factory()->create();

    $this->get(route('units.show', $unit))->assertOk();

    expect(UnitView::query()->count())->toBe(1);

    $view = UnitView::query()->first();
    expect($view->unit_id)->toBe($unit->id)
        ->and($view->user_id)->toBeNull()
        ->and($view->visitor_hash)->not->toBeEmpty()
        ->and($view->viewed_at)->not->toBeNull();
});

test('a second visit within 30 minutes does not create a duplicate row', function () {
    $unit = Unit::factory()->create();

    $this->get(route('units.show', $unit))->assertOk();
    $this->get(route('units.show', $unit))->assertOk();

    expect(UnitView::query()->count())->toBe(1);
});

test('a visit after the 30 minute window creates a new row', function () {
    $unit = Unit::factory()->create();

    $this->get(route('units.show', $unit))->assertOk();
    expect(UnitView::query()->count())->toBe(1);

    $this->travel(31)->minutes();

    $this->get(route('units.show', $unit))->assertOk();
    expect(UnitView::query()->count())->toBe(2);
});

test('views of different units by the same visitor are tracked separately', function () {
    $unitA = Unit::factory()->create();
    $unitB = Unit::factory()->create();

    $this->get(route('units.show', $unitA))->assertOk();
    $this->get(route('units.show', $unitB))->assertOk();

    expect(UnitView::query()->count())->toBe(2)
        ->and(UnitView::query()->where('unit_id', $unitA->id)->count())->toBe(1)
        ->and(UnitView::query()->where('unit_id', $unitB->id)->count())->toBe(1);
});

test('guest views are recorded with a null user_id', function () {
    $unit = Unit::factory()->create();

    $this->assertGuest();
    $this->get(route('units.show', $unit))->assertOk();

    $view = UnitView::query()->sole();
    expect($view->user_id)->toBeNull()
        ->and($view->visitor_hash)->not->toBeEmpty();
});

test('authenticated views are recorded with the user id', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user);
    $this->get(route('units.show', $unit))->assertOk();

    $view = UnitView::query()->sole();
    expect($view->user_id)->toBe($user->id);
});

test('an authenticated repeat visit within the window is deduplicated by user id', function () {
    $unit = Unit::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user);
    $this->get(route('units.show', $unit))->assertOk();
    $this->get(route('units.show', $unit))->assertOk();

    expect(UnitView::query()->count())->toBe(1);
});

// ─── Failure isolation ───────────────────────────────────────────────────

test('a view tracking failure does not prevent the unit page from rendering', function () {
    Log::spy();

    $unit = Unit::factory()->create();

    // Force the insert to fail (simulates a DB error).
    UnitView::creating(function () {
        throw new RuntimeException('Simulated database failure');
    });

    $this->get(route('units.show', $unit))
        ->assertOk()
        ->assertSee($unit->name);

    expect(UnitView::query()->count())->toBe(0);

    Log::shouldHaveReceived('error')->once();
});

// ─── Showroom sorting ────────────────────────────────────────────────────

test('most viewed sort returns units in descending view count order', function () {
    $leastViewed = Unit::factory()->create(['name' => 'Zeta Roadster Low']);
    $mostViewed = Unit::factory()->create(['name' => 'Alpha Coupe High']);
    $midViewed = Unit::factory()->create(['name' => 'Midway Sedan Mid']);

    UnitView::factory()->count(5)->create(['unit_id' => $mostViewed->id]);
    UnitView::factory()->count(3)->create(['unit_id' => $midViewed->id]);
    UnitView::factory()->count(1)->create(['unit_id' => $leastViewed->id]);

    Livewire::test(PublicShowroom::class)
        ->set('sortBy', 'most_viewed')
        ->assertSeeInOrder([
            $mostViewed->name,
            $midViewed->name,
            $leastViewed->name,
        ]);
});

test('most favorited sort returns units in descending saved count order', function () {
    $leastSaved = Unit::factory()->create(['name' => 'Rarely Saved Unit']);
    $mostSaved = Unit::factory()->create(['name' => 'Crowd Favorite Unit']);

    User::factory()->count(3)->create()->each(
        fn (User $user) => $user->savedUnits()->attach($mostSaved->id)
    );
    User::factory()->create()->savedUnits()->attach($leastSaved->id);

    Livewire::test(PublicShowroom::class)
        ->set('sortBy', 'most_favorited')
        ->assertSeeInOrder([
            $mostSaved->name,
            $leastSaved->name,
        ]);
});

// ─── Dashboard hook ──────────────────────────────────────────────────────

test('viewsPerDay returns daily counts including zero-count days', function () {
    $unit = Unit::factory()->create();

    UnitView::factory()->count(2)->create([
        'unit_id' => $unit->id,
        'viewed_at' => now(),
    ]);
    UnitView::factory()->create([
        'unit_id' => $unit->id,
        'viewed_at' => now()->subDays(2),
    ]);
    // Outside the window — must be excluded.
    UnitView::factory()->create([
        'unit_id' => $unit->id,
        'viewed_at' => now()->subDays(10),
    ]);

    $series = UnitView::viewsPerDay(7);

    expect($series)->toHaveCount(7)
        ->and($series->last()['date'])->toBe(now()->format('Y-m-d'))
        ->and($series->last()['count'])->toBe(2)
        ->and($series->firstWhere('date', now()->subDays(2)->format('Y-m-d'))['count'])->toBe(1)
        ->and($series->sum('count'))->toBe(3);
});

test('unit detail component records the view through livewire mount', function () {
    $unit = Unit::factory()->create();

    Livewire::test(UnitDetail::class, ['unit' => $unit])
        ->assertOk();

    expect(UnitView::query()->where('unit_id', $unit->id)->count())->toBe(1);
});
