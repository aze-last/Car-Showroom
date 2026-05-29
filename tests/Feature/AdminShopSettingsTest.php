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
        ->set('dba_name', 'New Shop Name')
        ->set('sales_inquiry_email', 'new@example.com')
        ->call('save')
        ->assertStatus(200);

    expect(Setting::get('dba_name'))->toBe('New Shop Name');
    expect(Setting::get('sales_inquiry_email'))->toBe('new@example.com');
});

it('loads settings on mount', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Setting::set('dba_name', 'Existing Shop');

    Livewire::actingAs($admin)
        ->test(AdminShopSettings::class)
        ->assertSet('dba_name', 'Existing Shop');
});
