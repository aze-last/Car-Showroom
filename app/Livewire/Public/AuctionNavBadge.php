<?php

namespace App\Livewire\Public;

use App\Models\Bid;
use Livewire\Component;

class AuctionNavBadge extends Component
{
    public int $count = 0;

    public function mount()
    {
        $this->refreshCount();
    }

    public function refreshCount()
    {
        if (auth()->check()) {
            // For logged in users, we could show unread notifications or new bids since last visit
            // For now, let's keep it simple: unread bid notifications
            $this->count = auth()->user()->unreadNotifications
                ->where('type', 'App\Notifications\BidPlacedNotification')
                ->count();
        } else {
            // For guests, show new bids in the last hour
            $this->count = Bid::where('created_at', '>=', now()->subHour())->count();
        }
    }

    public function render()
    {
        return <<<'HTML'
            <div>
                @if($count > 0)
                    <span class="absolute -top-1 -right-2 flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-red-600 text-[9px] font-bold text-white items-center justify-center">
                            {{ $count > 9 ? '9+' : $count }}
                        </span>
                    </span>
                @endif
            </div>
        HTML;
    }
}
