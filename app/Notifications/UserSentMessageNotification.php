<?php

namespace App\Notifications;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class UserSentMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $user,
        public Unit $unit,
        public string $messageSnippet
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'unit_id' => $this->unit->id,
            'unit_name' => $this->unit->name,
            'title' => 'New Message: ' . $this->unit->name,
            'message' => $this->user->name . ' sent an inquiry: "' . Str::limit($this->messageSnippet, 50) . '"',
            'action_url' => route('admin.messages') . '?user_id=' . $this->user->id . '&unit_id=' . $this->unit->id,
        ];
    }
}
