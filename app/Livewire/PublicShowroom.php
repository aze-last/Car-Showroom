<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PublicShowroom extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'category', history: true)]
    public ?int $categoryId = null;

    #[Url(as: 'sort', history: true)]
    public string $sortBy = 'newest';

    #[Url(as: 'min', history: true)]
    public ?int $minPrice = null;

    #[Url(as: 'max', history: true)]
    public ?int $maxPrice = null;

    #[\Livewire\Attributes\Session(key: 'compare_ids')]
    public array $compareIds = [];

    public bool $showCompareModal = false;

    public function toggleCompare(int $id): void
    {
        if (in_array($id, $this->compareIds)) {
            $this->compareIds = array_values(array_diff($this->compareIds, [$id]));
        } elseif (count($this->compareIds) < 3) {
            $this->compareIds[] = $id;
        }
    }

    public function toggleSave(int $id)
    {
        if (! auth()->check()) {
            return redirect()->route('register');
        }

        $user = auth()->user();
        if ($user->savedUnits()->where('unit_id', $id)->exists()) {
            $user->savedUnits()->detach($id);
        } else {
            $user->savedUnits()->attach($id);
        }
    }

    public function clearCompare(): void
    {
        $this->compareIds = [];
    }

    #[\Livewire\Attributes\Computed]
    public function selectedUnits()
    {
        return Unit::query()
            ->with(['category', 'mainImage'])
            ->whereIn('id', $this->compareIds)
            ->get()
            ->sortBy(fn ($unit) => array_search($unit->id, $this->compareIds));
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function updatedMinPrice(): void
    {
        $this->resetPage();
    }

    public function updatedMaxPrice(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'categoryId', 'sortBy', 'minPrice', 'maxPrice']);
        $this->resetPage();
    }

    public function clearCategoryFilter(): void
    {
        $this->categoryId = null;
        $this->resetPage();
    }

    public function render(): View
    {
        $categories = Category::query()
            ->orderByRaw(
                'case name when ? then 1 when ? then 2 when ? then 3 when ? then 4 else 99 end',
                ['Motorcycle', 'Cars', 'Vans', 'Sportscars'],
            )
            ->orderBy('name')
            ->get();

        // 1. Featured Units for Slider (Unlimited)
        $featuredUnits = Unit::query()
            ->with(['category', 'mainImage'])
            ->where('status', Unit::STATUS_AVAILABLE)
            ->where('is_featured', true)
            ->latest()
            ->get();

        // 2. Main Catalog Units (All Available)
        $units = Unit::query()
            ->with(['category', 'mainImage'])
            ->where('status', Unit::STATUS_AVAILABLE)
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->when($this->categoryId !== null, fn ($q) => $q->where('category_id', $this->categoryId))
            ->when($this->minPrice !== null, fn ($q) => $q->where('price_php', '>=', $this->minPrice))
            ->when($this->maxPrice !== null, fn ($q) => $q->where('price_php', '<=', $this->maxPrice))
            ->when($this->sortBy === 'price_asc', fn ($q) => $q->orderBy('price_php', 'asc'))
            ->when($this->sortBy === 'price_desc', fn ($q) => $q->orderBy('price_php', 'desc'))
            ->when($this->sortBy === 'newest', fn ($q) => $q->orderByDesc('is_featured')->latest('updated_at'))
            ->paginate(12);

        // Fetch Design Tokens
        $designLayout = \App\Models\Setting::get('design_layout', 'cinema');
        $heroUnitId = \App\Models\Setting::get('design_hero_unit_id');

        // If a specific Hero Unit is set via customization, we use it as the first slide
        if ($heroUnitId) {
            $heroUnit = Unit::with(['category', 'mainImage'])->find($heroUnitId);
            if ($heroUnit && $heroUnit->isAvailable()) {
                $featuredUnits = collect([$heroUnit])->concat($featuredUnits->where('id', '!=', $heroUnitId))->unique('id');
            }
        }

        return view('livewire.public-showroom', [
            'categories' => $categories,
            'units' => $units,
            'featuredUnits' => $featuredUnits,
            'designLayout' => $designLayout,
            'designSettings' => [
                'headline' => \App\Models\Setting::get('design_hero_headline', 'Automotive Excellence'),
                'subtitle' => \App\Models\Setting::get('design_hero_subtitle', 'Curated collection.'),
                'showAuctions' => \App\Models\Setting::get('design_show_auctions', true),
                'showComparison' => \App\Models\Setting::get('design_show_comparison', true),
                'showInquiries' => \App\Models\Setting::get('design_show_inquiries', true),
            ],
        ])->layout('components.layouts.public-showroom', [
            'title' => 'Vehicle Showroom',
        ]);
    }
}
