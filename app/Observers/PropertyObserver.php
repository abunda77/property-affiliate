<?php

namespace App\Observers;

use App\Models\Property;
use Illuminate\Support\Str;

class PropertyObserver
{
    /**
     * Handle the Property "creating" event.
     */
    public function creating(Property $property): void
    {
        // Generate slug from title if not provided
        if (empty($property->slug) && !empty($property->title)) {
            $property->slug = $this->generateUniqueSlug($property->title);
        }
    }

    /**
     * Handle the Property "updating" event.
     */
    public function updating(Property $property): void
    {
        // If title changed and slug is empty or matches old title slug, regenerate
        if ($property->isDirty('title') && empty($property->slug)) {
            $property->slug = $this->generateUniqueSlug($property->title, $property->id);
        }
    }

    /**
     * Generate a unique slug from the given title.
     */
    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        // Check for uniqueness and add incremental suffix if needed
        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if a slug already exists in the database.
     */
    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = Property::where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
