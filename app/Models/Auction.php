<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends Model
{
    protected $fillable = [
        'unit_id',
        'lot_number',
        'start_at',
        'end_at',
        'reserve_price_php',
        'starting_bid_php',
        'current_bid_php',
        'status',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function highestBid(): BelongsTo
    {
        return $this->belongsTo(Bid::class, 'current_bid_id'); // We might want to store current_bid_id for performance
    }

    public function isLive(): bool
    {
        return $this->status === 'live' && now()->between($this->start_at, $this->end_at);
    }

    public function isReserveMet(): bool
    {
        return $this->current_bid_php >= $this->reserve_price_php;
    }
}
