<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'title',
        'description',
        'unit_details',
        'lot_number',
        'is_featured',
        'start_at',
        'end_at',
        'reserve_price_php',
        'starting_bid_php',
        'current_bid_php',
        'min_bidders',
        'status',
        'winner_user_id',
        'fallback_user_id',
        'payment_deadline',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'payment_deadline' => 'datetime',
        'unit_details' => 'array',
        'reserve_price_php' => 'integer',
        'starting_bid_php' => 'integer',
        'current_bid_php' => 'integer',
        'is_featured' => 'boolean',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class)->latest();
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(BidDeposit::class);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    public function fallbackUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fallback_user_id');
    }

    public function isLive(): bool
    {
        return $this->status === 'live' || $this->status === 'active';
    }
}
