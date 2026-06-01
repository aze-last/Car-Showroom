<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DepositApprovedNotification extends Notification
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
            'auction_id' => $this->data['auction_id'] ?? null,
            'unit_name' => $this->data['unit_name'] ?? null,
            'type' => 'deposit_approved',
        ];
    }
}
