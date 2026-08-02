<?php

namespace Database\Factories;

use App\Models\DevelopmentPlan;
use App\Models\Okr;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Okr>
 */
class OkrFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'development_plan_id' => DevelopmentPlan::factory(),
            'objective' => 'Evoluir em '.fake()->randomElement(['autonomia', 'arquitetura', 'qualidade']),
            'cycle' => '2026-Q3',
            'status' => fake()->randomElement(['draft', 'active', 'completed']),
            'focus_area' => fake()->randomElement(['Autonomia', 'Qualidade técnica', 'Comunicação']),
            'diagnosis' => fake()->paragraph(),
            'evidence_source' => fake()->sentence(),
            'baseline' => fake()->sentence(),
            'target' => fake()->sentence(),
            'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+3 months'),
            'confidence' => fake()->numberBetween(0, 100),
            'progress' => fake()->numberBetween(0, 100),
        ];
    }
}
