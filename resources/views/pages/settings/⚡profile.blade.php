<?php

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';

    /**
     * Set the layout for the component.
     */
    public function rendering($view): void
    {
        $title = auth()->user()->isStaff() ? 'Curator Profile' : 'Account Identity';
        $view->layout('layouts.admin-panel', ['title' => $title]);
    }

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-8">
            <flux:input 
                wire:model="name" 
                :label="__('Name')" 
                type="text" 
                required 
                autofocus 
                autocomplete="name" 
                class="admin-input !h-14 !bg-zinc-50/50 !text-zinc-900 font-bold"
            />

            <div>
                <flux:input 
                    wire:model="email" 
                    :label="__('Email')" 
                    type="email" 
                    required 
                    autocomplete="email" 
                    class="admin-input !h-14 !bg-zinc-50/50 !text-zinc-900 font-bold"
                />

                @if ($this->hasUnverifiedEmail)
                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-6 mt-6">
                        <flux:text class="text-amber-900 font-medium">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm font-black uppercase tracking-widest text-amber-600 underline underline-offset-4 cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Resend Link') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-4 text-xs font-black uppercase tracking-widest text-emerald-600">
                                {{ __('A new verification link has been sent.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-6 pt-4">
                <button 
                    type="submit" 
                    class="admin-btn-primary min-w-[160px] !h-14 shadow-2xl shadow-zinc-200" 
                    data-test="update-profile-button"
                >
                    <span wire:loading wire:target="updateProfileInformation" class="h-4 w-4 animate-spin rounded-full border-2 border-white/20 border-t-white mr-2"></span>
                    {{ __('Save Changes') }}
                </button>

                <x-action-message class="text-emerald-600 font-black text-[10px] uppercase tracking-[0.2em]" on="profile-updated">
                    {{ __('Registry Updated') }}
                </x-action-message>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <div class="mt-16 pt-12 border-t border-zinc-50">
                <livewire:pages::settings.delete-user-form />
            </div>
        @endif
    </x-pages::settings.layout>
</section>
