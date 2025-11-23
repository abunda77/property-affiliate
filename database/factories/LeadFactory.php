<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\User;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'affiliate_id' => User::factory(),
            'property_id' => Property::factory(),
            'name' => fake()->name(),
            'whatsapp' => fake()->numerify('08##########'),
            'status' => LeadStatus::NEW,
            'notes' => fake()->optional()->sentence(),
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
