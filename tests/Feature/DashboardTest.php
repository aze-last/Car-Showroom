<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('garage'));
});

test('admin users are redirected to the admin dashboard', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('admin.dashboard'));
});

test('employee users are redirected to the units workspace', function () {
    $employee = User::factory()->employee()->create();
    $this->actingAs($employee);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('admin.units.index'));
});
