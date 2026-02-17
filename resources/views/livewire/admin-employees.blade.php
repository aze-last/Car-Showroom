<section class="space-y-6">
    <article class="admin-card">
        <div class="admin-card-header">
            <h2 class="text-base font-semibold text-slate-900">Create Employee Account</h2>
            <button type="submit" form="employee-create-form" wire:loading.attr="disabled" wire:target="create" class="admin-btn-primary">
                Create Account
            </button>
        </div>
        <div class="admin-card-body">
            <form id="employee-create-form" wire:submit="create" class="grid gap-4 lg:grid-cols-2">
                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Full Name</span>
                    <input type="text" wire:model="name" class="admin-input" placeholder="Employee name">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Email</span>
                    <input type="email" wire:model="email" class="admin-input" placeholder="employee@example.com">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Password</span>
                    <input type="password" wire:model="password" class="admin-input" placeholder="Minimum 8 characters">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Confirm Password</span>
                    <input type="password" wire:model="password_confirmation" class="admin-input" placeholder="Retype password">
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Job Title</span>
                    <input type="text" wire:model="job_title" class="admin-input" placeholder="Showroom Staff">
                    @error('job_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Phone</span>
                    <input type="text" wire:model="phone" class="admin-input" placeholder="+63...">
                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Default Locale</span>
                    <input type="text" wire:model="preferred_locale" class="admin-input" placeholder="en_PH">
                    @error('preferred_locale') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Default Timezone</span>
                    <input type="text" wire:model="preferred_timezone" class="admin-input" placeholder="Asia/Manila">
                    @error('preferred_timezone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </label>
            </form>
        </div>
    </article>

    <article class="admin-card overflow-hidden">
        <div class="admin-card-header">
            <h2 class="text-base font-semibold text-slate-900">Employees</h2>
            <p class="text-xs text-slate-500">Verified staff accounts allowed for QR status workflows.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-4 py-3 sm:px-5">Name</th>
                        <th scope="col" class="px-4 py-3">Email</th>
                        <th scope="col" class="px-4 py-3">Job Title</th>
                        <th scope="col" class="px-4 py-3">Locale</th>
                        <th scope="col" class="px-4 py-3">Timezone</th>
                        <th scope="col" class="px-4 py-3 sm:px-5">Created</th>
                        <th scope="col" class="px-4 py-3 text-right sm:px-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($employees as $employee)
                        <tr wire:key="employee-row-{{ $employee->id }}" class="odd:bg-white even:bg-slate-50/40 hover:bg-slate-100/70">
                            <td class="px-4 py-3 font-medium text-slate-900 sm:px-5">{{ $employee->name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $employee->email }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $employee->job_title }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $employee->preferred_locale }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $employee->preferred_timezone }}</td>
                            <td class="px-4 py-3 text-slate-600 sm:px-5">{{ $employee->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-right sm:px-5">
                                <button
                                    type="button"
                                    wire:click="delete({{ $employee->id }})"
                                    wire:confirm="Delete this employee account? This action cannot be undone."
                                    wire:loading.attr="disabled"
                                    wire:target="delete"
                                    class="inline-flex items-center rounded-md border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                No employee accounts yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    <div class="flex justify-end">
        {{ $employees->links() }}
    </div>
</section>
