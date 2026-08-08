<?php

namespace Database\Factories;

use App\Models\IntegrationSystem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IntegrationSystem>
 */
class IntegrationSystemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $token = fake()->sha256();

        return [
            'name' => fake()->company().' GitHub',
            'provider' => 'github',
            'description' => fake()->sentence(),
            'token_hash' => hash('sha256', $token),
            'token_prefix' => substr($token, 0, 8),
            'active' => true,
        ];
    }
}
