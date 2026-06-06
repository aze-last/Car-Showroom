<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionWinnerNotification extends Notification
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
            ->subject('Congratulations! You Won the Auction')
            ->line('You are the highest bidder for '.($this->auction->unit->name ?? 'the vehicle').'.')
            ->line('Amount: ₱'.number_format($this->auction->current_bid_php))
            ->line('Please complete the payment within 48 hours.')
            ->action('View Payment Details', route('auction.room', $this->auction))
            ->line('Failure to pay within 48 hours will result in a strike and forfeiture of your deposit.');
    }

    public function toArray($notifiable): array
    {
        return [
            'auction_id' => $this->auction->id,
            'amount' => $this->auction->current_bid_php,
            'message' => 'Congratulations! You won the auction. Please pay within 48 hours.',
        ];
    }
}
