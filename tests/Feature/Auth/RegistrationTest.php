<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration screen is available', function (): void {
    $this->get('/register')->assertOk();
});

test('new users can register', function (): void {
    Livewire::test(Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register')
        ->assertRedirect(route('home'));

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
    expect(auth()->check())->toBeTrue();
});
