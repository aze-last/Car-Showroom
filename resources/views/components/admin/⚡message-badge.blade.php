<?php

use App\Models\ChatMessage;
use Livewire\Component;

new class extends Component
{
    public function getUnreadCountProperty(): int
    {
        return ChatMessage::query()
            ->where('is_from_admin', false)
            ->whereNull('read_at')
            ->count();
    }

    public function render()
    {
        return view('components.admin.⚡message-badge', [
            'count' => $this->unreadCount,
        ]);
    }
};
?>

<div wire:poll.10s>
    @if($count > 0)
        <span class="inline-flex items-center justify-center px-2 py-1 text-[9px] font-black leading-none text-white bg-red-600 rounded-full shadow-lg ring-2 ring-white">
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</div>
