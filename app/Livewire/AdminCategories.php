<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

use Livewire\WithPagination;

class AdminCategories extends Component
{
    use WithPagination;

    public string $name = '';

    public ?int $editingCategoryId = null;

    public string $editingName = '';

    public function create(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        Category::query()->create($validated);
        $this->reset('name');

        session()->flash('status', 'Category created.');
    }

    public function startEditing(int $categoryId): void
    {
        $category = Category::query()->findOrFail($categoryId);

        $this->editingCategoryId = $category->id;
        $this->editingName = $category->name;
    }

    public function cancelEditing(): void
    {
        $this->reset(['editingCategoryId', 'editingName']);
    }

    public function update(): void
    {
        if ($this->editingCategoryId === null) {
            return;
        }

        $this->validate([
            'editingName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($this->editingCategoryId),
            ],
        ]);

        $category = Category::query()->findOrFail($this->editingCategoryId);
        $category->update(['name' => $this->editingName]);

        $this->reset(['editingCategoryId', 'editingName']);
        session()->flash('status', 'Category updated.');
    }

    public function delete(int $categoryId): void
    {
        $category = Category::query()->findOrFail($categoryId);

        $hasUnits = Unit::withTrashed()
            ->where('category_id', $category->id)
            ->exists();

        if ($hasUnits) {
            session()->flash('error', 'Category cannot be deleted because it has units.');

            return;
        }

        $category->delete();

        if ($this->editingCategoryId === $categoryId) {
            $this->cancelEditing();
        }

        session()->flash('status', 'Category deleted.');
    }

    public function render(): View
    {
        return view('livewire.admin-categories', [
            'categories' => Category::query()
                ->withCount('allUnits')
                ->orderBy('name')
                ->paginate(10),
        ])->layout('layouts.admin-panel', [
            'title' => 'Manage Categories',
        ]);
    }
}
