<?php

namespace App\Livewire;

use App\Concerns\HandlesLivewireErrors;
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
    use HandlesLivewireErrors;

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

        // Mark messages as read — non-critical; failure shouldn't block viewing.
        $this->safely(fn () => ChatMessage::query()
            ->where('user_id', $userId)
            ->where('unit_id', $unitId)
            ->where('is_from_admin', false)
            ->whereNull('read_at')
            ->update(['read_at' => now()]), 'Could not mark messages as read.', [
                'thread_user_id' => $userId,
                'unit_id' => $unitId,
            ]);
    }

    public function sendReply(): void
    {
        if (! $this->selectedUserId || ! $this->selectedUnitId) {
            return;
        }

        $this->validate(['replyBody' => 'required|string|max:2000']);

        $created = $this->safely(fn () => ChatMessage::create([
            'user_id' => $this->selectedUserId,
            'unit_id' => $this->selectedUnitId,
            'body' => $this->replyBody,
            'is_from_admin' => true,
        ]), 'Could not send your reply. Please try again.', [
            'thread_user_id' => $this->selectedUserId,
            'unit_id' => $this->selectedUnitId,
        ]);

        if ($created === null) {
            return;
        }

        // Send notification — the reply is already saved, so a notification
        // failure is logged without discarding the message.
        $this->safely(function () {
            $user = User::find($this->selectedUserId);
            $unit = Unit::find($this->selectedUnitId);
            $user->notify(new AdminRepliedToInquiry($unit, Str::limit($this->replyBody, 50)));
        }, 'Reply sent, but the collector could not be notified.', [
            'thread_user_id' => $this->selectedUserId,
            'unit_id' => $this->selectedUnitId,
        ]);

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
                $user = User::find($thread->user_id);
                $unit = Unit::withTrashed()->find($thread->unit_id);

                if (! $user || ! $unit) {
                    return null;
                }

                return [
                    'user' => $user,
                    'unit' => $unit,
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
            })
            ->filter()
            ->values();
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
