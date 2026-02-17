<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitStatusLog extends Model
{
    use HasFactory;

    public const ACTION_CREATE = 'CREATE';

    public const ACTION_UPDATE = 'UPDATE';

    public const ACTION_DELETE = 'DELETE';

    public const ACTION_RESTORE = 'RESTORE';

    public const ACTION_IMAGE_ADD = 'IMAGE_ADD';

    public const ACTION_IMAGE_REMOVE = 'IMAGE_REMOVE';

    public const ACTION_IMAGE_REORDER = 'IMAGE_REORDER';

    public const ACTION_SET_SOLD = 'SET_SOLD';

    public const ACTION_SET_AVAILABLE = 'SET_AVAILABLE';

    protected $fillable = [
        'unit_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'request_id',
        'reason',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'request_id' => 'string',
            'changes' => 'array',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function actions(): array
    {
        return [
            self::ACTION_CREATE,
            self::ACTION_UPDATE,
            self::ACTION_DELETE,
            self::ACTION_RESTORE,
            self::ACTION_IMAGE_ADD,
            self::ACTION_IMAGE_REMOVE,
            self::ACTION_IMAGE_REORDER,
            self::ACTION_SET_SOLD,
            self::ACTION_SET_AVAILABLE,
        ];
    }

    /**
     * @return BelongsTo<Unit, $this>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
