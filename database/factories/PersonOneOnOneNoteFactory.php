<?php

namespace Database\Factories;

use App\Models\Person;
use App\Models\PersonOneOnOneNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PersonOneOnOneNote>
 */
class PersonOneOnOneNoteFactory extends Factory
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
            'created_by' => User::factory(),
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'status' => fake()->randomElement(['open', 'used', 'discarded']),
            'occurred_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
