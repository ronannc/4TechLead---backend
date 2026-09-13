<?php

namespace Database\Factories;

use App\Enums\DeliveryMilestoneType;
use App\Models\DeliveryCase;
use App\Models\DeliveryCaseMilestone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryCaseMilestone>
 */
class DeliveryCaseMilestoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'delivery_case_id' => DeliveryCase::factory(),
            'tenant_id' => fn (array $attributes): int => DeliveryCase::query()
                ->withoutGlobalScopes()
                ->findOrFail($attributes['delivery_case_id'])
                ->tenant_id,
            'milestone_type' => DeliveryMilestoneType::DevelopmentStarted,
            'semantic_key' => fake()->uuid(),
            'source_provider' => 'clickup',
            'source_ref' => fake()->bothify('task-########'),
            'occurred_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'metadata' => ['source' => 'factory'],
        ];
    }

    public function forDeliveryCase(DeliveryCase $deliveryCase): static
    {
        return $this->state(fn (): array => [
            'delivery_case_id' => $deliveryCase->id,
            'tenant_id' => $deliveryCase->tenant_id,
        ]);
    }
}
