<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

function mockGoogleSocialiteUser(
    string $id = 'google-123',
    string $email = 'collector@example.com',
    string $name = 'Collector User',
    bool $emailVerified = true,
): SocialiteUser {
    $googleUser = Mockery::mock(SocialiteUser::class);
    $googleUser->shouldReceive('getId')->andReturn($id);
    $googleUser->shouldReceive('getName')->andReturn($name);
    $googleUser->shouldReceive('getEmail')->andReturn($email);
    $googleUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
    $googleUser->shouldReceive('getRaw')->andReturn(['email_verified' => $emailVerified]);

    Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($googleUser);

    return $googleUser;
}

test('google redirect route sends users to google', function () {
    Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
    Socialite::shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

    $this->get(route('auth.google.redirect'))
        ->assertRedirect('https://accounts.google.com/o/oauth2/auth');
});

test('google callback creates a verified collector account', function () {
    mockGoogleSocialiteUser();

    $this->get(route('auth.google.callback'))
        ->assertRedirect(route('dashboard', absolute: false));

    $user = User::query()->where('email', 'collector@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->google_id)->toBe('google-123')
        ->and($user->hasVerifiedEmail())->toBeTrue()
        ->and($user->hasGoogleAccount())->toBeTrue();

    $this->assertAuthenticatedAs($user);
});

test('google callback links an existing password account with the same email', function () {
    $user = User::factory()->create([
        'email' => 'collector@example.com',
        'google_id' => null,
    ]);

    mockGoogleSocialiteUser(email: 'collector@example.com');

    $this->get(route('auth.google.callback'))
        ->assertRedirect(route('dashboard', absolute: false));

    expect($user->fresh())
        ->google_id->toBe('google-123')
        ->and($user->fresh()->hasGoogleAccount())->toBeTrue();
});

test('google callback rejects unverified google emails', function () {
    mockGoogleSocialiteUser(emailVerified: false);

    $this->get(route('auth.google.callback'))
        ->assertRedirect(route('login'));

    expect(User::query()->where('email', 'collector@example.com')->exists())->toBeFalse();
    $this->assertGuest();
});

test('google callback links google to the currently authenticated user', function () {
    $user = User::factory()->create([
        'email' => 'collector@example.com',
        'google_id' => null,
    ]);

    $this->actingAs($user);

    mockGoogleSocialiteUser(email: 'collector@example.com');

    $this->get(route('auth.google.callback'))
        ->assertRedirect(route('dashboard', absolute: false));

    expect($user->fresh()->google_id)->toBe('google-123');
});
