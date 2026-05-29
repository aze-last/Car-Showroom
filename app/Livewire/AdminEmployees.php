<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class AdminEmployees extends Component
{
    use WithPagination;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $job_title = '';

    public string $phone = '';

    public string $preferred_locale = '';

    public string $preferred_timezone = '';

    public function mount(): void
    {
        Gate::authorize('access-admin');
        $this->applyDefaults();
    }

    public function create(): void
    {
        Gate::authorize('access-admin');

        $this->name = trim($this->name);
        $this->email = Str::lower(trim($this->email));
        $this->job_title = trim($this->job_title);
        $this->phone = trim($this->phone);
        $this->preferred_locale = trim($this->preferred_locale);
        $this->preferred_timezone = trim($this->preferred_timezone);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'job_title' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:32'],
            'preferred_locale' => ['required', 'string', 'max:10'],
            'preferred_timezone' => ['required', 'string', 'max:64'],
        ]);

        $employee = new User;
        $employee->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
            'is_admin' => false,
            'is_employee' => true,
            'job_title' => $validated['job_title'],
            'phone' => $validated['phone'] !== '' ? $validated['phone'] : null,
            'preferred_locale' => $validated['preferred_locale'],
            'preferred_timezone' => $validated['preferred_timezone'],
        ]);
        $employee->save();

        $this->dispatch('employee-created');

        $this->reset(['name', 'email', 'password', 'password_confirmation', 'phone']);
        $this->applyDefaults();
        $this->resetPage();

        session()->flash('status', 'Employee account created successfully.');
    }

    public function delete(int $employeeId): void
    {
        Gate::authorize('access-admin');

        $employee = User::query()
            ->where('is_employee', true)
            ->find($employeeId);

        if (! $employee instanceof User) {
            session()->flash('error', 'Employee account not found.');

            return;
        }

        $employee->delete();
        $this->resetPage();

        session()->flash('status', 'Employee account deleted successfully.');
    }

    private function applyDefaults(): void
    {
        /** @var array{job_title: string, preferred_locale: string, preferred_timezone: string} $defaults */
        $defaults = config('showroom.employee_defaults', []);

        $this->job_title = $defaults['job_title'] ?? 'Showroom Staff';
        $this->preferred_locale = $defaults['preferred_locale'] ?? 'en_PH';
        $this->preferred_timezone = $defaults['preferred_timezone'] ?? 'Asia/Manila';
    }

    public function render(): View
    {
        Gate::authorize('access-admin');

        return view('livewire.admin-employees', [
            'employees' => User::query()
                ->where('is_employee', true)
                ->orderByDesc('id')
                ->paginate(12),
        ])->layout('layouts.admin-panel', [
            'title' => 'Employee Accounts',
        ]);
    }
}
