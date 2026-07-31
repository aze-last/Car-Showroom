<?php

use App\Http\Middleware\SetNoStoreCacheHeaders;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

test('sensitive admin and qr terminal routes have no_store middleware while public routes do not', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
        'is_employee' => true,
        'email_verified_at' => now(),
    ]);

    $unit = Unit::factory()->create([
        'status' => Unit::STATUS_AVAILABLE,
    ]);

    // Public showroom route should NOT have no_store middleware attached
    $publicRoute = Route::getRoutes()->match(app('request')->create('/'));
    expect($publicRoute->gatherMiddleware())->not->toContain('no_store');
    expect($publicRoute->gatherMiddleware())->not->toContain(SetNoStoreCacheHeaders::class);

    // Admin dashboard route MUST have no_store middleware attached
    $adminRoute = Route::getRoutes()->match(app('request')->create('/admin'));
    expect($adminRoute->gatherMiddleware())->toContain('no_store');

    // Deposit verification route MUST have no_store middleware attached
    $depositRoute = Route::getRoutes()->match(app('request')->create('/admin/deposits'));
    expect($depositRoute->gatherMiddleware())->toContain('no_store');

    // QR action route MUST have no_store middleware attached
    $qrRoute = Route::getRoutes()->match(app('request')->create("/admin/units/{$unit->public_id}/qr"));
    expect($qrRoute->gatherMiddleware())->toContain('no_store');

    // Verify response headers on admin routes explicitly contain no-store, no-cache, must-revalidate
    $adminResponse = $this->actingAs($admin)->get('/admin');
    $adminResponse->assertOk();
    $adminCacheHeader = (string) $adminResponse->headers->get('Cache-Control');
    expect($adminCacheHeader)->toContain('no-store');
    expect($adminCacheHeader)->toContain('no-cache');
    expect($adminCacheHeader)->toContain('must-revalidate');

    $signedQrUrl = URL::signedRoute('admin.units.qr', ['unit' => $unit]);
    $qrResponse = $this->actingAs($admin)->get($signedQrUrl);
    $qrResponse->assertOk();
    $qrCacheHeader = (string) $qrResponse->headers->get('Cache-Control');
    expect($qrCacheHeader)->toContain('no-store');
    expect($qrCacheHeader)->toContain('no-cache');
    expect($qrCacheHeader)->toContain('must-revalidate');
});

test('user can log out other browser sessions with correct current password', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
        'email_verified_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test('pages::settings.password')
        ->set('logout_password', 'wrong-password')
        ->call('logoutOtherDevices')
        ->assertHasErrors(['logout_password']);

    Livewire::actingAs($user)
        ->test('pages::settings.password')
        ->set('logout_password', 'password123')
        ->call('logoutOtherDevices')
        ->assertHasNoErrors()
        ->assertDispatched('logged-out-other-devices');
});

test('setting cache key is properly scoped per key', function () {
    \App\Models\Setting::set('showroom_theme', 'noir', 'string');
    expect(\App\Models\Setting::get('showroom_theme'))->toBe('noir');

    \App\Models\Setting::set('showroom_theme', 'cinema', 'string');
    expect(\App\Models\Setting::get('showroom_theme'))->toBe('cinema');
});
