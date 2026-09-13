<?php

namespace Database\Factories;

use App\Enums\DeliveryStage;
use App\Models\DeliveryCase;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryCase>
 */
class DeliveryCaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstSeenAt = fake()->dateTimeBetween('-90 days', '-1 day');

        return [
            'tenant_id' => Tenant::factory(),
            'task_ref' => mb_strtoupper(fake()->bothify('????-#####')),
            'title' => fake()->sentence(6),
            'current_stage' => DeliveryStage::Pending,
            'sprint_ref' => fake()->bothify('sprint-##'),
            'story_points' => fake()->randomElement([1, 2, 3, 5, 8, 13]),
            'first_seen_at' => $firstSeenAt,
            'last_activity_at' => fake()->dateTimeBetween($firstSeenAt, 'now'),
        ];
    }
}
