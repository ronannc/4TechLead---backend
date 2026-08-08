<?php

namespace Database\Factories;

use App\Models\IntegrationSystem;
use App\Models\Person;
use App\Models\PersonDeliveryMetric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PersonDeliveryMetric>
 */
class PersonDeliveryMetricFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'integration_system_id' => IntegrationSystem::factory(),
            'metric_type' => 'code_quality_score',
            'metric_value' => fake()->numberBetween(60, 100),
            'unit' => 'score',
            'source_ref' => fake()->bothify('repo#??'),
            'occurred_at' => now(),
            'metadata' => ['source' => 'factory'],
        ];
    }
}
