<?php

namespace App\Models;

use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'affiliate_id',
        'property_id',
        'name',
        'whatsapp',
        'message',
        'status',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
        ];
    }

    /**
     * Get the affiliate that owns the lead.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    /**
     * Get the property that the lead is interested in.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Mark the lead as follow up.
     */
    public function markAsFollowUp(): void
    {
        $this->status = LeadStatus::FOLLOW_UP;
        $this->save();
    }

    /**
     * Mark the lead as survey.
     */
    public function markAsSurvey(): void
    {
        $this->status = LeadStatus::SURVEY;
        $this->save();
    }

    /**
     * Mark the lead as closed.
     */
    public function markAsClosed(): void
    {
        $this->status = LeadStatus::CLOSED;
        $this->save();
    }

    /**
     * Mark the lead as lost.
     */
    public function markAsLost(): void
    {
        $this->status = LeadStatus::LOST;
        $this->save();
    }
}
