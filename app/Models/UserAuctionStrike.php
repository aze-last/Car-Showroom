<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAuctionStrike extends Model
{
    protected $fillable = [
        'user_id',
        'strike_count',
        'is_suspended',
        'suspended_until',
    ];

    protected $casts = [
        'is_suspended' => 'boolean',
        'suspended_until' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
