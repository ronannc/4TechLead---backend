<?php

namespace Database\Factories;

use App\Models\IntegrationSystem;
use App\Models\Person;
use App\Models\PersonExternalIdentity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PersonExternalIdentity>
 */
class PersonExternalIdentityFactory extends Factory
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
            'external_code' => fake()->userName(),
            'external_username' => fake()->userName(),
            'metadata' => ['source' => 'factory'],
            'active' => true,
        ];
    }
}
