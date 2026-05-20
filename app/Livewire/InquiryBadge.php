<?php

namespace App\Livewire;

use App\Models\Inquiry;
use Livewire\Component;

class InquiryBadge extends Component
{
    public int $count = 0;

    public function placeholder()
    {
        return <<<'HTML'
            <div></div>
        HTML;
    }

    public function render()
    {
        $this->count = Inquiry::query()->count();

        return view('livewire.inquiry-badge');
    }
}
