<?php

namespace App\Livewire;

use App\Models\Auction;
use App\Models\Inquiry;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AdminGlobalSearch extends Component
{
    public bool $isOpen = false;

    public string $query = '';

    protected $listeners = ['openGlobalSearch' => 'open'];

    public function open(): void
    {
        $this->isOpen = true;
        $this->reset('query');
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->reset('query');
    }

    public function render(): View
    {
        $term = trim($this->query);
        $units = collect();
        $customers = collect();
        $auctions = collect();
        $employees = collect();
        $inquiries = collect();

        if (strlen($term) >= 2) {
            $wildcard = '%'.$term.'%';

            $units = Unit::query()
                ->where('name', 'like', $wildcard)
                ->orWhere('public_id', 'like', $wildcard)
                ->limit(5)
                ->get();

            $customers = User::query()
                ->customers()
                ->where(function ($q) use ($wildcard) {
                    $q->where('name', 'like', $wildcard)
                        ->orWhere('email', 'like', $wildcard);
                })
                ->limit(5)
                ->get();

            $auctions = Auction::query()
                ->with('unit')
                ->where(function ($q) use ($wildcard) {
                    $q->where('lot_number', 'like', $wildcard)
                        ->orWhereHas('unit', fn ($u) => $u->where('name', 'like', $wildcard));
                })
                ->limit(5)
                ->get();

            $employees = User::query()
                ->where(fn ($q) => $q->where('is_employee', true)->orWhere('is_admin', true))
                ->where(function ($q) use ($wildcard) {
                    $q->where('name', 'like', $wildcard)
                        ->orWhere('email', 'like', $wildcard);
                })
                ->limit(5)
                ->get();

            $inquiries = Inquiry::query()
                ->where(function ($q) use ($wildcard) {
                    $q->where('name', 'like', $wildcard)
                        ->orWhere('email', 'like', $wildcard)
                        ->orWhere('message', 'like', $wildcard);
                })
                ->limit(5)
                ->get();
        }

        return view('livewire.admin-global-search', [
            'units' => $units,
            'customers' => $customers,
            'auctions' => $auctions,
            'employees' => $employees,
            'inquiries' => $inquiries,
        ]);
    }
}
