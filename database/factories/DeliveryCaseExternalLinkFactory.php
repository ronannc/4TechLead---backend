<?php

namespace Database\Factories;

use App\Enums\DeliveryExternalLinkType;
use App\Models\DeliveryCase;
use App\Models\DeliveryCaseExternalLink;
use App\Models\IntegrationSystem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryCaseExternalLink>
 */
class DeliveryCaseExternalLinkFactory extends Factory
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
            'integration_system_id' => fn (array $attributes): int => IntegrationSystem::factory()->create([
                'tenant_id' => $attributes['tenant_id'],
            ])->id,
            'link_type' => DeliveryExternalLinkType::GitHubPullRequest,
            'external_id' => fake()->unique()->bothify('acme/repository#?????'),
            'source_ref' => fake()->url(),
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
