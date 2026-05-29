<?php

namespace App\Livewire;

use App\Models\Inquiry;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminInquiriesIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    public ?int $selectedInquiryId = null;

    public function mount(): void
    {
        // Select the latest inquiry by default if none selected
        if (! $this->selectedInquiryId) {
            $latest = Inquiry::query()->latest()->first();
            if ($latest) {
                $this->selectedInquiryId = $latest->id;
            }
        }
    }

    public function selectInquiry(int $id): void
    {
        $this->selectedInquiryId = $id;
    }

    public function unselectInquiry(): void
    {
        $this->selectedInquiryId = null;
    }

    public function setStatus(int $id, string $status): void
    {
        $inquiry = Inquiry::query()->findOrFail($id);
        $inquiry->update(['status' => $status]);

        session()->flash('status', 'Inquiry status updated to '.ucfirst($status));
    }

    public function delete(int $id): void
    {
        Inquiry::query()->findOrFail($id)->delete();

        if ($this->selectedInquiryId === $id) {
            $this->selectedInquiryId = Inquiry::query()->latest()->first()?->id;
        }

        session()->flash('status', 'Inquiry deleted.');
    }

    public function render(): View
    {
        $inquiries = Inquiry::query()
            ->with('unit')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('message', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate(20);

        $selectedInquiry = $this->selectedInquiryId
            ? Inquiry::query()->with(['unit', 'unit.mainImage'])->find($this->selectedInquiryId)
            : null;

        return view('livewire.admin-inquiries-index', [
            'inquiries' => $inquiries,
            'selectedInquiry' => $selectedInquiry,
        ])->layout('layouts.admin-panel', [
            'title' => 'Inquiry Management',
        ]);
    }
}
