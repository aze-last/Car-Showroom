<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UnitView extends Model
{
    /** @use HasFactory<\Database\Factories\UnitViewFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'unit_id',
        'user_id',
        'visitor_hash',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
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

    /**
     * Scope views to the recent dedup window for a given visitor and unit.
     *
     * A returning visitor is matched by visitor_hash, or by user_id when
     * authenticated (so logging in mid-session doesn't double-count).
     *
     * @param  Builder<UnitView>  $query
     * @return Builder<UnitView>
     */
    public function scopeRecentFor(Builder $query, int $unitId, string $visitorHash, ?int $userId, int $minutes = 30): Builder
    {
        return $query
            ->where('unit_id', $unitId)
            ->where('viewed_at', '>=', now()->subMinutes($minutes))
            ->where(function (Builder $q) use ($visitorHash, $userId) {
                $q->where('visitor_hash', $visitorHash);

                if ($userId !== null) {
                    $q->orWhere('user_id', $userId);
                }
            });
    }

    /**
     * Daily view counts for the last N days (dashboard hook — data only).
     *
     * Returns ['date' => 'Y-m-d', 'count' => int] rows including zero-count
     * days, so a future chart gets a continuous series.
     *
     * @return Collection<int, array{date: string, count: int}>
     */
    public static function viewsPerDay(int $days = 30): Collection
    {
        $from = now()->subDays($days - 1)->startOfDay();

        // strftime on sqlite (tests); DATE_FORMAT on MySQL (dev/prod).
        $dateExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m-%d', viewed_at)"
            : "DATE_FORMAT(viewed_at, '%Y-%m-%d')";

        $counts = static::query()
            ->where('viewed_at', '>=', $from)
            ->selectRaw("{$dateExpression} as date, count(*) as count")
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        return collect(range(0, $days - 1))
            ->map(fn (int $offset) => $from->copy()->addDays($offset)->format('Y-m-d'))
            ->map(fn (string $date) => [
                'date' => $date,
                'count' => (int) ($counts[$date] ?? 0),
            ]);
    }
}
