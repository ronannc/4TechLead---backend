<?php

namespace Database\Factories;

use App\Enums\DeliveryAssociationConfidence;
use App\Enums\DeliveryParticipantRole;
use App\Models\DeliveryCase;
use App\Models\DeliveryCaseParticipant;
use App\Models\Person;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryCaseParticipant>
 */
class DeliveryCaseParticipantFactory extends Factory
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
            'person_id' => function (array $attributes): int {
                $team = Team::factory()->create(['tenant_id' => $attributes['tenant_id']]);

                return Person::factory()->create([
                    'tenant_id' => $attributes['tenant_id'],
                    'team_id' => $team->id,
                ])->id;
            },
            'role' => DeliveryParticipantRole::Implementer,
            'source_provider' => 'clickup',
            'source_ref' => fake()->bothify('task-########'),
            'confidence' => DeliveryAssociationConfidence::High,
            'valid_from' => fake()->dateTimeBetween('-30 days', 'now'),
            'valid_to' => null,
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
