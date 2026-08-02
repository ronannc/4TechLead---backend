<?php

namespace Database\Factories;

use App\Models\OneOnOneSession;
use App\Models\OneOnOneTemplate;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OneOnOneSession>
 */
class OneOnOneSessionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'one_on_one_template_id' => OneOnOneTemplate::factory(),
            'scheduled_for' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'held_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'title' => '1:1 - '.fake()->date('d/m/Y'),
            'status' => fake()->randomElement(['draft', 'planned', 'completed']),
            'sentiment' => fake()->randomElement(['positive', 'neutral', 'attention']),
            'questions' => ['O que evoluiu?', 'O que precisa de apoio?'],
            'answers' => ['q1' => fake()->sentence(), 'q2' => fake()->sentence()],
            'notes' => fake()->paragraph(),
            'action_items' => [
                ['title' => fake()->sentence(4), 'done' => false],
            ],
        ];
    }
}
