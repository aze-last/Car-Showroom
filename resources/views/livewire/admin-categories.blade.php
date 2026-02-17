<section class="space-y-6">
    @if (session('status'))
        <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <article class="admin-card">
        <div class="admin-card-header">
            <h2 class="text-base font-semibold text-slate-900">Add Category</h2>
            <button type="submit" form="category-create-form" wire:loading.attr="disabled" wire:target="create" class="admin-btn-primary">
                Add Category
            </button>
        </div>
        <div class="admin-card-body">
            <form id="category-create-form" wire:submit="create" class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Category Name</span>
                    <input type="text" wire:model="name" class="admin-input" placeholder="e.g. SUVs">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </label>
                <span wire:loading wire:target="create" class="text-xs text-slate-500">Saving...</span>
            </form>
        </div>
    </article>

    <article class="admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-4 py-3 sm:px-5">Category Name</th>
                        <th scope="col" class="px-4 py-3">Units Count</th>
                        <th scope="col" class="px-4 py-3">Created Date</th>
                        <th scope="col" class="px-4 py-3 text-right sm:px-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($categories as $category)
                        <tr wire:key="category-row-{{ $category->id }}" class="odd:bg-white even:bg-slate-50/40 hover:bg-slate-100/70">
                            <td class="px-4 py-3 sm:px-5">
                                @if ($editingCategoryId === $category->id)
                                    <div class="flex flex-wrap items-center gap-2">
                                        <input type="text" wire:model="editingName" class="admin-input max-w-xs">
                                        <button type="button" wire:click="update" wire:loading.attr="disabled" wire:target="update" class="admin-btn-primary px-3 py-1.5 text-xs">Save</button>
                                        <button type="button" wire:click="cancelEditing" class="admin-btn-secondary px-3 py-1.5 text-xs">Cancel</button>
                                    </div>
                                    @error('editingName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                @else
                                    <span class="font-medium text-slate-900">{{ $category->name }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ number_format($category->all_units_count) }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $category->created_at?->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-right sm:px-5">
                                @if ($editingCategoryId !== $category->id)
                                    <div class="inline-flex gap-2">
                                        <button type="button" wire:click="startEditing({{ $category->id }})" class="admin-btn-secondary px-3 py-1.5 text-xs">
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="delete({{ $category->id }})"
                                            wire:confirm="Delete this category?"
                                            @disabled($category->all_units_count > 0)
                                            class="admin-btn-secondary px-3 py-1.5 text-xs text-red-700 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">
                                No categories found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</section>
