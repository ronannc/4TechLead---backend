<?php

namespace Database\Factories;

use App\Models\DevelopmentPlan;
use App\Models\DevelopmentPlanItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DevelopmentPlanItem>
 */
class DevelopmentPlanItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'development_plan_id' => DevelopmentPlan::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'competency' => fake()->randomElement(['Autonomia', 'Comunicação', 'Qualidade técnica']),
            'evidence' => fake()->sentence(),
            'status' => fake()->randomElement(['todo', 'doing', 'done']),
            'due_date' => fake()->dateTimeBetween('now', '+3 months'),
            'completed_at' => null,
            'progress' => fake()->numberBetween(0, 100),
        ];
    }
}
