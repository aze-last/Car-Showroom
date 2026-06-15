<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Auth;

trait EnforcesCollectorAuthentication
{
    protected function redirectIfGuest(): bool
    {
        if (! Auth::check()) {
            $this->redirectRoute('login');

            return true;
        }

        return false;
    }

    protected function redirectIfUnverified(): bool
    {
        if (! Auth::user()->hasVerifiedEmail()) {
            $this->redirectRoute('verification.notice');

            return true;
        }

        return false;
    }

    protected function redirectIfGoogleRequiredForAuctions(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isStaff()) {
            return false;
        }

        if ($user->hasGoogleAccount()) {
            return false;
        }

        session()->put('url.intended', url()->current());
        $this->redirectRoute('auth.google.redirect');

        return true;
    }
}
