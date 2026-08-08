<?php

namespace Database\Factories;

use App\Models\IntegrationSystem;
use App\Models\IntegrationWebhookEvent;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IntegrationWebhookEvent>
 */
class IntegrationWebhookEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'integration_system_id' => IntegrationSystem::factory(),
            'person_id' => Person::factory(),
            'event_id' => fake()->uuid(),
            'event_type' => 'pull_request_merged',
            'external_actor_code' => fake()->userName(),
            'status' => 'processed',
            'payload' => ['pull_request' => ['number' => fake()->numberBetween(1, 300)]],
            'normalized_payload' => ['quality_score' => 90],
            'received_at' => now(),
        ];
    }
}
