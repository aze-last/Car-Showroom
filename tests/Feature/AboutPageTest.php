<?php

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can render the about page', function () {
    Setting::set('shop_name', 'Test Showroom');

    $response = $this->get(route('about'));

    $response->assertStatus(200);
    $response->assertSee('Curators');
});
