<?php

namespace Tests\Feature;

use App\Livewire\AdminUnitForm;
use App\Livewire\PublicShowroom;
use App\Livewire\UnitDetail;
use App\Models\Category;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;

test('featured units appear first in showroom', function () {
    $category = Category::factory()->create();
    $normalUnit = Unit::factory()->create([
        'category_id' => $category->id,
        'is_featured' => false,
        'updated_at' => now()->subDay(),
    ]);
    $featuredUnit = Unit::factory()->create([
        'category_id' => $category->id,
        'is_featured' => true,
        'updated_at' => now()->subDays(2),
    ]);

    Livewire::test(PublicShowroom::class)
        ->assertSeeInOrder([
            $featuredUnit->name,
            $normalUnit->name,
        ]);
});

test('admin can toggle featured status', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $unit = Unit::factory()->create(['is_featured' => false]);

    Livewire::actingAs($admin)
        ->test(AdminUnitForm::class, ['unit' => $unit])
        ->set('is_featured', true)
        ->call('save');

    expect($unit->fresh()->is_featured)->toBeTrue();
});

test('guests can submit inquiries', function () {
    $unit = Unit::factory()->create();

    Livewire::test(UnitDetail::class, ['unit' => $unit])
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('phone', '09123456789')
        ->set('message', 'Is this still available?')
        ->call('submitInquiry')
        ->assertSet('submitted', true)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('inquiries', [
        'unit_id' => $unit->id,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'Is this still available?',
    ]);
});
