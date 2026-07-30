<?php

namespace App\Models;

use App\Enums\AuthProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'auth_provider',
        'avatar',
        'terms_accepted_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'password' => 'hashed',
            'auth_provider' => AuthProvider::class,
            'is_admin' => 'boolean',
            'is_employee' => 'boolean',
            'is_blocked' => 'boolean',
        ];
    }

    public function isBlocked(): bool
    {
        return (bool) $this->is_blocked;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * @return HasMany<UnitStatusLog, $this>
     */
    public function unitStatusLogs(): HasMany
    {
        return $this->hasMany(UnitStatusLog::class);
    }

    public function isStaff(): bool
    {
        return $this->is_admin || $this->is_employee;
    }

    public function isOwner(): bool
    {
        return $this->is_admin && $this->job_title === 'Owner';
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isStaffOnly(): bool
    {
        return $this->is_employee && ! $this->is_admin;
    }

    /**
     * Scope a query to only include customer users (non-staff).
     *
     * @param  \Illuminate\Database\Eloquent\Builder<User>  $query
     * @return \Illuminate\Database\Eloquent\Builder<User>
     */
    public function scopeCustomers($query)
    {
        return $query->where('is_admin', false)->where('is_employee', false);
    }

    public function hasGoogleAccount(): bool
    {
        return $this->google_id !== null;
    }

    public function canParticipateInAuctions(): bool
    {
        if ($this->isBlocked()) {
            return false;
        }

        return $this->isStaff() || $this->hasGoogleAccount();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Unit, $this>
     */
    public function savedUnits(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'saved_units')->withTimestamps();
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function bidDeposits(): HasMany
    {
        return $this->hasMany(BidDeposit::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function auctionStrike(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserAuctionStrike::class);
    }
}
