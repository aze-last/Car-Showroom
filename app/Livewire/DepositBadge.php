<?php

namespace App\Livewire;

use App\Models\BidDeposit;
use Livewire\Component;

class DepositBadge extends Component
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
        $this->count = BidDeposit::where('status', 'pending')->count();

        return view('livewire.deposit-badge');
    }
}
