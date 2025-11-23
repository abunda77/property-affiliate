<?php

namespace Database\Factories;

use App\Enums\PropertyStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(3);
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'price' => fake()->numberBetween(500000000, 5000000000),
            'location' => fake()->city() . ', ' . fake()->state(),
            'description' => fake()->paragraphs(3, true),
            'features' => [
                'Swimming Pool',
                'Garden',
                'Parking',
                'Security 24/7',
            ],
            'specs' => [
                'Bedrooms' => fake()->numberBetween(2, 5),
                'Bathrooms' => fake()->numberBetween(1, 3),
                'Land Size' => fake()->numberBetween(100, 500) . ' m²',
                'Building Size' => fake()->numberBetween(80, 400) . ' m²',
            ],
            'status' => PropertyStatus::PUBLISHED,
        ];
    }
}
