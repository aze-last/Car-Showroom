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
        return false;
    }
}
