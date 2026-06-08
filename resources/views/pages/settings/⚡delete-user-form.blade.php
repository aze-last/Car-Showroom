<?php

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    use PasswordValidationRules;

    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="mt-10 space-y-8">
    <div class="relative">
        <h3 class="text-xl font-bold text-zinc-900 tracking-tight">{{ __('Delete Account') }}</h3>
        <p class="mt-2 text-sm font-medium text-zinc-400">{{ __('Permanently remove your access and registry data from the system.') }}</p>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <button 
            type="button"
            class="admin-btn-danger !h-14 px-8" 
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" 
            data-test="delete-user-button"
        >
            {{ __('Request Account Deletion') }}
        </button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg rounded-[32px] border-none shadow-2xl p-8">
        <form method="POST" wire:submit="deleteUser" class="space-y-8">
            <div class="space-y-4">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-red-600">{{ __('Critical Action') }}</h2>
                <p class="text-sm font-medium text-zinc-500 leading-relaxed">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently purged. Please enter your password to confirm.') }}
                </p>
            </div>

            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-zinc-400 mb-2">{{ __('Password') }}</label>
                <input 
                    wire:model="password" 
                    type="password" 
                    required
                    class="admin-input !h-14 !bg-zinc-50/50 !text-zinc-900 font-bold"
                />
                @error('password') <p class="mt-2 text-xs font-bold text-red-600 uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-4 pt-4">
                <flux:modal.close>
                    <button type="button" class="admin-btn-secondary !h-14">{{ __('Cancel') }}</button>
                </flux:modal.close>

                <flux:spacer />

                <button 
                    type="submit" 
                    class="admin-btn-danger !h-14 px-8" 
                    data-test="confirm-delete-user-button"
                >
                    {{ __('Confirm Deletion') }}
                </button>
            </div>
        </form>
    </flux:modal>
</section>
