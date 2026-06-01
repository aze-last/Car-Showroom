<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DepositSubmittedNotification extends Notification
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
            'user_name' => $this->data['user_name'] ?? null,
            'amount' => $this->data['amount'] ?? null,
            'type' => 'deposit_submitted',
        ];
    }
}
