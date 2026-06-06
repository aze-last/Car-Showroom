<?php

namespace App\Notifications;

use App\Models\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminRepliedToInquiry extends Notification
{
    use Queueable;

    public function __construct(
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
            'unit_id' => $this->unit->id,
            'unit_name' => $this->unit->name,
            'title' => 'New message from '.config('app.name'),
            'message' => 'A curator replied to your inquiry: "'.$this->messageSnippet.'"',
            'action_url' => route('units.show', $this->unit).'?expandChat=true',
        ];
    }
}
