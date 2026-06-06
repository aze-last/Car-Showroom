<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\AdminRepliedToInquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class AdminMessagesIndex extends Component
{
    public $selectedUserId = null;

    public $selectedUnitId = null;

    public $replyBody = '';

    public function mount(): void
    {
        if (request()->has('user_id') && request()->has('unit_id')) {
            $this->selectThread(request('user_id'), request('unit_id'));
        }
    }

    public function selectThread($userId, $unitId): void
    {
        $this->selectedUserId = $userId;
        $this->selectedUnitId = $unitId;

        // Mark messages as read
        ChatMessage::query()
            ->where('user_id', $userId)
            ->where('unit_id', $unitId)
            ->where('is_from_admin', false)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function sendReply(): void
    {
        if (! $this->selectedUserId || ! $this->selectedUnitId) {
            return;
        }

        $this->validate(['replyBody' => 'required|string|max:2000']);

        ChatMessage::create([
            'user_id' => $this->selectedUserId,
            'unit_id' => $this->selectedUnitId,
            'body' => $this->replyBody,
            'is_from_admin' => true,
        ]);

        // Send notification
        $user = User::find($this->selectedUserId);
        $unit = Unit::find($this->selectedUnitId);
        $user->notify(new AdminRepliedToInquiry($unit, Str::limit($this->replyBody, 50)));

        $this->replyBody = '';
    }

    public function getThreadsProperty()
    {
        return ChatMessage::query()
            ->select('user_id', 'unit_id', DB::raw('MAX(created_at) as last_msg'))
            ->groupBy('user_id', 'unit_id')
            ->orderBy('last_msg', 'desc')
            ->get()
            ->map(function ($thread) {
                return [
                    'user' => User::find($thread->user_id),
                    'unit' => Unit::find($thread->unit_id),
                    'last_message' => ChatMessage::query()
                        ->where('user_id', $thread->user_id)
                        ->where('unit_id', $thread->unit_id)
                        ->latest()
                        ->first(),
                    'unread_count' => ChatMessage::query()
                        ->where('user_id', $thread->user_id)
                        ->where('unit_id', $thread->unit_id)
                        ->where('is_from_admin', false)
                        ->whereNull('read_at')
                        ->count(),
                ];
            });
    }

    public function getMessagesProperty()
    {
        if (! $this->selectedUserId || ! $this->selectedUnitId) {
            return collect();
        }

        return ChatMessage::query()
            ->where('user_id', $this->selectedUserId)
            ->where('unit_id', $this->selectedUnitId)
            ->oldest()
            ->get();
    }

    public function render(): View
    {
        return view('livewire.admin-messages-index', [
            'threads' => $this->threads,
            'messages' => $this->messages,
        ])->layout('layouts.admin-panel', [
            'title' => 'Message Center',
        ]);
    }
}
