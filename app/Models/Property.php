<?php

namespace App\Models;

use App\Enums\PropertyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Property extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'price',
        'location',
        'description',
        'features',
        'specs',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'features' => 'array',
            'specs' => 'json',
            'price' => 'integer',
            'status' => PropertyStatus::class,
        ];
    }

    /**
     * Get the leads for the property.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get the visits for the property.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * Scope a query to only include published properties.
     */
    public function scopePublished($query)
    {
        return $query->where('status', PropertyStatus::PUBLISHED);
    }

    /**
     * Scope a query to only include available properties (published and not sold).
     */
    public function scopeAvailable($query)
    {
        return $query->whereIn('status', [PropertyStatus::PUBLISHED]);
    }

    /**
     * Get the formatted price attribute.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useFallbackUrl('/images/placeholder.jpg')
            ->useFallbackPath(public_path('/images/placeholder.jpg'));
    }

    /**
     * Register media conversions.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->format('webp')
            ->quality(80)
            ->performOnCollections('images');

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(600)
            ->sharpen(10)
            ->format('webp')
            ->quality(85)
            ->performOnCollections('images');

        $this->addMediaConversion('large')
            ->width(1920)
            ->height(1080)
            ->sharpen(10)
            ->format('webp')
            ->quality(90)
            ->performOnCollections('images');

        // Keep original format conversions as well
        $this->addMediaConversion('thumb-jpg')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->quality(80)
            ->performOnCollections('images');

        $this->addMediaConversion('medium-jpg')
            ->width(800)
            ->height(600)
            ->sharpen(10)
            ->quality(85)
            ->performOnCollections('images');

        $this->addMediaConversion('large-jpg')
            ->width(1920)
            ->height(1080)
            ->sharpen(10)
            ->quality(90)
            ->performOnCollections('images');
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'location' => $this->location,
            'description' => $this->description,
        ];
    }
}
