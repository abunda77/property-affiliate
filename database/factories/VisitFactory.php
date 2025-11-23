<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'affiliate_id' => User::factory(),
            'property_id' => Property::factory(),
            'visitor_ip' => fake()->ipv4(),
            'device' => fake()->randomElement(['mobile', 'desktop']),
            'browser' => fake()->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
            'url' => fake()->url(),
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
