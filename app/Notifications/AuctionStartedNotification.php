<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionStartedNotification extends Notification
{
    use Queueable;

    public function __construct(public Auction $auction) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Auction Live: ' . $this->auction->title)
            ->line('The auction for ' . ($this->auction->unit->name ?? 'a vehicle') . ' is now live!')
            ->action('View Auction', route('auction.room', $this->auction))
            ->line('Happy bidding!');
    }

    public function toArray($notifiable): array
    {
        return [
            'auction_id' => $this->auction->id,
            'title' => $this->auction->title,
            'message' => 'Auction is now LIVE!',
        ];
    }
}
