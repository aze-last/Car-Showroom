<?php

use App\Livewire\PublicShowroom;
use App\Models\Unit;
use Livewire\Livewire;

it('can add units to compare', function () {
    $unit1 = Unit::factory()->create();
    $unit2 = Unit::factory()->create();

    Livewire::test(PublicShowroom::class)
        ->call('toggleCompare', $unit1->id)
        ->assertSet('compareIds', [$unit1->id])
        ->call('toggleCompare', $unit2->id)
        ->assertSet('compareIds', [$unit1->id, $unit2->id]);
});

it('can remove units from compare', function () {
    $unit1 = Unit::factory()->create();

    session()->put('compare_ids', [$unit1->id]);

    Livewire::test(PublicShowroom::class)
        ->call('toggleCompare', $unit1->id)
        ->assertSet('compareIds', []);
});

it('limits comparison to 3 units', function () {
    $units = Unit::factory()->count(4)->create();

    $component = Livewire::test(PublicShowroom::class);

    foreach ($units as $unit) {
        $component->call('toggleCompare', $unit->id);
    }

    expect($component->get('compareIds'))->toHaveCount(3);
});

it('can clear all comparisons', function () {
    $units = Unit::factory()->count(3)->create();

    Livewire::test(PublicShowroom::class)
        ->set('compareIds', $units->pluck('id')->toArray())
        ->call('clearCompare')
        ->assertSet('compareIds', []);
});

it('fetches selected units in order', function () {
    $unit1 = Unit::factory()->create(['name' => 'Car A']);
    $unit2 = Unit::factory()->create(['name' => 'Car B']);

    // Set the session directly to simulate comparison selection
    session()->put('compare_ids', [$unit2->id, $unit1->id]);

    Livewire::test(\App\Livewire\Public\ComparisonTray::class)
        ->assertCount('selectedUnits', 2)
        ->assertSee('Car B')
        ->assertSee('Car A');
});
