<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\GoogleAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback(GoogleAuthService $googleAuthService): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = $googleAuthService->resolveUser($googleUser);

            Auth::login($user, remember: true);

            return redirect()->intended(route('dashboard'));
        } catch (ValidationException $exception) {
            return redirect()
                ->route('login')
                ->withErrors($exception->errors());
        }
    }
}
