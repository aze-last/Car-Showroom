<?php

namespace App\Livewire;

use App\Models\Inquiry;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AdminInquiriesIndex extends Component
{
    use WithPagination;

    public function markAsRead(int $id): void
    {
        Inquiry::query()->findOrFail($id)->update(['status' => 'read']);
    }

    public function markAsUnread(int $id): void
    {
        Inquiry::query()->findOrFail($id)->update(['status' => 'unread']);
    }

    public function delete(int $id): void
    {
        Inquiry::query()->findOrFail($id)->delete();
    }

    public function render(): View
    {
        $inquiries = Inquiry::query()
            ->with('unit')
            ->latest()
            ->paginate(15);

        return view('livewire.admin-inquiries-index', [
            'inquiries' => $inquiries,
        ])->layout('layouts.admin-panel', [
            'title' => 'Customer Inquiries',
        ]);
    }
}
