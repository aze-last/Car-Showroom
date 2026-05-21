<?php

use App\Livewire\AdminShopSettings;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('can update shop settings', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    Livewire::actingAs($admin)
        ->test(AdminShopSettings::class)
        ->set('shop_name', 'New Shop Name')
        ->set('shop_phone', '09991234567')
        ->call('save')
        ->assertStatus(200);

    expect(Setting::get('shop_name'))->toBe('New Shop Name');
    expect(Setting::get('shop_phone'))->toBe('09991234567');
});

it('loads settings on mount', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Setting::set('shop_name', 'Existing Shop');

    Livewire::actingAs($admin)
        ->test(AdminShopSettings::class)
        ->assertSet('shop_name', 'Existing Shop');
});
