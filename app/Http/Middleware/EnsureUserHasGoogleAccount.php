<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasGoogleAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        if ($user->isStaff() || $user->hasGoogleAccount()) {
            return $next($request);
        }

        session()->put('url.intended', $request->fullUrl());

        return redirect()
            ->route('auth.google.redirect')
            ->with('status', 'Sign in with Google to participate in auctions.');
    }
}
