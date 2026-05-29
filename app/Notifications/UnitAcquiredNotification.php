<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UnitAcquiredNotification extends Notification
{
    use Queueable;

    public function __construct(public array $data) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->data['message'],
            'unit_id' => $this->data['unit_id'] ?? null,
            'unit_name' => $this->data['unit_name'] ?? null,
        ];
    }
}
