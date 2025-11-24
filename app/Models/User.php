<?php

namespace App\Models;

use App\Enums\UserStatus;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'whatsapp',
        'affiliate_code',
        'status',
        'profile_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    /**
     * Get the visits for the affiliate.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'affiliate_id');
    }

    /**
     * Get the leads for the affiliate.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'affiliate_id');
    }

    /**
     * Scope a query to only include affiliates.
     */
    public function scopeAffiliates($query)
    {
        return $query->whereNotNull('affiliate_code');
    }

    /**
     * Scope a query to only include pending users.
     */
    public function scopePending($query)
    {
        return $query->where('status', UserStatus::PENDING);
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', UserStatus::ACTIVE);
    }

    /**
     * Generate a unique affiliate code.
     */
    public function generateAffiliateCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('affiliate_code', $code)->exists());

        $this->affiliate_code = $code;
        $this->save();

        return $code;
    }

    /**
     * Approve the user and activate their account.
     */
    public function approve(): void
    {
        $this->status = UserStatus::ACTIVE;

        if (! $this->affiliate_code) {
            $this->generateAffiliateCode();
        }

        $this->save();
    }

    /**
     * Block the user account.
     */
    public function block(): void
    {
        $this->status = UserStatus::BLOCKED;
        $this->save();
    }
}
