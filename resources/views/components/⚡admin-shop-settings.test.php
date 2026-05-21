<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('admin-shop-settings')
        ->assertStatus(200);
});
