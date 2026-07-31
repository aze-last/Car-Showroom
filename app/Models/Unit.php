<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_AVAILABLE = 'AVAILABLE';

    public const STATUS_SOLD = 'SOLD';

    protected $fillable = [
        'category_id',
        'name',
        'price_php',
        'description',
        'status',
        'show_price',
        'is_featured',
        'year',
        'mileage',
        'transmission',
        'fuel_type',
        'source_name',
        'source_external_id',
        'source_url',
        'buyer_id',
        'guest_name',
        'guest_contact',
        'handover_image_path',
    ];

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'price_php' => 'integer',
            'show_price' => 'boolean',
            'is_featured' => 'boolean',
            'year' => 'integer',
            'mileage' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Unit $unit): void {
            if ($unit->public_id === null || $unit->public_id === '') {
                $unit->public_id = (string) Str::ulid();
            }

            if ($unit->status === null || $unit->status === '') {
                $unit->status = self::STATUS_AVAILABLE;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_AVAILABLE,
            self::STATUS_SOLD,
        ];
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isSold(): bool
    {
        return $this->status === self::STATUS_SOLD;
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * @return HasMany<UnitImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(UnitImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * @return HasOne<UnitImage, $this>
     */
    public function mainImage(): HasOne
    {
        return $this->hasOne(UnitImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * @return HasMany<UnitStatusLog, $this>
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(UnitStatusLog::class)
            ->latest();
    }

    /**
     * @return HasMany<Inquiry, $this>
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class)
            ->latest();
    }

    /**
     * @return HasMany<UnitView, $this>
     */
    public function views(): HasMany
    {
        return $this->hasMany(UnitView::class);
    }

    /**
     * Users who saved this unit to their garage (inverse of User::savedUnits()).
     *
     * @return BelongsToMany<User, $this>
     */
    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_units')->withTimestamps();
    }

    /**
     * Eager-load total view count as `views_count`.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Unit>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Unit>
     */
    public function scopeWithViewCount($query)
    {
        return $query->withCount('views');
    }

    public function formattedPrice(): string
    {
        if (! $this->show_price || $this->price_php === null) {
            return 'Price upon request';
        }

        return "\u{20B1}".number_format($this->price_php);
    }

    /**
     * Compact public-facing view count: exact below 1,000, otherwise
     * abbreviated with one decimal ("1.2K", "3.4M"). Uses the eager-loaded
     * `views_count` when present (withCount) to avoid extra queries.
     */
    public function formattedViewCount(): string
    {
        $count = (int) ($this->views_count ?? $this->views()->count());

        if ($count < 1000) {
            return (string) $count;
        }

        [$divisor, $suffix] = $count < 1_000_000 ? [1000, 'K'] : [1_000_000, 'M'];

        return rtrim(rtrim(sprintf('%.1f', round($count / $divisor, 1)), '0'), '.').$suffix;
    }

    public function markAsSold(): void
    {
        if ($this->isSold()) {
            throw new \Exception('Unit is already sold.');
        }

        $this->update(['status' => self::STATUS_SOLD]);
    }

    public function markAsAvailable(): void
    {
        $this->update(['status' => self::STATUS_AVAILABLE]);
    }

    public function signedQrUrl(): string
    {
        return URL::signedRoute('admin.units.qr', $this);
    }

    /**
     * Compute days in current AVAILABLE state using the most recent AVAILABLE status log,
     * or fallback to created_at if no such log exists.
     */
    public function daysInCurrentListing(): int
    {
        /** @var \App\Models\UnitStatusLog|null $latestAvailableLog */
        $latestAvailableLog = $this->statusLogs()
            ->where('to_status', self::STATUS_AVAILABLE)
            ->latest('created_at')
            ->first();

        $startDate = $latestAvailableLog ? $latestAvailableLog->created_at : $this->created_at;

        if (! $startDate) {
            return 0;
        }

        return (int) max(0, $startDate->diffInDays(now()));
    }

    public function isAuctionCandidate(): bool
    {
        return $this->isAvailable() && $this->daysInCurrentListing() >= 31;
    }

    /**
     * Evaluate auction readiness details and price suggestions for this unit.
     *
     * @return array<string, mixed>
     */
    public function auctionReadiness(): array
    {
        return app(\App\Services\AuctionReadinessService::class)->evaluate($this);
    }
}
