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
        ->set('terms', true)
        ->call('register')
        ->assertRedirect(route('home'));

    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->terms_accepted_at)->not->toBeNull();
    expect(auth()->check())->toBeTrue();
});

test('registration requires accepting the terms', function (): void {
    Livewire::test(Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('terms', false)
        ->call('register')
        ->assertHasErrors(['terms' => 'accepted']);

    expect(User::where('email', 'test@example.com')->exists())->toBeFalse();
    expect(auth()->check())->toBeFalse();
});

test('registration page shows the terms checkbox with policy links', function (): void {
    $this->get('/register')
        ->assertOk()
        ->assertSee('Terms of Service')
        ->assertSee('Privacy Policy')
        ->assertSee(route('terms'), false)
        ->assertSee(route('privacy'), false);
});
