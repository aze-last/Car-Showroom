<?php

namespace App\Concerns;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

trait HandlesLivewireErrors
{
    /**
     * Run an action, converting unexpected failures into a logged error
     * plus a user-facing flash message instead of a 500 response.
     *
     * Validation, authorization, and HTTP (abort/404) exceptions are
     * rethrown so Livewire and Laravel handle them normally.
     *
     * Pass null as $userMessage to log silently (for poll-driven or
     * background actions where an error toast would be noise).
     *
     * @return mixed Returns the closure result, or null on failure.
     */
    protected function safely(Closure $action, ?string $userMessage = 'Something went wrong. Please try again.', array $context = []): mixed
    {
        try {
            return $action();
        } catch (ValidationException|AuthorizationException|ModelNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            if ($e instanceof HttpExceptionInterface) {
                throw $e;
            }

            Log::error('Livewire action failed: '.$e->getMessage(), array_merge([
                'component' => static::class,
                'user_id' => Auth::id(),
                'exception' => $e,
            ], $context));

            if ($userMessage !== null) {
                session()->flash('error', $userMessage);
                $this->dispatch('toast', message: $userMessage, type: 'error');
            }

            return null;
        }
    }
}
