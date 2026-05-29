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
        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 divide-y divide-slate-100 md:hidden">
            @forelse ($categories as $category)
                <div wire:key="category-card-{{ $category->id }}" class="p-6 bg-white space-y-4">
                    <div class="flex justify-between items-start">
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Category</p>
                            @if ($editingCategoryId === $category->id)
                                <div class="flex flex-col gap-2 mt-2">
                                    <input type="text" wire:model="editingName" class="admin-input">
                                    <div class="flex gap-2">
                                        <button type="button" wire:click="update" class="admin-btn-primary px-4 py-2 text-xs">Save</button>
                                        <button type="button" wire:click="cancelEditing" class="admin-btn-secondary px-4 py-2 text-xs">Cancel</button>
                                    </div>
                                </div>
                            @else
                                <h3 class="text-lg font-bold text-slate-900">{{ $category->name }}</h3>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Units</p>
                            <span class="inline-flex items-center justify-center bg-slate-100 text-slate-900 font-bold px-2 py-0.5 rounded text-xs">{{ number_format($category->all_units_count) }}</span>
                        </div>
                    </div>

                    @if ($editingCategoryId !== $category->id)
                        <div class="flex justify-between items-center pt-4 border-t border-slate-50">
                            <span class="text-[10px] text-slate-400 font-medium italic">Added {{ $category->created_at?->format('Y-m-d') }}</span>
                            <div class="flex gap-2">
                                <button type="button" wire:click="startEditing({{ $category->id }})" class="h-10 px-4 rounded-xl border border-slate-200 text-slate-600 font-bold text-[10px] uppercase tracking-widest">Edit</button>
                                <button
                                    type="button"
                                    wire:click="delete({{ $category->id }})"
                                    wire:confirm="Delete this category?"
                                    @disabled($category->all_units_count > 0)
                                    class="h-10 w-10 rounded-xl border border-red-100 bg-red-50 text-red-600 flex items-center justify-center disabled:opacity-30"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-10 text-center text-sm text-slate-500">No categories found.</div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
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
                    @foreach ($categories as $category)
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </article>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</section>
