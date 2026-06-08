<?php

use App\Concerns\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

new class extends Component {
    use PasswordValidationRules;

    /**
     * Set the layout for the component.
     */
    public function rendering($view): void
    {
        $title = auth()->user()->isStaff() ? 'Credentials' : 'Security Settings';
        $view->layout('layouts.admin-panel', ['title' => $title]);
    }

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Password Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Update Password')" :subheading="__('Secure your registry access with a long, complex credential.')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-8">
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">{{ __('Current Password') }}</label>
                <input
                    wire:model="current_password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="admin-input !h-14 !bg-zinc-50/50 !text-zinc-900 font-bold"
                />
                @error('current_password') <p class="mt-2 text-xs font-bold text-red-600 uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">{{ __('New Password') }}</label>
                <input
                    wire:model="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="admin-input !h-14 !bg-zinc-50/50 !text-zinc-900 font-bold"
                />
                @error('password') <p class="mt-2 text-xs font-bold text-red-600 uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">{{ __('Confirm Registry Key') }}</label>
                <input
                    wire:model="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="admin-input !h-14 !bg-zinc-50/50 !text-zinc-900 font-bold"
                />
                @error('password_confirmation') <p class="mt-2 text-xs font-bold text-red-600 uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-6 pt-4">
                <button 
                    type="submit" 
                    class="admin-btn-primary min-w-[160px] !h-14 shadow-2xl shadow-zinc-200" 
                    data-test="update-password-button"
                >
                    <span wire:loading wire:target="updatePassword" class="h-4 w-4 animate-spin rounded-full border-2 border-white/20 border-t-white mr-2"></span>
                    {{ __('Apply Key Change') }}
                </button>

                <x-action-message class="text-emerald-600 font-black text-[10px] uppercase tracking-[0.2em]" on="password-updated">
                    {{ __('Credentials Secured') }}
                </x-action-message>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
