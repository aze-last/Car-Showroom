<?php

namespace App\Services;

use App\Enums\AuthProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class GoogleAuthService
{
    public function resolveUser(SocialiteUser $googleUser): User
    {
        $googleId = $googleUser->getId();
        $email = strtolower((string) $googleUser->getEmail());

        if ($email === '' || ! $this->emailIsVerifiedByGoogle($googleUser)) {
            throw ValidationException::withMessages([
                'email' => 'Your Google account email must be verified before signing in.',
            ]);
        }

        $existingByGoogle = User::query()->where('google_id', $googleId)->first();
        if ($existingByGoogle) {
            $this->syncGoogleProfile($existingByGoogle, $googleUser);

            return $existingByGoogle;
        }

        if (Auth::check()) {
            return $this->linkGoogleToAuthenticatedUser(Auth::user(), $googleUser, $googleId, $email);
        }

        $existingByEmail = User::query()->where('email', $email)->first();
        if ($existingByEmail) {
            return $this->linkGoogleToExistingUser($existingByEmail, $googleUser, $googleId);
        }

        $user = User::query()->create([
            'name' => $googleUser->getName() ?: 'Collector',
            'email' => $email,
            'google_id' => $googleId,
            'auth_provider' => AuthProvider::Google,
            'avatar' => $googleUser->getAvatar(),
            'password' => null,
        ]);

        $user->markEmailAsVerified();

        return $user;
    }

    protected function linkGoogleToAuthenticatedUser(User $user, SocialiteUser $googleUser, string $googleId, string $email): User
    {
        if (strtolower($user->email) !== $email) {
            throw ValidationException::withMessages([
                'email' => 'The Google account email must match your signed-in account email.',
            ]);
        }

        if ($user->google_id !== null && $user->google_id !== $googleId) {
            throw ValidationException::withMessages([
                'email' => 'This account is already linked to a different Google profile.',
            ]);
        }

        $this->linkGoogleToExistingUser($user, $googleUser, $googleId);

        return $user->fresh();
    }

    protected function linkGoogleToExistingUser(User $user, SocialiteUser $googleUser, string $googleId): User
    {
        if ($user->google_id !== null && $user->google_id !== $googleId) {
            throw ValidationException::withMessages([
                'email' => 'This email is already linked to another Google account.',
            ]);
        }

        $user->forceFill([
            'google_id' => $googleId,
            'auth_provider' => $user->password ? AuthProvider::Password : AuthProvider::Google,
            'avatar' => $googleUser->getAvatar() ?: $user->avatar,
        ])->save();

        if ($user->email_verified_at === null) {
            $user->markEmailAsVerified();
        }

        return $user->fresh();
    }

    protected function syncGoogleProfile(User $user, SocialiteUser $googleUser): void
    {
        $user->forceFill([
            'name' => $googleUser->getName() ?: $user->name,
            'avatar' => $googleUser->getAvatar() ?: $user->avatar,
        ])->save();

        if ($user->email_verified_at === null) {
            $user->markEmailAsVerified();
        }
    }

    protected function emailIsVerifiedByGoogle(SocialiteUser $googleUser): bool
    {
        /** @var \Laravel\Socialite\Two\User $googleUser */
        $raw = $googleUser->getRaw();

        return (bool) ($raw['email_verified'] ?? false);
    }
}
