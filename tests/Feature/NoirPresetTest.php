<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Setting;
use App\Models\Unit;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('noir layout is registered as a design preset', function () {
    expect(config('showroom.design.layouts'))->toHaveKey('noir');
});

test('showroom renders noir preset when design layout is noir', function () {
    Setting::set('design_layout', 'noir');
    $category = Category::factory()->create();
    $unit = Unit::factory()->create(['category_id' => $category->id]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('theme-noir', false)
        ->assertSee('noir-stagger', false)
        ->assertSee($unit->name);
});

test('unit detail renders noir preset when design layout is noir', function () {
    Setting::set('design_layout', 'noir');
    $unit = Unit::factory()->create();

    $this->get(route('units.show', $unit))
        ->assertOk()
        ->assertSee('theme-noir-page', false)
        ->assertSee('Curator', false)
        ->assertSee($unit->name);
});

test('showroom still renders default cinema preset when noir is not selected', function () {
    Setting::set('design_layout', 'cinema');
    $unit = Unit::factory()->create();

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('noir-stagger', false)
        ->assertSee($unit->name);
});
