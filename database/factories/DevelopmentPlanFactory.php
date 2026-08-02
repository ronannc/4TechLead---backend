<?php

namespace Database\Factories;

use App\Models\DevelopmentPlan;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DevelopmentPlan>
 */
class DevelopmentPlanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'title' => 'PDI - '.fake()->words(2, true),
            'summary' => fake()->paragraph(),
            'status' => fake()->randomElement(['draft', 'active', 'completed']),
            'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+6 months'),
            'target_role' => fake()->jobTitle(),
            'target_seniority' => fake()->randomElement(['junior', 'pleno', 'senior', 'especialista']),
            'progress' => fake()->numberBetween(0, 100),
        ];
    }
}
