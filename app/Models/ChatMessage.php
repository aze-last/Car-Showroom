<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'unit_id',
        'body',
        'is_from_admin',
        'is_automated',
        'read_at',
    ];

    protected $casts = [
        'is_from_admin' => 'boolean',
        'is_automated' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * The user who sent/received the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The unit this chat context belongs to.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
