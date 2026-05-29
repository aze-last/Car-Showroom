<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use HasFactory;

    const STATUS_NEW = 'new';

    const STATUS_CONTACTED = 'contacted';

    const STATUS_NEGOTIATING = 'negotiating';

    const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'unit_id',
        'name',
        'email',
        'phone',
        'message',
        'status',
    ];

    /**
     * @return BelongsTo<Unit, $this>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
