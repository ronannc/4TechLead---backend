<?php

namespace Database\Factories;

use App\Models\Okr;
use App\Models\OkrKeyResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OkrKeyResult>
 */
class OkrKeyResultFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'okr_id' => Okr::factory(),
            'title' => fake()->sentence(5),
            'metric_name' => fake()->randomElement(['PRs revisados', 'Ações concluídas', 'Feedbacks']),
            'data_source' => fake()->randomElement(['manual', 'pull_requests', 'tasks', 'dailies']),
            'initial_value' => 0,
            'current_value' => fake()->numberBetween(0, 5),
            'target_value' => fake()->numberBetween(6, 10),
            'unit' => 'itens',
            'confidence' => fake()->numberBetween(0, 100),
            'status' => fake()->randomElement(['todo', 'doing', 'done']),
            'due_date' => fake()->dateTimeBetween('now', '+3 months'),
            'evidence' => fake()->sentence(),
            'progress' => fake()->numberBetween(0, 100),
        ];
    }
}
