<?php

namespace App\Livewire;

use App\Models\Inquiry;
use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Session;
use Livewire\Component;

class UnitDetail extends Component
{
    public Unit $unit;

    public int $currentImageIndex = 0;

    #[Session(key: 'compare_ids')]
    public array $compareIds = [];

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $message = '';

    public bool $submitted = false;

    public function mount(Unit $unit): void
    {
        $this->unit = $unit->load([
            'category',
            'images',
        ]);

        if (! is_array($this->compareIds)) {
            $this->compareIds = [];
        }

        if (auth()->check()) {
            $user = auth()->user();
            $this->name = $user->name;
            $this->email = $user->email;
        }
    }

    public function toggleCompare(int $id): void
    {
        if (in_array($id, $this->compareIds)) {
            $this->compareIds = array_values(array_diff($this->compareIds, [$id]));
        } elseif (count($this->compareIds) < 3) {
            $this->compareIds[] = $id;
        }
    }

    public function submitInquiry(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // Rate limiting: 5 inquiries per minute per IP
        $rateLimitKey = 'inquiry:'.request()->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $this->addError('message', 'Too many requests. Please try again in a minute.');

            return;
        }
        \Illuminate\Support\Facades\RateLimiter::hit($rateLimitKey, 60);

        Inquiry::query()->create([
            'unit_id' => $this->unit->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'message' => $this->message,
        ]);

        $this->reset(['name', 'email', 'phone', 'message']);
        $this->submitted = true;
    }

    public function nextImage(): void
    {
        if ($this->unit->images->isEmpty()) {
            return;
        }

        $this->currentImageIndex = ($this->currentImageIndex + 1) % $this->unit->images->count();
    }

    public function previousImage(): void
    {
        if ($this->unit->images->isEmpty()) {
            return;
        }

        $this->currentImageIndex = ($this->currentImageIndex - 1 + $this->unit->images->count()) % $this->unit->images->count();
    }

    public function render(): View
    {
        $image = $this->unit->images->get($this->currentImageIndex);

        $similarUnits = Unit::query()
            ->with(['category', 'mainImage'])
            ->where('category_id', $this->unit->category_id)
            ->where('id', '!=', $this->unit->id)
            ->where('status', Unit::STATUS_AVAILABLE)
            ->latest('updated_at')
            ->take(3)
            ->get();

        return view('livewire.unit-detail', [
            'activeImage' => $image,
            'similarUnits' => $similarUnits,
        ])->layout('components.layouts.public-showroom', [
            'title' => $this->unit->name,
        ]);
    }
}
