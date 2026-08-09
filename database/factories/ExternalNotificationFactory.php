<?php

namespace Database\Factories;

use App\Models\ExternalNotification;
use App\Models\IntegrationSystem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExternalNotification>
 */
class ExternalNotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ExternalNotification>
     */
    protected $model = ExternalNotification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'integration_system_id' => IntegrationSystem::factory(),
            'event_id' => $this->faker->uuid(),
            'title' => $this->faker->sentence(4),
            'message' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['deploy', 'ci', 'task']),
            'severity' => $this->faker->randomElement(['info', 'success', 'warning', 'error']),
            'source_ref' => 'external#'.$this->faker->numberBetween(1, 100),
            'payload' => ['source' => 'factory'],
            'metadata' => ['environment' => 'test'],
            'received_at' => now(),
        ];
    }
}
